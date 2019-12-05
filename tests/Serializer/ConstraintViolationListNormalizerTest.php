<?php


namespace IIIRxs\ValidationErrorNormalizerBundle\Tests\Serializer;


use IIIRxs\ValidationErrorNormalizerBundle\Serializer\ConstraintViolationListNormalizer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\ValidatorBuilder;

class ConstraintViolationListNormalizerTest extends TestCase
{

    public function testSupportsNormalization()
    {
        $normalizer = new ConstraintViolationListNormalizer(0);
        $this->assertTrue($normalizer->supportsNormalization(new ConstraintViolationList(), 'constraint.violation.list'));
    }

    /**
     * @dataProvider modeProvider
     */
    public function testNormalize($mode)
    {
        $constraints = new Collection([
            'email' => [
                new Email(['message' => 'Invalid e-mail']),
                new NotBlank(['message' => 'Empty e-mail']),
            ]
        ]);

        $data = [ 'email' => 'foobar' ];

        $errors = $this->getValidator()->validate($data, $constraints, $groups = null);

        $expected = [ 'email' => 'Invalid e-mail' ];

        $normalizer = new ConstraintViolationListNormalizer($mode);

        $this->assertEquals($expected, $normalizer->normalize($errors));

    }

    public function modeProvider()
    {
        return [ [0], [1] ];
    }

    protected function getValidator()
    {
        $validatorBuilder = new ValidatorBuilder();
        return $validatorBuilder->getValidator();
    }

}