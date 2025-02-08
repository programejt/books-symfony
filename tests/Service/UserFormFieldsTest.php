<?php

namespace Test\Service;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;
use App\Service\UserFormFields;

final class UserFormFieldsTest extends WebTestCase
{
  public function testGetPasswordConfig(): void
  {
    $this->assertEquals(
      [
        'type' => PasswordType::class,
        'mapped' => false,
        'first_options'  => ['label' => 'Password'],
        'second_options' => ['label' => 'Repeat password'],
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
      ],
      UserFormFields::getPasswordConfig(
        'Password',
        'Repeat password',
      ),
    );
  }

  public function testGetNameConstraints(): void
  {
    $this->assertEquals(
      [
        new NotBlank,
        new Length(
          min: 2,
          max: 255
        )
      ],
      UserFormFields::getNameConstraints(),
    );
  }

  public function testGetEmailConstraints(): void
  {
    $this->assertEquals(
      [
        new NotBlank,
        new Email(
          message: 'The email is not valid'
        ),
        new Length(
          min: 3,
          max: 180
        )
      ],
      UserFormFields::getEmailConstraints(),
    );
  }
}