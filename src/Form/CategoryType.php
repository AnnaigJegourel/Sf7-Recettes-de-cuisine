<?php

namespace App\Form;

use App\Entity\Recipe;
use App\Entity\Category;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class CategoryType extends AbstractType
{
    public function __construct(private FormListenerFactory $listenerFactory)
    {
        
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                "empty_data" => ""
            ])
            ->add('slug', TextType::class, [
                "required" => false,
                "empty_data" => ""
            ])
            ->add('recipes', EntityType::class, [
                'class' => Recipe::class,
                //menu de labels corresopndant au champ title
                'choice_label' => 'title',
                //pour pouvoir en sélectionner plusieurs
                'multiple' => true,
                //pour avoir des checkbox
                'expanded' => true,
                //pour que ce soit enregistré en bdd car utilise add() et remove() plutôt que chercher un setter
                'by_reference' => false
            ])
            ->add('save', SubmitType::class, [
                "label" => "Enregistrer"
            ])
            ->addEventListener(FormEvents::PRE_SUBMIT, $this->listenerFactory->autoSlug('name'))
            ->addEventListener(FormEvents::POST_SUBMIT, $this->listenerFactory->timestamps())
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Category::class,
        ]);
    }
}
