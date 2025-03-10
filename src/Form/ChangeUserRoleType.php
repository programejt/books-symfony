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
        'label' => 'user',
        'invalid_message' => 'select.user',
        'constraints' => [
          new AnyOtherAdminExists(
            groups: [self::ROLE_OTHER_THAN_ADMIN]
          )
        ],
      ])
      ->add('role', EnumType::class, [
        'class' => UserRole::class,
        'label' => 'role',
        'invalid_message' => 'select.user_role',
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
