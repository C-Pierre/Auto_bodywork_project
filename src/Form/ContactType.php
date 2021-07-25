<?php

namespace App\Form;

use App\Entity\Message;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Entrez votre message',
                    ]),
                ],
            ])
            ->add('lastname', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Entrez votre message',
                    ]),
                ],
            ])
            ->add('email', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Entrez votre message',
                    ]),
                ],
            ])
            ->add('phone', TextType::class)
            ->add('email', TextType::class)
            ->add('message', TextareaType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Entrez votre message',
                    ]),
                ],
            ]);
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Message::class,
        ]);
    }
}