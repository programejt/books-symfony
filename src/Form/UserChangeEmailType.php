<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use App\Form\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\User;
use App\Validator\EmailRequirements;
use App\Validator\UniqueEmail;

class UserChangeEmailType extends AbstractType
{
  public function buildForm(
    FormBuilderInterface $builder,
    array $options,
  ): void {
    $builder
      ->add('email', EmailType::class, [
        'label' => 'email',
        'property_path' => 'newEmail',
        'constraints' => [
          new EmailRequirements,
          new UniqueEmail,
        ],
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
