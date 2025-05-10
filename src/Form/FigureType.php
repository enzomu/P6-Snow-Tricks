<?php

namespace App\Form;

use App\Entity\Figure;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Url;

class FigureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom',
                'attr' => ['placeholder' => 'Nom de la figure']
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => ['rows' => 5]
            ])
            ->add('category', ChoiceType::class, [
                'label' => 'Catégorie',
                'choices' => [
                    'Les grabs' => 'Les grabs',
                    'Les rotations' => 'Les rotations',
                    'Les flips' => 'Les flips',
                    'Les slides' => 'Les slides',
                    'Old school' => 'Old school'
                ]
            ])
            ->add('mainMedia', UrlType::class, [
                'label' => 'Média principal (URL)',
                'required' => false,
                'attr' => [
                    'placeholder' => 'https://example.com/image.jpg ou https://youtube.com/embed/...'
                ],
                'constraints' => [
                    new Url([
                        'message' => 'Veuillez entrer une URL valide',
                    ]),
                ],
            ])
            ->add('mainMediaFileUpload', FileType::class, [
                'label' => 'OU Image principale (Fichier)',
                'required' => false,
                'mapped' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '5M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/gif',
                            'image/webp'
                        ],
                        'mimeTypesMessage' => 'Veuillez uploader une image valide',
                    ])
                ],
                'attr' => ['class' => 'form-control']
            ])
            ->add('mediaGallery', CollectionType::class, [
                'entry_type' => UrlType::class,
                'entry_options' => [
                    'label' => false,
                    'attr' => [
                        'class' => 'media-url-input',
                        'placeholder' => 'URL du média'
                    ],
                    'constraints' => [
                        new Url([
                            'message' => 'Veuillez entrer une URL valide',
                        ]),
                    ],
                ],
                'label' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'by_reference' => false,
                'required' => false,
            ])
            ->add('mediaGalleryFileUpload', FileType::class, [
                'label' => 'Ajouter des images à la galerie',
                'required' => false,
                'mapped' => false,
                'multiple' => true,
                'attr' => [
                    'class' => 'form-control',
                    'multiple' => 'multiple',
                    'accept' => 'image/jpeg,image/png,image/gif,image/webp'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Figure::class,
        ]);
    }
}