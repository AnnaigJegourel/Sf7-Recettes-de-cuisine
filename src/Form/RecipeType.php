<?php

namespace App\Form;

use App\Entity\Recipe;
use DateTimeImmutable;
use App\Entity\Category;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Event\PreSubmitEvent;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Event\PostSubmitEvent;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Sequentially;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class RecipeType extends AbstractType
{
    //constructeur pour utiliser le service d'événement pour les formulaires
    public function __construct(private FormListenerFactory $listenerFactory)
    {
        
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        // on peut préciser le type de champ, modifier le label...
            ->add('title', TextType::class, [
                'empty_data' => ''
            ])
            ->add('slug', TextType::class, [
                'required' => false
            ])
            ->add('thumbnailFile', FileType::class)
/*             SANS VICH UPLOADER
            ->add('thumbnailFile', FileType::class, [
                //ne sera pas mappé vers un champ de la bdd (pas de recherche des getters et setters)
                'mapped' => false,
                //il faut donc ajouter les contraintes car pas d'entité
                'constraints' => [
                    //il faut que ce soit une image
                    new Image()
                ]
            ])
 */

 //Contraintes au niveau des champs
/*             ->add('slug', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Length(min: 10),
                    new Regex(
                        "/ ^[a-z0-9]+(?:-[a-z0-9]+)*$ /", 
                        message: "Certains caractères ne sont pas acceptés."
                    )
                ]
            ])
 */

 //Vérifier une contrainte après l'autre (envoie un seul message)
/*                 'constraints' => new Sequentially([
                    new Length(min: 10),
                    new Regex(
                        "/ ^[a-z0-9]+(?:-[a-z0-9]+)*$ /", 
                        message: "Certains caractères ne sont pas acceptés."
                    )
                ]) */ 
            ->add('category', CategoryAutocompleteField::class)
            //Champ entity type classique
/*             ->add('category', EntityType::class, [
                'class' => Category::class,
                //menu de labels corresopndant au champ name
                'choice_label' => 'name',
                //pour avoir des boutons radio
                //'expanded' => 'true',
                'autocomplete' => 'true'
            ]) */
            ->add('content', TextareaType::class, [
                'empty_data' => ''
            ])
            ->add('duration')
            // Création du bouton submit
            //on peut préciser le type de champ, modifier le label par défaut
            ->add('quantities', CollectionType::class, [
                'entry_type' => QuantityType::class,    //nom de la classe représentant chacune de ces entrées
                'allow_add' => true,
                'by_reference' => false,    //utiliser add() et remove() et non modifier la collection
                'entry_options' => ['label' => false]
            ])
            ->add('save', SubmitType::class, [
                'label' => "Envoyer"
            ])
            ->addEventListener(FormEvents::PRE_SUBMIT, $this->listenerFactory->autoSlug('title'))
            ->addEventListener(FormEvents::POST_SUBMIT, $this->listenerFactory->timestamps())
            // sans service / factory :
            //->addEventListener(FormEvents::PRE_SUBMIT, $this->autoSlug(...))
            //->addEventListener(FormEvents::POST_SUBMIT, $this->attachTimestamps(...))
        ;
    }



/*     public function autoSlug(PreSubmitEvent $event): void
    {
        //les données sont un tableau
        $data = $event->getData();
        if(empty($data['slug'])) {
            $slugger = new AsciiSlugger();
            $data['slug'] = strtolower($slugger->slug($data['title']));
            $event->setData($data);
        }
    }

    public function attachTimestamps(PostSubmitEvent $event): void
    {
        //les données sont un objet Recipe
        $data = $event->getData();
        if(!($data instanceof Recipe)){
            return;
        }

        $data->setUpdatedAt(new DateTimeImmutable());
        //si nouvelle entité
        if(!($data->getId())) {
            $data->setCreatedAt(new DateTimeImmutable());
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Recipe::class,
        ]);
    }
 */}
