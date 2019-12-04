<?php

namespace IIIRxs\ValidationErrorNormalizerBundle\Tests\Serializer;

use IIIRxs\ValidationErrorNormalizerBundle\Serializer\FormValidationErrorNormalizer;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Forms;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\PropertyNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;

class FormValidationErrorNormalizerTest extends TypeTestCase
{

    public function testSupportsNormalization()
    {
        $form = $this->factory->create();

        $validationErrorNormalizer = new FormValidationErrorNormalizer(1);

        $this->assertTrue($validationErrorNormalizer->supportsNormalization($form, 'form.validation.error'));
    }

    /**
     * @dataProvider validationErrorProvider
     */
    public function testNormalize($mode, $expected)
    {
        $form = $this->getFormBuilder()
            ->add('testProperty0', TextType::class, [
                'required' => true,
                'constraints' => [
                    new Length(['max' => 5, 'maxMessage' => 'Input too long']),
                    new Email(['message' => 'Invalid email'])
                ]
            ])
            ->add(
                $this->getFormBuilder()->create('location', FormType::class)
                    ->add('address', TextType::class, [
                        'constraints' => [ new Length(['min' => 10, 'minMessage' => 'Input too short']) ]
                    ])
                    ->add(
                        $this->getFormBuilder()->create('coordinates', FormType::class)
                            ->add('lng', IntegerType::class, [
                                'constraints' => [ new Positive(['message' => 'Longitude should be positive']) ]
                            ])
                            ->add('lat', IntegerType::class, [
                                'constraints' => [ new Positive(['message' => 'Latitude should be positive']) ]
                            ])
                    )
            )
            ->getForm()
        ;

        $formData = [
            'testProperty0' => '123456',
            'location' => [
                'address' => 'Volos',
                'coordinates' => [
                    'lng' => 0,
                    'lat' => 0
                ]
            ]
        ];
        $form->submit($formData);
        $validationErrorNormalizer = new FormValidationErrorNormalizer($mode);

        $this->assertEquals($expected, $validationErrorNormalizer->normalize($form));
    }

    public function validationErrorProvider()
    {
        return [
            [
                'mode' => 0,
                'expected' => [
                    'testProperty0' => ['Input too long', 'Invalid email'],
                    'location' => [
                        'Input too short',
                        'Longitude should be positive',
                        'Latitude should be positive'
                    ]
                ]
            ],
            [
                'mode' => 1,
                'expected' => [
                    'testProperty0' => ['Input too long', 'Invalid email'],
                    'location' => [
                        "address" => "Input too short",
                        "coordinates" => [
                            "lat" => "Latitude should be positive",
                            "lng" => "Longitude should be positive"
                        ]
                    ]
                ]
            ]

        ];
    }

    protected function getFormBuilder()
    {
        return Forms::createFormFactoryBuilder()
            ->addExtension(new ValidatorExtension(Validation::createValidator()))
            ->getFormFactory()
            ->createBuilder();
    }

}