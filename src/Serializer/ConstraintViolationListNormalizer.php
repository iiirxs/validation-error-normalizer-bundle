<?php

namespace IIIRxs\ValidationErrorNormalizerBundle\Serializer;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ConstraintViolationListNormalizer implements NormalizerInterface
{
    use ContraintViolationPathTrait;

    const FORMAT = 'constraint.violation.list';

    /**
     * Normalizes an object into a set of arrays/scalars.
     *
     * @param ConstraintViolationListInterface $constraintViolationList
     * @param string $format Format the normalization result will be encoded as
     * @param array $context Context options for the normalizer
     *
     * @return array|string|int|float|bool
     *
     */
    public function normalize($constraintViolationList, $format = null, array $context = [])
    {
        $errors = [];
        foreach ($constraintViolationList as $constraintViolation) {
            $path = $this->getViolationPath($constraintViolation);
            $errors[$path][] = $constraintViolation->getMessage();
        }
        return $errors;
    }

    public function supportsNormalization($data, $format = null, array $context = [])
    {
        return
            $format === self::FORMAT
            && $data instanceof ConstraintViolationListInterface;
    }
}