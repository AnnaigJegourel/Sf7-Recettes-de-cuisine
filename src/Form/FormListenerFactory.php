<?php

namespace App\Form;

use DateTimeImmutable;
use Symfony\Component\Form\Event\PreSubmitEvent;
use Symfony\Component\Form\Event\PostSubmitEvent;
use Symfony\Component\String\Slugger\AsciiSlugger;

class FormListenerFactory
{

    //fonction qui génère un callable = retourne une fonction
    public function autoSlug(string $field): callable
    {
        // n'a pas accès aux varaibles ext, d'où le use
        return function (PreSubmitEvent $event) use ($field)
        {
            $data = $event->getData();
            if(empty($data['slug'])) {
                //je pourrais aussi créer en service en utilisant SluggerInterface
                $slugger = new AsciiSlugger();
                $data['slug'] = strtolower($slugger->slug($data[$field]));
                $event->setData($data);
            }
        };
    }

    public function timestamps(): callable
    {
        return function (PostSubmitEvent $event)
        {
            $data = $event->getData();

            $data->setUpdatedAt(new DateTimeImmutable());
            //si nouvelle entité
            if(!($data->getId())) {
                $data->setCreatedAt(new DateTimeImmutable());
            }
        };
    }
}