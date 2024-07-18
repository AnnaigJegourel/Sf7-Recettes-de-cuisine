<?php

namespace App\MessageHandler;

use Error;
use App\Message\RecipePDFMessage;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

#[AsMessageHandler]
final class RecipePDFMessageHandler
{
    public function __construct(
        #[Autowire('%kernel.project_dir%/public/pdfs')]
        private readonly string $path
    )
    {
        
    }


    public function __invoke(RecipePDFMessage $message)
    {
        throw new Error('errreeuuurrr!!!');

        //chemin d'enregistrement du pdf + contenu vide
        file_put_contents($this->path . '/' . $message->id . '.pdf', '');

        // do something with your message
    }
}
