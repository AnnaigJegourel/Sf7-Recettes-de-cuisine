<?php

namespace App\DTO;

class CategoryWithCountDTO
{
    //constructeur avec seulement ce qui nous est nécessaire
    public function __construct(
        //readonly car initialisées que à la construction et jamais modifiées
        public readonly int $id,
        public readonly ?string $name,
        public readonly int $count
    )
    {
        
    }
}
