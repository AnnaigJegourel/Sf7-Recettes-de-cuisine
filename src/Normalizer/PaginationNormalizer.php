<?php

namespace App\Normalizer;

use App\Entity\Recipe;
use ArrayObject;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Symfony\Component\Serializer\Exception\RuntimeException;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class PaginationNormalizer implements NormalizerInterface
{
    public function __construct(
        #[Autowire(service: 'serializer.normalizer.object')]
        private readonly NormalizerInterface $normalizer
        )
    {
        
    }
    /**
     * explique comment normaliser l'objet
     *
     * @param mixed $object
     * @param string|null $format
     * @param array $context
     * @return array|string|integer|float|boolean|ArrayObject|null
     */
    public function normalize(mixed $object, ?string $format = null, array $context = []): array|string|int|float|bool|ArrayObject|null
    {
        if(!($object instanceof PaginationInterface)) {
            throw new RuntimeException();
        }

        return [
            'items' => array_map(fn (Recipe $recipe) => $this->normalizer->normalize($recipe, $format, $context), $object->getItems()),
            'total' => $object->getTotalItemCount(),
            'page' => $object->getCurrentPageNumber(),
            'lastPage' => ceil($object->getTotalItemCount() / $object->getItemNumberPerPage())
        ];
    }


    /**
     * dit si oui ou non il doit agir sur l'objet reçu
     * oui si l'objet est une instance de PI & si le format demandé est json
     *
     * @param mixed $data
     * @param string|null $format
     * @param array $context
     * @return boolean
     */
    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof PaginationInterface && $format === 'json';
    }


    /**
     * prend un format & dit quels types d'objets peuvent être sérialisés
     * ce normaliseur ne peut être déclenché que par qqch qui implémente la PaginationInterface
     *
     * @param string|null $format
     * @return array
     */
    public function getSupportedTypes(?string $format): array
    {
        return [
            PaginationInterface::class => true
        ];
    }
}