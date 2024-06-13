<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class BanWord extends Constraint
{
    //constructeur personnalisé
    public function __construct(
        public string $message = 'This contains a banned word: "{{ banWord }}".',
        public array $banWords = ['spam', 'viagra'],
        // arguments du constructeur parent
        //    - groups permet de regrouper certaines règles de contraintes
        array $groups = null,
        mixed $payload = null
        )
    {
        //Appel du constructeur parent
        parent::__construct(null, $groups, $payload);
    }
    //liste des mots à bannir (sans fonction _construc)
    //public $banWords = ['spam', 'viagra'];

    /*
        * Any public properties become valid options for the annotation.
        * Then, use these in your validator class.
        */

    // fonction par défaut
    //public $message = 'The value "{{ value }}" is not valid.';
}
