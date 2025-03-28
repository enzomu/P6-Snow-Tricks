<?php

namespace App\Form;

use App\Entity\Figure;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
            ->add('mainMedia', TextType::class, [
                'label' => 'Média principal (URL YouTube embed ou image)',
                'required' => false,
                'attr' => [
                    'placeholder' => 'https://www.youtube.com/embed/... ou https://example.com/image.jpg',
                    'pattern' => 'https?://.+'
                ],
                'constraints' => [
                    new \Symfony\Component\Validator\Constraints\Url([
                        'message' => 'Veuillez entrer une URL valide',
                    ]),
                ],
            ])
            ->add('mediaGallery', CollectionType::class, [
                'entry_type' => TextType::class,
                'entry_options' => [
                    'label' => false,
                    'attr' => [
                        'placeholder' => 'https://www.youtube.com/embed/... ou https://example.com/image.jpg',
                        'pattern' => 'https?://.+'
                    ],
                    'constraints' => [
                        new \Symfony\Component\Validator\Constraints\Url([
                            'message' => 'Veuillez entrer une URL valide',
                        ]),
                    ]
                ],
                'label' => 'Galerie de médias',
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Figure::class,
        ]);
    }
}