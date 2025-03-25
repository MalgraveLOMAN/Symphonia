<?php

namespace App\Repository;

use App\Entity\Event;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Constraints\Date;

/**
 * @extends ServiceEntityRepository<Event>
 */
class EventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }
        public function findEventById(int $id): ?Event
        {
            return $this->find($id);
        }

        public function findEventWithDetails(int $id): ?Event
        {
            return $this->createQueryBuilder('e')
                ->leftJoin('e.artists', 'a')
                ->addSelect('a')
                ->leftJoin('e.organizer', 'o')
                ->addSelect('o')
                ->leftJoin('e.participants', 'p')
                ->addSelect('p')
                ->where('e.id = :id')
                ->setParameter('id', $id)
                ->getQuery()
                ->getOneOrNullResult();
        }

        public function findEventsByParticipant(User $user): array
        {
            return $this->createQueryBuilder('e')
                ->join('e.participants', 'p')
                ->where('p.id = :userId')
                ->setParameter('userId', $user->getId())
                ->getQuery()
                ->getResult();
        }

        public function findParticipantsByEvent(int $eventId): array
        {
            return $this->createQueryBuilder('e')
                ->select('u')
                ->join('e.participants', 'u')
                ->where('e.id = :eventId')
                ->setParameter('eventId', $eventId)
                ->getQuery()
                ->getResult();
        }

        public function findEventsByOrganizer(User $user): array
        {
            return $this->createQueryBuilder('e')
                ->where('e.organizer = :organizerId')
                ->setParameter('organizerId', $user->getId())
                ->getQuery()
                ->getResult();
        }

        public function findUpcomingEvents(): array
        {
            return $this->createQueryBuilder('e')
                ->where('e.date >= :now')
                ->setParameter('now', new \DateTime())
                ->orderBy('e.date', 'ASC')
                ->getQuery()
                ->getResult();
        }

    public function findByDate($date): array
    {
        return $this->createQueryBuilder('e')
            ->where('e.date >= :date')
            ->setParameter('date', $date)
            ->getQuery()
            ->getResult();
    }

}