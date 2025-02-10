<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use App\Form\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
// use App\Entity\User;

class UserChangeEmailType extends AbstractType
{
  public function buildForm(
    FormBuilderInterface $builder,
    array $options,
  ): void {
    $builder
      ->add('email', EmailType::class)
    ;
  }

  public function configureOptions(OptionsResolver $resolver): void
  {
    // $resolver->setDefaults([
      // 'data_class' => User::class,
    // ]);
  }
}
