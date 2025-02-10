<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use App\Validator\EmailRequirements;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EmailType extends AbstractType
{
  public function configureOptions(OptionsResolver $resolver): void
  {
    $resolver->setDefaults([
      'constraints' => [
        new EmailRequirements,
      ],
    ]);
  }

  public function getParent(): string
  {
    return \Symfony\Component\Form\Extension\Core\Type\EmailType::class;
  }
}
