<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use App\Form\Type\PasswordRepeatedType;
use App\Form\Type\EmailType;
use App\Form\Type\UserNameType;
use App\Validator\EmailRequirements;
use App\Validator\UniqueEmail;

class RegistrationType extends AbstractType
{
  public function buildForm(
    FormBuilderInterface $builder,
    array $options,
  ): void {
    $builder
      ->add('email', EmailType::class, [
        'label' => 'email',
        'constraints' => [
          new EmailRequirements,
          new UniqueEmail('newEmail'),
        ],
      ])
      ->add('name', UserNameType::class, [
        'label' => 'name',
      ])
      ->add('password', PasswordRepeatedType::class)
      ->add('agreeTerms', CheckboxType::class, [
        'mapped' => false,
        'label' => 'agree_terms',
        'constraints' => [
          new IsTrue([
            'message' => 'agree_terms',
          ])
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
