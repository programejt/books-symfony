<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use App\Form\Type\PasswordRepeatedType;
use Symfony\Component\Validator\Constraints as Assert;

class UserChangePasswordType extends AbstractType
{
  public function buildForm(
    FormBuilderInterface $builder,
    array $options,
  ): void {
    $builder
      ->add('password', PasswordType::class, [
        'constraints' => [
          new Assert\NotBlank
        ]
      ])
      ->add('newPassword', PasswordRepeatedType::class, [
        'first_options'  => ['label' => 'New password'],
        'second_options' => ['label' => 'Repeat new password'],
      ])
    ;
  }

  public function configureOptions(OptionsResolver $resolver): void
  {}
}
