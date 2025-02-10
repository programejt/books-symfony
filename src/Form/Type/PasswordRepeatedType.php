<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use App\Validator\PasswordRequirements;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PasswordRepeatedType extends AbstractType
{
  public function configureOptions(OptionsResolver $resolver): void
  {
    $resolver->setDefaults([
      'type' => PasswordType::class,
      'mapped' => false,
      'first_options'  => ['label' => 'Password'],
      'second_options' => ['label' => 'Repeat password'],
      // 'attr' => ['autocomplete' => 'new-password'],
      'invalid_message' => 'The password fields must match',
      'constraints' => [
        new PasswordRequirements,
      ],
    ]);
  }

  public function getParent(): string
  {
    return RepeatedType::class;
  }
}
