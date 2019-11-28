<?php

namespace IIIRxs\ValidationErrorNormalizerBundle\Tests;

use IIIRxs\ValidationErrorNormalizerBundle\IIIRxsValidationErrorNormalizerBundle;
use IIIRxs\ValidationErrorNormalizerBundle\Serializer\ConstraintViolationListNormalizer;
use IIIRxs\ValidationErrorNormalizerBundle\Serializer\FormValidationErrorNormalizer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Kernel;

class FunctionalTest extends TestCase
{

    public function testServiceWiring()
    {
        $kernel = new IIIRxsValidationErrorNormalizerTestingKernel('test', true);
        $kernel->boot();
        $container = $kernel->getContainer();

        $formValidationErrorNormalizer = $container->get('iiirxs_validation_error_normalizer.serializer.form_validation_error_normalizer');
        $this->assertInstanceOf(FormValidationErrorNormalizer::class, $formValidationErrorNormalizer);

        $constraintViolationListNormalizer = $container->get('iiirxs_validation_error_normalizer.serializer.constraint_violation_list_normalizer');
        $this->assertInstanceOf(ConstraintViolationListNormalizer::class, $constraintViolationListNormalizer);
    }

}

class IIIRxsValidationErrorNormalizerTestingKernel extends Kernel
{

    /**
     * Returns an array of bundles to register.
     *
     * @return iterable|BundleInterface[] An iterable of bundle instances
     */
    public function registerBundles()
    {
        return [
            new IIIRxsValidationErrorNormalizerBundle(),
        ];

    }


    /**
     * @param LoaderInterface $loader
     * Loads the container configuration.
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        // TODO: Implement registerContainerConfiguration() method.
    }
}