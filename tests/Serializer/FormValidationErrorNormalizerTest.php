<?php

namespace IIIRxs\ValidationErrorNormalizerBundle\Tests\Serializer;

use IIIRxs\ValidationErrorNormalizerBundle\Serializer\FormValidationErrorNormalizer;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Forms;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\PropertyNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;

class FormValidationErrorNormalizerTest extends TypeTestCase
{

    public function testSupportsNormalization()
    {
        $form = $this->factory->create();

        $validationErrorNormalizer = new FormValidationErrorNormalizer();

        $this->assertTrue($validationErrorNormalizer->supportsNormalization($form, 'form.validation.error'));
    }

    public function testNormalize()
    {
        $form = $this->getFormBuilder()
            ->add('testProperty0', TextType::class, [
                'required' => true,
                'constraints' => [ new Length(['max' => 5, 'maxMessage' => 'Input too long']) ]
            ])
            ->getForm()
        ;

        $formData = [ 'testProperty0' => '123456' ];
        $form->submit($formData);
        $validationErrorNormalizer = $this->getNormalizer();

        $expected = [ 'children[testProperty0].data' => 'Input too long' ];
        $this->assertEquals($expected, $validationErrorNormalizer->normalize($form));
    }

    protected function getNormalizer()
    {
        $validationErrorNormalizer = new FormValidationErrorNormalizer();
        $validationErrorNormalizer->setSerializer(new Serializer([new PropertyNormalizer()]));

        return $validationErrorNormalizer;
    }

    protected function getFormBuilder()
    {
        return Forms::createFormFactoryBuilder()
            ->addExtension(new ValidatorExtension(Validation::createValidator()))
            ->getFormFactory()
            ->createBuilder();
    }

}