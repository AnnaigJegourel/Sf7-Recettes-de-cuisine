<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Recipe;
use DateTimeImmutable;
use App\Entity\Category;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use FakerRestaurant\Provider\ar_SA\Restaurant;
use Symfony\Component\String\Slugger\SluggerInterface;

class RecipeFixtures extends Fixture
{
    public function __construct(private readonly SluggerInterface $slugger)
    {
        
    }
    public function load(ObjectManager $manager): void
    {
        //on appelle les bundle & bibliothèque tiers qui construisent de fausses données
        $faker = Factory::create('fr_FR');
        $faker->addProvider(new Restaurant($faker));

        //on crée des catégories
        $categories = ['Plat chaud', 'Dessert', 'Entrée', 'Goûter'];
        foreach($categories as $c) {
            $category = (new Category)
                ->setName($c)
                ->setSlug($this->slugger->slug($c))
                ->setUpdatedAt(DateTimeImmutable::createFromMutable($faker->dateTime()))
                ->setCreatedAt(DateTimeImmutable::createFromMutable($faker->dateTime()));
            $manager->persist($category);
            //on crée une référence pour qu'elle puisse être appelée dans les recettes
            $this->addReference($c, $category);
        }

        //on crée des recettes
        for($i = 1; $i <= 10; $i++) {
            $title = $faker->foodName();
            $recipe = (new Recipe)
                ->setTitle($title)
                ->setSlug($this->slugger->slug($title))
                ->setUpdatedAt(DateTimeImmutable::createFromMutable($faker->dateTime()))
                ->setCreatedAt(DateTimeImmutable::createFromMutable($faker->dateTime()))
                ->setContent($faker->paragraphs(10, true))
                //on utilise la référence de la catégorie pour la rattacher
                //faker prend un élément aléatoire dans le tableau des catégories
                ->setCategory($this->getReference($faker->randomElement($categories)))
                ->setDuration($faker->numberBetween(2, 60));
            $manager->persist($recipe);
        }

        $manager->flush();
    }
}
