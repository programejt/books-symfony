<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use App\Validator\UserNameRequirements;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class UserNameType extends AbstractType
{
  public function configureOptions(OptionsResolver $resolver): void
  {
    $resolver->setDefaults([
      'constraints' => [
        new UserNameRequirements,
      ],
    ]);
  }

  public function getParent(): string
  {
    return TextType::class;
  }
}
