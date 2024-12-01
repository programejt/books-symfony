<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use App\Service\UserFormFields;

class UserChangePhotoFormType extends AbstractType
{
  public function __construct(
    private Security $security
  ) {}

  public function buildForm(
    FormBuilderInterface $builder,
    array $options
  ): void
  {
    $builder
      ->add('photo', FileType::class, [
        'required' => false,
        'constraints' => UserFormFields::getPhotoConstraints()
      ])
    ;

    $builder->addEventListener(
      FormEvents::PRE_SET_DATA,
      function (FormEvent $event): void {
        /** @var User $user */
        $user = $this->security->getUser();
        $form = $event->getForm();

        if (null != $user->getPhoto()) {
          $form->add('deletePhoto', CheckboxType::class, [
            'required' => false,
            'label' => 'Delete photo'
          ]);
        }
      }
    );
  }

  public function configureOptions(OptionsResolver $resolver): void
  {}
}
