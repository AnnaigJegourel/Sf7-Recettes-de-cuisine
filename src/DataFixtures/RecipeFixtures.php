<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Recipe;
use DateTimeImmutable;
use App\Entity\Category;
use App\DataFixtures\UserFixtures;
use App\Entity\Ingredient;
use App\Entity\Quantity;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use FakerRestaurant\Provider\ar_SA\Restaurant;
use Symfony\Component\String\Slugger\SluggerInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class RecipeFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(private readonly SluggerInterface $slugger)
    {
        
    }
    public function load(ObjectManager $manager): void
    {
        //on appelle les bundle & bibliothèque tiers qui construisent de fausses données
        $faker = Factory::create('de_DE');
        $faker->addProvider(new Restaurant($faker));

        //on crée des ingrédients
        $ingredients = array_map(fn(string $name) => (new Ingredient)
        ->setName($name)
        ->setSlug(strtolower($this->slugger->slug($name))), [
            'farine', 
            'eau', 
            'épices', 
            'yaourt', 
            'sucre', 
            'huile', 
            'fruits secs', 
            'levure chimique',
            'pommes',
            'poires',
            'carottes',
            'chou ravi'
        ]);
        $units = [
            'g', 
            'mL', 
            'cuillère', 
            'pincée', 
            'verre'
        ];
        foreach ($ingredients as $ingredient) {
            $manager->persist($ingredient);
        }

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
            //le nom est celui de $c (un nom de catégorie) et l'objet est $category défini ci-dessus
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
                //on utilise la référence de la catégorie pour la rattacher à cette recette
                //faker prend un élément aléatoire dans le tableau des catégories et passe son nom à la référence
                ->setCategory($this->getReference($faker->randomElement($categories)))
                //on utilise la référence de l'utilisateur, définie dans la dépendance UserFixtures
                //faker prend un chiffre aléatoire et le concatène pour obtenir le nom
                ->setAuthor($this->getReference('USER' . $faker->numberBetween(1, 10)))
                ->setDuration($faker->numberBetween(2, 60));
            foreach($faker->randomElement($ingredients, $faker->numberBetween(2, 5)) as $ingredient) {
                $recipe->addQuantity((new Quantity)
                    ->setQuantity($faker->numberBetween(1, 250))
                    ->setUnit($faker->randomElement($units))
                    ->setIngredient($ingredient)
                );
            }
            $manager->persist($recipe);
        }

        $manager->flush();
    }


    /**
     * Fonction de DependentFixtureInterface
     *
     * @return array
     */
    public function getDependencies()
    {
        return [UserFixtures::class];
    }
}
