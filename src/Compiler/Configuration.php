<?php
/* src/Compiler/Configuration.php v1.0 */

declare(strict_types=1);

namespace Survos\TablerBundle\Compiler;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;

final class Configuration
{
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

    /**
     * Site identity + header defaults.
     *
     * Goal: keep keys stable so tenant overrides can simply overlay the same structure.
     */
    private function getAppConfig(): ArrayNodeDefinition
    {
        $treeBuilder = new TreeBuilder('app');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->addDefaultsIfNotSet()
            ->children()

                ->scalarNode('code')
                    ->defaultValue('my-project')
                    ->info('Project code for repo, deployment, etc.')
                ->end()

                ->scalarNode('title')
                    ->defaultValue('My Project')
                    ->info('Human-readable site title')
                ->end()

                ->scalarNode('description')
                    ->defaultValue('')
                    ->info('Short description used for meta tags and sharing cards')
                ->end()

                ->scalarNode('abbr')
                    ->defaultValue('my<b>Project</b>')
                    ->info('HTML abbreviation for branding (rendered as safe HTML)')
                ->end()

                ->scalarNode('logo')
                    ->defaultNull()
                    ->info('Path/URL to main logo')
                ->end()

                ->scalarNode('logo_small')
                    ->defaultNull()
                    ->info('Path/URL to small/favicon logo')
                ->end()

                ->scalarNode('homepage_route')
                    ->defaultNull()
                    ->info('Route name for brand link (preferred for Symfony apps)')
                ->end()

                ->scalarNode('homepage_url')
                    ->defaultNull()
                    ->info('Absolute/relative URL for brand link (useful for demo sites)')
                ->end()

                ->arrayNode('links')
                    ->addDefaultsIfNotSet()
                    ->info('Common external links used to seed header/footer menus (github/docs/sponsor/site/etc.)')
                    ->children()
                        ->scalarNode('github')->defaultNull()->end()
                        ->scalarNode('docs')->defaultNull()->end()
                        ->scalarNode('sponsor')->defaultNull()->end()
                        ->scalarNode('site')->defaultNull()->end()
                        ->scalarNode('contact')->defaultNull()->end()
                    ->end()
                ->end()

                ->arrayNode('social')
                    ->useAttributeAsKey('name')
                    ->scalarPrototype()->end()
                    ->info('Social media links. Prefer full URLs for trivial rendering.')
                ->end()

                ->arrayNode('meta')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('og_image')->defaultNull()->info('OpenGraph image URL/path')->end()
                        ->scalarNode('twitter_site')->defaultNull()->info('Twitter/X site handle, e.g. @tabler_io')->end()
                        ->scalarNode('theme_color')->defaultNull()->info('Brand theme color hex, e.g. #066fd1')->end()
                    ->end()
                ->end()

                ->arrayNode('header')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('locale_switcher')
                            ->defaultTrue()
                            ->info('Show locale switcher in the top navbar')
                        ->end()

                        ->arrayNode('auth')
                            ->addDefaultsIfNotSet()
                            ->info('Default behavior for AUTH slot (top navbar user/login area)')
                            ->children()
                                ->booleanNode('enabled')->defaultTrue()->end()
                                ->booleanNode('show_login')->defaultTrue()->end()
                                ->booleanNode('show_user_menu')->defaultTrue()->end()

                                ->arrayNode('routes')
                                    ->addDefaultsIfNotSet()
                                    ->info('Common auth routes; missing routes should be ignored safely by renderers')
                                    ->children()
                                        ->scalarNode('login')->defaultValue('app_login')->end()
                                        ->scalarNode('logout')->defaultValue('app_logout')->end()
                                        ->scalarNode('register')->defaultValue('app_register')->end()
                                        ->scalarNode('profile')->defaultValue('app_profile')->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()

            ->end();

        return $rootNode;
    }

    /**
     * Route aliases (general).
     *
     * Note: keep these for existing RouteAliasService consumers.
     * Auth routes also exist under app.header.auth.routes as "defaults for AUTH slot".
     */
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
                    ->info('Theme name (informational; actual CSS is up to the app)')
                ->end()
                ->enumNode('layout')
                    ->values(['horizontal', 'dashboard', 'vertical', 'condensed'])
                    ->defaultValue('horizontal')
                    ->info('Layout direction')
                ->end()
                ->booleanNode('dark_mode')
                    ->defaultFalse()
                    ->info('Enable dark mode by default')
                ->end()
                ->booleanNode('show_locale_dropdown')
                    ->defaultTrue()
                    ->info('Legacy: show locale dropdown in navbar (prefer app.header.locale_switcher)')
                ->end()
            ->end();

        return $rootNode;
    }
}
