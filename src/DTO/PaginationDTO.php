<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class PaginationDTO
{
    public function __construct(
        //= je m’attends à avoir une propriété publique « page », entier ou nul, en readonly (non modifiable une fois construite)
        #[Assert\Positive()]
        public readonly ?int $page = 1
    )
    {
        
    }
}