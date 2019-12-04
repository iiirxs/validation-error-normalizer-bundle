<?php

namespace IIIRxs\ValidationErrorNormalizerBundle;

use IIIRxs\ValidationErrorNormalizerBundle\DependencyInjection\IIIRxsValidationErrorNormalizerExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class IIIRxsValidationErrorNormalizerBundle extends Bundle
{

    public function getContainerExtension()
    {
        if (null === $this->extension) {
            $this->extension = new IIIRxsValidationErrorNormalizerExtension();
        }
        return $this->extension;
    }

}