<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use App\Service\PasswordFormField;

class UserChangePasswordFormType extends AbstractType
{
  public function buildForm(
    FormBuilderInterface $builder,
    array $options
  ): void
  {
    $builder
      ->add('password', PasswordType::class)
      ->add('newPassword', RepeatedType::class, PasswordFormField::getConfig('New password', 'Repeat new password'))
    ;
  }

  public function configureOptions(OptionsResolver $resolver): void
  {
    $resolver->setDefaults([
      'data_class' => User::class,
    ]);
  }
}
