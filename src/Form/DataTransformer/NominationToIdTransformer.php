<?php

//This file is based on https://symfony.com/index.php/doc/current/form/data_transformers.html#example-2-transforming-an-issue-number-into-an-issue-entity
//which is licensed under a Creative Commons BY-SA 3.0 license (https://creativecommons.org/licenses/by-sa/3.0/)

namespace App\Form\DataTransformer;

use App\Entity\Nomination;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class NominationToIdTransformer implements DataTransformerInterface
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Transforms an object (nomination) to a string (number).
     *
     * @param  Nomination|null $nomination
     */
    public function transform($nomination): string
    {
        if (null === $nomination) {
            return '';
        }

        return $nomination->getId();
    }

    /**
     * Transforms a string (number) to an object (nomination).
     *
     * @param  string $nominationId
     * @throws TransformationFailedException if object (nomination) is not found.
     */
    public function reverseTransform($nominationId): ?Nomination
    {
        if (!$nominationId) {
            return null;
        }

        $nomination = $this->entityManager
            ->getRepository(Nomination::class)
            // query for the nomination with this id
            ->find($nominationId)
        ;

        if (null === $nomination) {
            throw new TransformationFailedException(sprintf(
                'A nomination with id "%s" does not exist!',
                $nominationId
            ));
        }

        return $nomination;
    }
}
