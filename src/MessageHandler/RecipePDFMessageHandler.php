<?php

namespace App\MessageHandler;

use Error;
use App\Message\RecipePDFMessage;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[AsMessageHandler]
final class RecipePDFMessageHandler
{
    public function __construct(
        #[Autowire('%kernel.project_dir%/public/pdfs')]
        private readonly string $path,
        private readonly UrlGeneratorInterface $urlGenerator
    )
    {
        
    }


    public function __invoke(RecipePDFMessage $message) : void
    {
        throw new Error('errreeuuurrr!!!');

        //pour convertir en pdf avec le serveur Gotenberg
        $process = new Process([
            'curl',
            '--request',
            'POST',
            'http://localhost:800/forms/chormium/convert/url',  //url du serveur Gotenborg
            '--form',   
            'url=' . $this->urlGenerator->generate('recipe.show', ['id' => $message->id], UrlGeneratorInterface::ABSOLUTE_URL), //url à contacter
            '-o',
            $this->path . '/' . $message->id . '.pdf', ''
        ]);

        $process->run();
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        //chemin d'enregistrement du pdf + contenu vide
        file_put_contents($this->path . '/' . $message->id . '.pdf', '');

        // do something with your message
    }
}
