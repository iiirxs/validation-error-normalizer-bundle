<?php

namespace IIIRxs\ValidationErrorNormalizerBundle\Serializer;

use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Validator\ConstraintViolation;

class FormValidationErrorNormalizer implements NormalizerInterface
{

    use ContraintViolationPathTrait;

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
        $errors = [];

        foreach ($form->getErrors() as $error) {
            $errors = array_merge_recursive($errors, $this->normalizeError($error));
        }

        foreach ($form->all() as $childForm) {
            if ($childForm instanceof FormInterface) {
                if ($childErrors = $this->normalize($childForm)) {
                    $errors = array_merge_recursive($errors, $childErrors);
                }
            }
        }

        return $errors;
    }

    protected function normalizeError(FormError $formError)
    {
        if ($formError->getCause() instanceof ConstraintViolation) {
            return $this->normalizeConstraintViolation($formError->getCause());
        }
        return ['global' => $formError->getMessage()];
    }

    public function supportsNormalization($data, $format = null, array $context = [])
    {
        return $data instanceof FormInterface && $format === self::FORMAT;
    }

}