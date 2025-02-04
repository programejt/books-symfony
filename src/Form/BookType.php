<?php

namespace App\Form;

use App\Entity\Book;
use App\Entity\Author;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use App\Service\FormConstraints;

class BookType extends AbstractType
{
  public function buildForm(
    FormBuilderInterface $builder,
    array $options,
  ): void
  {
    $builder
      ->add('photo', FileType::class, [
        'data_class' => null,
        'required' => false,
        'mapped' => false,
        'constraints' => [
          FormConstraints::getForPhoto()
        ],
      ])
      ->add('title')
      ->add('authors', EntityType::class, [
        'class' => Author::class,
        'multiple' => true,
        'expanded' => true,
        'by_reference' => false,
      ])
      ->add('year', options: [
        'empty_data' => 2025,
      ])
      ->add('isbn')
      ->add('description', TextareaType::class);

    $builder->addEventListener(
      FormEvents::PRE_SET_DATA,
      function (FormEvent $event): void {
        $book = $event->getData();
        $form = $event->getForm();

        if ($book?->getPhoto()) {
          $form->add(
            'deletePhoto',
            CheckboxType::class,
            [
              'mapped' => false,
              'required' => false,
              'label' => 'Delete photo',
            ],
          );
        }
      }
    );
  }

  public function configureOptions(OptionsResolver $resolver): void
  {
    $resolver->setDefaults([
      'data_class' => Book::class,
    ]);
  }
}
