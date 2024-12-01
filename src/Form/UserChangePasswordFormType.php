<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use App\Service\UserFormFields;
use Symfony\Component\Validator\Constraints as Assert;

class UserChangePasswordFormType extends AbstractType
{
  public function buildForm(
    FormBuilderInterface $builder,
    array $options
  ): void
  {
    $builder
      ->add('password', PasswordType::class, [
        'required' => true,
        'constraints' => [
          new Assert\NotBlank
        ]
      ])
      ->add('newPassword', RepeatedType::class, UserFormFields::getPasswordConfig('New password', 'Repeat new password'))
    ;
  }

  public function configureOptions(OptionsResolver $resolver): void
  {}
}
