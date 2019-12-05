<?php

namespace IIIRxs\ValidationErrorNormalizerBundle\Serializer;

use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Validator\ConstraintViolation;

Trait ContraintViolationNormalizerTrait
{

    protected $mode;

    public function __construct(int $formValidationErrorMode)
    {
        $this->mode = $formValidationErrorMode;
    }

    protected function normalizeConstraintViolation(ConstraintViolation $constraintViolation)
    {

        $pattern = "/(?=(\[(?<form>.*?]*)]))|(?=(\.(?<json>[^\.]*)))/";
        preg_match_all($pattern, $constraintViolation->getPropertyPath(), $fields);

        $matches = array_filter($fields['form'] ?? []) ?: array_filter($fields['json'] ?? []);

        switch ($this->mode) {
            case 0:
                return [ $matches[0] ?? $constraintViolation->getPropertyPath() => $constraintViolation->getMessage() ];
            case 1:
                $array = [];
                $accessor = PropertyAccess::createPropertyAccessor();
                $accessor->setValue($array, '[' . implode('][', $matches ?? []) . ']', $constraintViolation->getMessage());
                return $array;
        }

        return null;
    }

}