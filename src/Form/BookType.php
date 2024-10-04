<?php

namespace App\Form;

use App\Entity\Book;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class BookType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options): void
  {
    $builder
      ->add('title')
      ->add('author')
      ->add('description', TextareaType::class)
      ->add('year')
      ->add('isbn')
      ->add('photo', FileType::class, [
        'data_class' => null,
        'required' => false,
        'constraints' => [
          new File([
            'maxSize' => '25m',
            'mimeTypes' => [
                'image/jpg',
                'image/jpeg',
                'image/png',
                'image/webp'
            ],
            'mimeTypesMessage' => 'Please upload a valid image',
          ])
        ],
      ]);

    $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event): void {
      $book = $event->getData();
      $form = $event->getForm();

      if ($book && null != $book->getPhoto()) {
        $form->add('deletePhoto', CheckboxType::class, [
          'mapped' => false,
          'required' => false,
          'label' => 'Delete photo'
        ]);
      }
    });
  }

  public function configureOptions(OptionsResolver $resolver): void
  {
    $resolver->setDefaults([
        'data_class' => Book::class,
    ]);
  }
}
