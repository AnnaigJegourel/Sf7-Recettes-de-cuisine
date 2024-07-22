# Découvrir Symfony 7 (Grafikart)

[Playlist de tutoriels en libre accès](https://www.youtube.com/playlist?list=PLjwdMgw5TTLXuvlGqP18gbJCYVg7y6Fig) (les numéros des chapitres ci-dessous correspondent aux vidéos)

## Internationalisation

v v v v v v v v v   = 9 min

v v v v v v v v v   = 18 min

1 2 3 4 5 6 7 8 9   = 27 min

1 2 3 4 5 6         = 33 min

[Documentation](https://symfony.com/doc/current/translation.html)

Par défaut, le système va chercher des traductions pour les libellés.

### Générer les traductions

#### Traduction des libellés de formulaires

config/packages/translation.yaml pour piloter la traduction

- locale par défaut
- translator :

  - où sont les fichiers à utiliser (/translation),
  - le fallback,
  - les providers qui permettent d'utiliser des traductions automatiques.

créer les fichiers de traduction dans translation/s

à nommer en fonction du domaine associé (cf. Profiler > translation: messages pour les libellés ici)
<img src="Profiler1.png" alt="">
Par exemple, messages.fr.yaml (ou autre extension) = pour traduire en français

- directement le libellé (Prénom)
- ou sa variable défini dans le type (Email : label = contactForm.email, traduction 'courriel')

#### Traduction de chaînes de caractères plus "arbitraires" (texte d'accueil...)

Solution 1 : utiliser le TAG "trans" dans templates/home/index.html.twig et ajouter la traduction dans translations/.

```twig
    {% trans %}Welcome Home!{% endtrans %}
```

Solution 2 : utiliser le FILTRE "trans" dans templates/home/index.html.twig et ajouter la traduction dans translations/.

```twig
    {{ 'Nice to see you.' | trans }}
```

Solution 3 : Injecter TranslatorInterface au niveau du contrôleur
Puis utiliser la méthode trans('IDduMessage'). ---BUG ?!---

#### Générer les traductions de manière dynamique

```bash
php bin/console translation:extract --dump-messages fr
```

Il scanne tout le code source et génère toutes les clés de traductions (validation, etc.).
On peut lui demander de l'extraire dans un fichier yaml (sera prérempli par Symfony) :

```bash
php bin/console translation:extract --force fr --format=yaml
```

Crée un fichier par "domaine" : validators, security... mais n'a pas été capable de scanner les formulaires car ce sont de simples chaînes de caractères.`

On peut y remédier en utilisant la fonction Symfony globale t() dans les libellés de formulaires : cela crée un objet TranslatableMessage qui sera reconnu et extrait dans les yaml :

```php
'label' => t('contactForm.submit')
```

Il est recommandé d'utiliser cette fonction. Néanmoins elle a des bugs dans les version de Symfony 7.0 et 7.1, cela devrait sans doute être résolu dans la future version stable 7.4

#### Fichier intl-icu

cf. [Documentation Sf/icu](https://symfony.com/doc/current/reference/formats/message_format.html)

Généré par la commande ci-dessus parfois, permet de gérer un format supplémentaire :

- Traduction du singulier et du pluriel si j'ai un nombre variable.
- Traduction des genres

Dans le template :

```twig
{{ "home_recipe_count" | trans({count : 100}) }}
````

Dans le fichier intl-icu :

```php
home_recipe_count: >-
  {count, plural,
    =0    {Aucune recette}
    =1    {Une recette}
    other {# recettes}
  }
```

- cas où je veux mettre une info supplémentaire (ici le nom) :

Dans le template :

```twig
{{ 'Nice to see you' | trans({name: 'Jane'}) }}
````

Dans le fichier "normal" des traductions :

```php
'Nice to see you ': 'ça fait plaiz, name'
```

### Paramétrer le changement de langue

#### 1er cas : changer en fonction d'une information utilisateur

Ajouter la propriété $locale à l'entité User :

- la définir à 'fr' par défaut
- ajouter une option pour que 'fr' par défaut soit en bdd aussi

Utiliser le service LocaleSwitcher :

- choisir à quel moment; ici : lorsque la requête rentre
- créer le UserLocaleListener, rattaché à kernel.request
- le customiser avec un constructeur injectant le composant Security & LocaleSwitcher (du composant Translation)
- dans onKernelRequest(), récupérer l'utilisateur avec Security
- ...et changer la locale de l'utilisateur avec setLocale() de LocaleSwitcher
