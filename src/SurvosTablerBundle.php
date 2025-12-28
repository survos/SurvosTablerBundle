<?php

namespace Survos\TablerBundle;

use Survos\TablerBundle\Components\AccordionComponent;
use Survos\TablerBundle\Components\AlertComponent;
use Survos\TablerBundle\Components\BadgeComponent;
use Survos\TablerBundle\Components\BrandComponent;
use Survos\TablerBundle\Components\ButtonComponent;
use Survos\TablerBundle\Components\CardComponent;
use Survos\TablerBundle\Components\CarouselComponent;
use Survos\TablerBundle\Components\DividerComponent;
use Survos\TablerBundle\Components\DropdownComponent;
use Survos\TablerBundle\Components\LinkComponent;
use Survos\TablerBundle\Components\LocaleSwitcherDropdown;
use Survos\TablerBundle\Components\MenuBreadcrumbComponent;
use Survos\TablerBundle\Components\MenuComponent;
use Survos\TablerBundle\Components\TablerFooter;
use Survos\TablerBundle\Components\TablerHeader;
use Survos\TablerBundle\Components\TablerPage;
use Survos\TablerBundle\Components\TabsComponent;
use Survos\TablerBundle\Menu\DemoMenu;
use Survos\TablerBundle\Menu\MenuSlot;
use Survos\TablerBundle\Service\ContextService;
use Survos\TablerBundle\Service\IconService;
use Survos\TablerBundle\Service\MenuDispatcher;
use Survos\TablerBundle\Service\MenuRenderer;
use Survos\TablerBundle\Service\MenuService;
use Survos\TablerBundle\Service\RouteAliasService;
use Survos\TablerBundle\Translation\RoutesTranslationLoader;
use Survos\TablerBundle\Twig\Components\MiniCard;
use Survos\TablerBundle\Twig\Components\TablerHead;
use Survos\TablerBundle\Twig\Components\TablerIcon;
use Survos\TablerBundle\Twig\Components\TablerPageHeader;
use Survos\TablerBundle\Twig\IconExtension;
use Survos\TablerBundle\Twig\MenuExtension;
use Survos\TablerBundle\Twig\MenuGlobalsExtension;
use Survos\TablerBundle\Twig\RouteAliasExtension;
use Survos\TablerBundle\Twig\TwigExtension;
use Survos\CoreBundle\HasAssetMapperInterface;
use Survos\CoreBundle\Traits\HasAssetMapperTrait;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class SurvosTablerBundle extends AbstractBundle implements CompilerPassInterface, HasAssetMapperInterface
{
    use HasAssetMapperTrait;

    public function build(ContainerBuilder $container): void
    {
        parent::build($container);
        $container->addCompilerPass($this);
    }

    public function process(ContainerBuilder $container): void
    {
        // Collect route security requirements from IsGranted attributes
        $routeRequirements = $this->collectRouteRequirements($container);
        $container->setParameter('survos_tabler.route_requirements', $routeRequirements);

        // Set up Twig globals
        $this->configureTwigGlobals($container);
    }

    private function collectRouteRequirements(ContainerBuilder $container): array
    {
        $requirements = [];
        $taggedServices = $container->findTaggedServiceIds('container.service_subscriber');

        foreach (array_keys($taggedServices) as $controllerClass) {
            if (!class_exists($controllerClass)) {
                continue;
            }

            $reflectionClass = new \ReflectionClass($controllerClass);
            
            // Controller-level IsGranted attributes
            $controllerRequirements = [];
            foreach ($reflectionClass->getAttributes(IsGranted::class) as $attribute) {
                $controllerRequirements = $attribute->getArguments();
            }

            // Method-level attributes
            foreach ($reflectionClass->getMethods() as $method) {
                $methodRequirements = [];
                foreach ($method->getAttributes(IsGranted::class) as $attribute) {
                    $methodRequirements = $attribute->getArguments();
                }

                // Get route name(s) and associate requirements
                foreach ($method->getAttributes(Route::class) as $attribute) {
                    $args = $attribute->getArguments();
                    $routeName = $args['name'] ?? $method->getName();
                    $requirements[$routeName] = array_merge($methodRequirements, $controllerRequirements);
                }
            }
        }

        return $requirements;
    }

    private function configureTwigGlobals(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition('twig')) {
            return;
        }

        $twigDef = $container->getDefinition('twig');

        // MenuSlot constants (legacy support for old templates)
        $menuSlotReflection = new \ReflectionEnum(MenuSlot::class);
        foreach ($menuSlotReflection->getConstants() as $name => $value) {
            if (is_string($value)) {
                $twigDef->addMethodCall('addGlobal', [$name, $value]);
            }
        }

        // Theme from config
        if ($container->hasParameter('survos_tabler.theme')) {
            $twigDef->addMethodCall('addGlobal', ['theme', $container->getParameter('survos_tabler.theme')]);
        }
    }

    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        // === Parameters ===
        $builder->setParameter('survos_tabler.config', $config);
        $builder->setParameter('survos_tabler.routes', $config['routes']);
        $builder->setParameter('survos_tabler.theme', $config['options']['theme']);
        $builder->setParameter('survos_tabler.route_requirements', []); // Populated in compiler pass

        // === Core Services ===

        $builder->register(MenuDispatcher::class)
            ->setArgument('$factory', new Reference('knp_menu.factory'))
            ->setArgument('$dispatcher', new Reference('event_dispatcher'));

        $builder->register(MenuRenderer::class)
            ->setArgument('$dispatcher', new Reference(MenuDispatcher::class))
            ->setArgument('$knpHelper', new Reference('knp_menu.helper'))
            ->setArgument('$requestStack', new Reference('request_stack'))
            ->setArgument('$templatePrefix', '@SurvosTabler/menu/');

        $iconConfig = $config['icons'] ?? [];
        $builder->register(IconService::class)
            ->setArgument('$configuredAliases', $iconConfig['aliases'] ?? [])
            ->setArgument('$configuredPresets', $iconConfig['presets'] ?? [])
            ->setArgument('$defaultPrefix', $iconConfig['prefix'] ?? 'tabler');

        $builder->register(RouteAliasService::class)
            ->setArgument('$configuredAliases', $config['routes'])
            ->setArgument('$router', new Reference('router'));

        $builder->register(ContextService::class)
            ->setAutowired(true)
            ->setArgument('$config', $config)
            ->setArgument('$options', $config['options']);

        $builder->register(MenuService::class)
            ->setAutowired(true)
            ->setArgument('$routeRequirements', '%survos_tabler.route_requirements%')
            ->setArgument('$impersonateUrlGenerator', new Reference('security.impersonate_url_generator', ContainerInterface::NULL_ON_INVALID_REFERENCE))
            ->setArgument('$authorizationChecker', new Reference('security.authorization_checker', ContainerInterface::NULL_ON_INVALID_REFERENCE))
            ->setArgument('$usersToImpersonate', $config['impersonate'])
            ->setArgument('$security', new Reference('security.helper', ContainerInterface::NULL_ON_INVALID_REFERENCE));

        // === Twig Extensions ===

        $builder->register(MenuExtension::class)
            ->setArgument('$renderer', new Reference(MenuRenderer::class))
            ->addTag('twig.extension');

        $builder->register(MenuGlobalsExtension::class)
            ->addTag('twig.extension');

        $builder->register(IconExtension::class)
            ->setArgument('$iconService', new Reference(IconService::class))
            ->addTag('twig.extension');

        $builder->register(RouteAliasExtension::class)
            ->setArgument('$routeAliasService', new Reference(RouteAliasService::class))
            ->addTag('twig.extension');

        $builder->autowire('survos.tabler_twig', TwigExtension::class)
            ->setArgument('$config', $config)
            ->setArgument('$routes', $config['routes'])
            ->setArgument('$options', $config['options'])
            ->setArgument('$contextService', new Reference(ContextService::class))
            ->addTag('twig.extension');

        // === Menu Demo Listener ===

        $builder->register(DemoMenu::class)
            ->setAutowired(true)
            ->setAutoconfigured(true)
            ->setArgument('$requestStack', new Reference('request_stack'));

        // === Twig Components ===

        $simpleComponents = [
            AlertComponent::class,
            AccordionComponent::class,
            BrandComponent::class,
            BadgeComponent::class,
            ButtonComponent::class,
            CardComponent::class,
            CarouselComponent::class,
            DropdownComponent::class,
            DividerComponent::class,
            LinkComponent::class,
            TabsComponent::class,
            TablerPage::class,
            LocaleSwitcherDropdown::class,
            TablerHeader::class,
            TablerFooter::class,
            MiniCard::class,
            TablerIcon::class,
            TablerHead::class,
            TablerPageHeader::class,
        ];

        foreach ($simpleComponents as $componentClass) {
            $builder->register($componentClass)
                ->setAutowired(true)
                ->setAutoconfigured(true);
        }

        // Menu components need extra arguments
        foreach ([MenuComponent::class, MenuBreadcrumbComponent::class] as $componentClass) {
            $builder->register($componentClass)
                ->setAutowired(true)
                ->setAutoconfigured(true)
                ->setArgument('$menuOptions', $config['menu_options'])
                ->setArgument('$helper', new Reference('knp_menu.helper'))
                ->setArgument('$factory', new Reference('knp_menu.factory'))
                ->setArgument('$eventDispatcher', new Reference('event_dispatcher'));
        }

        // === Translation Loader ===

        $builder->autowire('survos.tabler_translations', RoutesTranslationLoader::class)
            ->setAutowired(true)
            ->setAutoconfigured(true)
            ->addTag('translation.loader', ['alias' => 'bin']);
    }

    public function configure(DefinitionConfigurator $definition): void
    {
        $definition->rootNode()
            ->children()
                ->append($this->getIconsConfig())
                ->append($this->getAppConfig())
                ->append($this->getRoutesConfig())
                ->append($this->getOptionsConfig())
                ->arrayNode('menu_options')
                    ->useAttributeAsKey('name')
                    ->scalarPrototype()->end()
                ->end()
                ->arrayNode('impersonate')
                    ->useAttributeAsKey('name')
                    ->scalarPrototype()->end()
                    ->info('User identifiers that can be impersonated')
                ->end()
            ->end();
    }

    private function getIconsConfig(): ArrayNodeDefinition
    {
        $treeBuilder = new TreeBuilder('icons');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('prefix')
                    ->defaultValue('tabler')
                    ->info('Default icon prefix (e.g., tabler, fa6-solid)')
                ->end()
                ->arrayNode('aliases')
                    ->useAttributeAsKey('name')
                    ->scalarPrototype()->end()
                    ->info('Icon alias overrides: alias: icon-name')
                ->end()
                ->arrayNode('presets')
                    ->useAttributeAsKey('name')
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('icon')->isRequired()->end()
                            ->scalarNode('class')->defaultValue('')->end()
                        ->end()
                    ->end()
                    ->info('Style presets with icon and CSS class')
                ->end()
            ->end();

        return $rootNode;
    }

    private function getAppConfig(): ArrayNodeDefinition
    {
        $treeBuilder = new TreeBuilder('app');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->arrayNode('social')
                    ->useAttributeAsKey('name')
                    ->scalarPrototype()->end()
                    ->info('Social media links (facebook: https://...)')
                ->end()
                ->scalarNode('code')
                    ->defaultValue('my-project')
                    ->info('Project code for repo, deployment, etc.')
                ->end()
                ->scalarNode('abbr')
                    ->defaultValue('my<b>Project</b>')
                    ->info('HTML abbreviation for branding')
                ->end()
                ->scalarNode('logo')
                    ->defaultNull()
                    ->info('Path to main logo')
                ->end()
                ->scalarNode('logo_small')
                    ->defaultNull()
                    ->info('Path to small/favicon logo')
                ->end()
            ->end();

        return $rootNode;
    }

    private function getRoutesConfig(): ArrayNodeDefinition
    {
        $treeBuilder = new TreeBuilder('routes');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->addDefaultsIfNotSet()
            ->info('Route aliases - use null/~ for routes that do not exist')
            ->children()
                ->scalarNode('home')
                    ->defaultValue('app_homepage')
                    ->info('Homepage route')
                ->end()
                ->scalarNode('login')
                    ->defaultNull()
                    ->info('Login route')
                ->end()
                ->scalarNode('logout')
                    ->defaultNull()
                    ->info('Logout route')
                ->end()
                ->scalarNode('register')
                    ->defaultNull()
                    ->info('Registration route')
                ->end()
                ->scalarNode('profile')
                    ->defaultNull()
                    ->info('User profile route')
                ->end()
                ->scalarNode('settings')
                    ->defaultNull()
                    ->info('User settings route')
                ->end()
                ->scalarNode('search')
                    ->defaultNull()
                    ->info('Global search route')
                ->end()
            ->end();

        return $rootNode;
    }

    private function getOptionsConfig(): ArrayNodeDefinition
    {
        $treeBuilder = new TreeBuilder('options');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('theme')
                    ->defaultValue('tabler')
                    ->info('Theme name')
                ->end()
                ->enumNode('layout')
                    ->values(['horizontal', 'vertical', 'condensed'])
                    ->defaultValue('horizontal')
                    ->info('Layout direction')
                ->end()
                ->booleanNode('dark_mode')
                    ->defaultFalse()
                    ->info('Enable dark mode by default')
                ->end()
                ->booleanNode('show_locale_dropdown')
                    ->defaultFalse()
                    ->info('Show locale switcher in navbar')
                ->end()
            ->end();

        return $rootNode;
    }

    public function getPaths(): array
    {
        $dir = realpath(__DIR__ . '/../assets/');
        assert(file_exists($dir), 'asset path must exist for the assets in ' . __DIR__);
        return [$dir => '@survos/tabler'];
    }
}
