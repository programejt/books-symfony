<?php

namespace App\Form;

use App\Form\Type\UserNameType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\User;

class UserChangeNameType extends AbstractType
{
  public function buildForm(
    FormBuilderInterface $builder,
    array $options,
  ): void {
    $builder
      ->add('name', UserNameType::class, [
        'label' => 'name',
      ])
    ;
  }

  public function configureOptions(OptionsResolver $resolver): void
  {
    $resolver->setDefaults([
      'data_class' => User::class,
    ]);
  }
}
