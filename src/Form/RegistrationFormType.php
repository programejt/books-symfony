<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use App\Service\UserFormFields;

class RegistrationFormType extends AbstractType
{
  public function buildForm(
    FormBuilderInterface $builder,
    array $options
  ): void
  {
    $builder
      ->add('email', EmailType::class, [
        'constraints' => UserFormFields::getEmailConstraints()
      ])
      ->add('name', options: [
        'constraints' => UserFormFields::getNameConstraints()
      ])
      ->add('password', RepeatedType::class, UserFormFields::getPasswordConfig())
      ->add('agreeTerms', CheckboxType::class, [
        'mapped' => false,
        'constraints' => [
          new IsTrue([
            'message' => 'You should agree to our terms.',
          ])
        ]
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
