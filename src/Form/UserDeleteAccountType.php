<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class UserDeleteAccountType extends AbstractType
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
    ;
  }

  public function configureOptions(OptionsResolver $resolver): void
  {
  }
}
