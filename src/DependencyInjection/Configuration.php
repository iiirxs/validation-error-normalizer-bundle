<?php


namespace IIIRxs\ValidationErrorNormalizerBundle\DependencyInjection;


use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{

    const FORM_ERROR_MODES = ['root_only', 'verbose'];

    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('iiirxs_validation_error_normalizer');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->enumNode('form_validation_error_mode')
                    ->values(self::FORM_ERROR_MODES)
                    ->defaultValue('root_only')
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}