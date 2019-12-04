<?php

namespace IIIRxs\ValidationErrorNormalizerBundle\Serializer;

use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Validator\ConstraintViolation;

Trait ContraintViolationPathTrait {

    protected $mode;

    public function __construct(int $formValidationErrorMode)
    {
        $this->mode = $formValidationErrorMode;
    }

    protected function normalizeConstraintViolation(ConstraintViolation $constraintViolation)
    {
        preg_match_all("/\[([^\]]*)\]/", $constraintViolation->getPropertyPath(), $fields);

        switch ($this->mode) {
            case 0:
                return [ $fields[1][0] ?? $constraintViolation->getPropertyPath() => $constraintViolation->getMessage() ];
            case 1:
                $array = [];
                $accessor = PropertyAccess::createPropertyAccessor();

                $accessor->setValue($array, implode('', $fields[0]), $constraintViolation->getMessage());
                return $array;
        }

        return null;
    }

}