<?php
namespace Survos\TablerBundle;

use Survos\BaseBundle\DependencyInjection\Compiler\SurvosBaseCompilerPass;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class SurvosTablerBundle extends AbstractBundle
{
    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        // autowire the tabler components
//        $builder->autowire(::class)
//            ->addTag('maker.command')
//            ->addArgument(new Reference('maker.doctrine_helper'))
//            ->addArgument($config['template_path'])
//        ;

    }


    public function configure(DefinitionConfigurator $definition): void
    {
        $definition->rootNode()
            ->children()
            ->scalarNode('extends')->defaultValue('base.html.twig')->end()
            ->end();


    }


}
