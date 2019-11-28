<?php

namespace IIIRxs\ValidationErrorNormalizerBundle\Serializer;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\PropertyNormalizer;

class FormValidationErrorNormalizer extends PropertyNormalizer
{

    const FORMAT = 'form.validation.error';

    /**
     * @param FormInterface $form
     * @param null $format
     * @param array $context
     * @return array
     * @throws ExceptionInterface
     */
    public function normalize($form, $format = null, array $context = []): array
    {
        $normalizedErrors = parent::normalize($form->getErrors(true))['errors'];

        $mapMessage = function (array $error) { return $error['message']; };
        $mapPropertyPath = function (array $error) { return $error['cause']['propertyPath'] ?? 'global'; };

        $errorMessages = array_map($mapMessage, $normalizedErrors);
        $errorPaths = array_map($mapPropertyPath, $normalizedErrors);

        return array_combine($errorPaths, $errorMessages);
    }

    public function supportsNormalization($data, $format = null, array $context = [])
    {
        return $data instanceof FormInterface && $format === self::FORMAT;
    }
}