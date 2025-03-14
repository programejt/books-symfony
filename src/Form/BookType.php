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
use App\Validator\ImageRequirements;

class BookType extends AbstractType
{
  public function buildForm(
    FormBuilderInterface $builder,
    array $options,
  ): void {
    $builder
      ->add('photo', FileType::class, [
        'data_class' => null,
        'required' => false,
        'mapped' => false,
        'label' => 'photo',
        'constraints' => [
          new ImageRequirements,
        ],
      ])
      ->add('title', options: [
        'label' => 'title',
      ])
      ->add('authors', EntityType::class, [
        'class' => Author::class,
        'multiple' => true,
        'expanded' => true,
        'by_reference' => false,
        'label' => 'authors',
      ])
      ->add('year', options: [
        'label' => 'year',
      ])
      ->add('isbn', options: [
        'label' => 'isbn',
      ])
      ->add('description', TextareaType::class, [
        'label' => 'description',
      ]);

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
              'label' => 'delete_photo',
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
