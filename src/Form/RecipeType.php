<?php

namespace App\Form;

use App\Entity\Recipe;
use App\Entity\Category;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Event\PreSubmitEvent;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class RecipeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre',
                'empty_data' => ''
            ])
            ->add('slug', TextType::class, [
                'required' => false,
                // 'constraints' => [
                //     new Length(min: 10),
                //     new Regex('/^[a-z0-9]+(?:-[a-z0-9]+)*$/')
                //     ]
            ])
            // Pour selectionner les différentes catégories EntityType::class
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name', 
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Descriptif',
                'empty_data' => ''
            ])
            ->add('duration', TextType::class, [
                'label' => 'Durée'
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Envoyer'
            ])
            ->addEventListener(FormEvents::PRE_SUBMIT, $this->autoSlug(...))
            ->add('thumbnailFile', FileType::class, [
                // 'mapped' => false,
                // 'constraints' => [
                //     new Image()
                // ]
            ])
        ;
    }

    public function autoSlug(PreSubmitEvent $event): void {
        $data = $event->getData();
        if (empty($data["slug"])) {
            $slugger = new AsciiSlugger();
            $data['slug'] = strtolower($slugger->slug($data['title']));
        }
        $event->setData($data);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Recipe::class,
        ]);
    }
}
