<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Service\UserFormFields;

class UserChangeNameFormType extends AbstractType
{
  public function buildForm(
    FormBuilderInterface $builder,
    array $options
  ): void
  {
    $builder
      ->add('name', options: [
        'required' => true,
        'constraints' => UserFormFields::getNameConstraints()
      ])
    ;
  }

  public function configureOptions(OptionsResolver $resolver): void
  {}
}
