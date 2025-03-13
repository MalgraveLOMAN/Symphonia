<?php

namespace App\Repository;

use App\Entity\Artist;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Artist>
 */
class ArtistRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Artist::class);
    }
     public function findArtistsByEvent(int $eventId): array
    {
        return $this->createQueryBuilder('a')
            ->join('a.events', 'e')
            ->where('e.id = :eventId')
            ->setParameter('eventId', $eventId)
            ->getQuery()
            ->getResult();
    }

    public function findArtistById(int $id): ?Artist
    {
        return $this->find($id);
    }

    public function findByName(string $name): array
    {
        return $this->createQueryBuilder('a')
            ->where('LOWER(a.name) LIKE LOWER(:name)')
            ->setParameter('name', strtolower($name) . '%')
            ->getQuery()
            ->getResult();
    }

}