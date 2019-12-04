<?php

namespace IIIRxs\ValidationErrorNormalizerBundle\DependencyInjection;

use IIIRxs\ValidationErrorNormalizerBundle\Serializer\ConstraintViolationListNormalizer;
use IIIRxs\ValidationErrorNormalizerBundle\Serializer\FormValidationErrorNormalizer;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class IIIRxsValidationErrorNormalizerExtension extends Extension
{

    /**
     * Loads a specific configuration.
     *
     * @param array $configs
     * @param ContainerBuilder $container
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../Resources/config')
        );

        $loader->load('services.xml');

        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        $mode = $config['form_validation_error_mode'];
        $intMode = (int) array_search($mode, Configuration::FORM_ERROR_MODES);
        $definition = $container->findDefinition(FormValidationErrorNormalizer::class);
        $definition->setArgument(0, $intMode);
        $definition = $container->findDefinition(ConstraintViolationListNormalizer::class);
        $definition->setArgument(0, $intMode);
    }

    public function getAlias()
    {
        return 'iiirxs_validation_error_normalizer';
    }

}