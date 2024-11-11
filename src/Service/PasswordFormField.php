<?php

namespace App\Service;

use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class PasswordFormField
{
  public static function getConfig(
    string $passwordLabel = 'Password',
    string $passwordRepeatLabel = 'Repeat password'
  ): array {
    return [
      'type' => PasswordType::class,
      'mapped' => false,
      'first_options'  => ['label' => $passwordLabel],
      'second_options' => ['label' => $passwordRepeatLabel],
      // 'attr' => ['autocomplete' => 'new-password'],

      'invalid_message' => 'The password fields must match',
      'constraints' => [
        new NotBlank([
          'message' => 'Please enter a password',
        ]),
        new Length([
          'min' => 6,
          'minMessage' => 'Your password should be at least {{ limit }} characters',
          'max' => 4096,
        ]),
      ],
    ];
  }
}