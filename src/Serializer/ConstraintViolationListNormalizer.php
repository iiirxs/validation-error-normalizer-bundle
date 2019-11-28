<?php

namespace IIIRxs\ValidationErrorNormalizerBundle\Serializer;

use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ConstraintViolationListNormalizer implements ContextAwareNormalizerInterface
{

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

    private function getViolationPath(ConstraintViolation $constraintViolation) {
        preg_match_all("/\[([^\]]*)\]/", $constraintViolation->getPropertyPath(), $fields);

        return $fields[1][0] ?? $constraintViolation->getPropertyPath();
    }

    public function supportsNormalization($data, $format = null, array $context = [])
    {
        return
            $format === self::FORMAT
            && $data instanceof ConstraintViolationListInterface;
    }
}