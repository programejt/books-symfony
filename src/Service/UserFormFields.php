<?php

namespace App\Service;

use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;

class UserFormFields
{
  public static function getPasswordConfig(
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
          'message' => 'Please enter a password'
        ]),
        new Length([
          'min' => 6,
          'minMessage' => 'Your password should be at least {{ limit }} characters',
          'max' => 255,
          'maxMessage' => 'Your password should be max {{ limit }} characters'
        ]),
      ],
    ];
  }

  public static function getNameConstraints(): array
  {
    return [
      new NotBlank,
      new Length(
        min: 2,
        max: 255
      )
    ];
  }

  public static function getEmailConstraints(): array
  {
    return [
      new NotBlank,
      new Email(
        message: 'The email is not valid'
      ),
      new Length(
        min: 3,
        max: 180
      )
    ];
  }
}