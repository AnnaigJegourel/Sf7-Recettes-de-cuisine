<?php

namespace App\MessageHandler;

use App\Message\RecipePDFMessage;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class RecipePDFMessageHandler
{
    public function __construct(
        #[Autowire('%kernel.projetc_dir%/public/pdfs')]
        private readonly string $path
    )
    {
        
    }


    public function __invoke(RecipePDFMessage $message)
    {
        //chemin d'enregistrement du pdf + contenu vide
        file_put_contents($this->path . '/' . $message->id . '.pdf', '');

        // do something with your message
    }
}
