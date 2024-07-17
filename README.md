# Sf7-Recettes-de-cuisine

Découverte de Symfony 7 en suivant le tuto / la formation YouTube de Grafikart

<https://www.youtube.com/watch?v=1Fz6-Gkou-U&list=PLjwdMgw5TTLXuvlGqP18gbJCYVg7y6Fig&index=4>

Les différents thèmes correspondent aux différentes pull requests (sauf les 2 premiers).
Voir le détail des commit constituant chaque pull request : sur la branche dev.

Voir le dossier [documentation](documentation) pour plus de détails.

## Configuration / Technologies ⚙️

Apache  
MySQL / MariaDB  
PHP >= 8.2  
Symfony 7.1

## Installation 🧑🏻‍🔧

### Importing the repository

1. Clone the repository to work on your localhost.
2. To install the dependencies, run the following command at the root of the project:

    ````text
    composer intall
    ````

### Configuring the database

3. Launch xamppserver, configure your php version.
4. Configure .env.local : Database & DSN
5. Create the database running:

    ````text
    php bin/console doctrine:database:create
    ````

6. Import its structure using this command:

    ````text
    php bin/console doctrine:schema:update --force
    ````

7. You can also load the fixtures as initial set of data:

    ````text
    php bin/console doctrine:fixtures:load
    ````

### Launching the project

8. Launch the Symfony server running:

    ````text
    symfony server
    ````

🎉 Congrats! You can now watch at the project in your navigator following the link given in your terminal, mostly:
<https://localhost:8000/>

You can also connect with ADMIN ROLE using usernam: "admin" and password: "admin" (see Fixtures files in src).

## Liens intéressants

### Twig

<https://twig.symfony.com/doc/3.x/>

<https://symfony.com/doc/current/reference/configuration/twig.html>

### Symfony

<https://symfony.com/doc/current/reference>

#### Forms

<https://symfony.com/doc/current/reference/forms/types/form.html#validation-groups>

<https://symfony.com/doc/current/reference/forms/types/entity.html>

<https://symfony.com/doc/current/reference/forms/types/entity.html#by-reference>

#### E-mails

<https://symfony.com/doc/current/reference/constraints/Email.html>

<https://symfony.com/doc/current/mailer.html>

### Regex

<https://ihateregex.io/>

<https://symfony.com/doc/current/reference/constraints/Regex.html>

### Doctrine

<https://www.doctrine-project.org/projects/doctrine-orm/en/3.2/reference/working-with-associations.html#transitive-persistence-cascade-operations>

## Thèmes

1. Découverte
2. Installation
3. Premières pages & contrôleures
4. Twig + Boostrap
5. Doctrine
6. Formulaires
7. Validation
8. Services
9. TP Formulaire de contact
10. TP Catégories
11. ORM Many to One
12. Envoi de fichiers
13. Composant [Security](https://symfony.com/doc/current/security.html)
14. Pagination:

    - [Doctrine Paginator](https://www.doctrine-project.org/projects/doctrine-orm/en/3.2/tutorials/pagination.html)
    - [KnpPaginatorBundle](https://github.com/KnpLabs/KnpPaginatorBundle)

15. Doctrine avancé
16. [AssetMapper](https://symfony.com/doc/current/frontend/asset_mapper.html)
17. API 1: [Serializer](https://symfony.com/doc/current/components/serializer.html)
18. API 2 : Désérialisation & MapRequestPayLoad
19. API 3 : Authenticator stateless
20. Fixtures :

    - [DoctrineFixturesBundle](https://symfony.com/bundles/DoctrineFixturesBundle/current/index.html)
    - [Faker](https://fakerphp.org/)

21. [Voters](https://symfony.com/doc/current/security/voters.html)
22. [Evénements](https://symfony.com/doc/current/event_dispatcher.html)
23. [Symfony UX](https://ux.symfony.com/)

## TO DO

Chapitres à voir :

- Formulaires imbriqués
- Messenger

- Internationalisation
- Hébergement Infomaniak
- Hébergement O2Switch

à corriger:

- RecipeController l.60: getId()  n'existe pas (mais marche quand même?!)
- Fixtures : titres en persan !? (voir doc faker & fakerrestaurant : versions ? paramétrage de la locale?)
- EventListener : quand le DogCatEventListener est activé, plus rien d'autre ne s'affiche que "Dog" !
- EventSubscriber / Mailer :

  - les e-mails ne sont pas envoyés? tester à SB (en fait l'événement ne semble pas envoyé ou le subscriber ne le capte pas)
  - sur onLogin : erreur

    ``Expected response code "250/251/252" but got code "554", with message "554 5.7.1 <admin@doe.fr>: Relay access denied".``

- turbo-frame / affichage de l'édition de recettes
