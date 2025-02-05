<?php

namespace App\Form;

use App\Entity\User;
use App\Enum\UserRole;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use App\Validator\AnyOtherAdminExists;
use Symfony\Component\Form\FormInterface;

class ChangeUserRoleType extends AbstractType
{
  private const string ROLE_OTHER_THAN_ADMIN = 'roleOtherThanAdmin';

  public function buildForm(
    FormBuilderInterface $builder,
    array $options,
  ): void {
    $builder
      ->add('user', EntityType::class, [
        'class' => User::class,
        'choice_label' => 'name',
        'invalid_message' => 'The selected user is not valid. Please choose a valid user.',
        'constraints' => [
          new AnyOtherAdminExists(
            groups: [self::ROLE_OTHER_THAN_ADMIN]
          )
        ],
      ])
      ->add('role', EnumType::class, [
        'class' => UserRole::class,
        'invalid_message' => 'The selected role is not valid. Please choose a valid role.',
      ]);
  }

  public function configureOptions(OptionsResolver $resolver): void
  {
    $resolver->setDefaults([
      'data_class' => null,
      'validation_groups' => function (FormInterface $form): array {
        $data = $form->getData();

        if ($data['role'] !== UserRole::Admin) {
          return [self::ROLE_OTHER_THAN_ADMIN];
        }

        return [];
      }
    ]);
  }
}
