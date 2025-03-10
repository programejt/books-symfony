<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use App\Form\Type\PasswordRepeatedType;

class UserChangePasswordType extends AbstractType
{
  public function buildForm(
    FormBuilderInterface $builder,
    array $options,
  ): void {
    $builder
      ->add('password', PasswordType::class, [
        'label' => 'password',
        'constraints' => [
          new \App\Validator\CurrentPassword,
        ],
      ])
      ->add('newPassword', PasswordRepeatedType::class, [
        'first_options'  => [
          'label' => 'new_password',
        ],
        'second_options' => [
          'label' => 'repeat_new_password',
        ],
      ])
    ;
  }

  public function configureOptions(OptionsResolver $resolver): void
  {
  }
}
