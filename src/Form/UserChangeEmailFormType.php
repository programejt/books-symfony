<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Service\UserFormFields;

class UserChangeEmailFormType extends AbstractType
{
  public function buildForm(
    FormBuilderInterface $builder,
    array $options
  ): void
  {
    $builder
      ->add('email', EmailType::class, [
        'required' => true,
        'constraints' => UserFormFields::getEmailConstraints()
      ])
    ;
  }

  public function configureOptions(OptionsResolver $resolver): void
  {}
}
