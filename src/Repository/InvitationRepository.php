<?php

namespace App\Repository;

use App\Entity\Invitation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Invitation>
 *
 * @method Invitation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Invitation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Invitation[]    findAll()
 * @method Invitation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InvitationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Invitation::class);
    }

    /**
     * @return Invitation[]
     */
    public function findExpiredInvitations(\DateTimeImmutable $oneMonthAgo): array
    {
        /* @phpstan-ignore-next-line */
        return $this->createQueryBuilder(alias: 'i')
            ->andWhere('i.accepted = :accepted')
            ->andWhere('i.createdAt < :oneMonthAgo')
            ->setParameter(key: 'accepted', value: false)
            ->setParameter(key: 'oneMonthAgo', value: $oneMonthAgo)
            ->getQuery()
            ->getResult();
    }

    /* Count total invitations accepted is true */

    public function countInvitationsAccepted(): int
    {
        $result = $this->createQueryBuilder(alias: 'i')
            ->select(select: 'COUNT(i.id)')
            ->andWhere('i.accepted = :accepted')
            ->setParameter(key: 'accepted', value: true)
            ->getQuery()
            ->getSingleScalarResult();

        if (!is_int($result)) {
            throw new \RuntimeException(message: 'Une erreur est survenue lors du comptage des invitations acceptées.');
        }

        return $result;
    }

    /* Count invitation not accepted */
    public function countInvitationsNotAccepted(): int
    {
        $result = $this->createQueryBuilder(alias: 'i')
            ->select(select: 'COUNT(i.id)')
            ->andWhere('i.accepted = :accepted')
            ->setParameter(key: 'accepted', value: false)
            ->getQuery()
            ->getSingleScalarResult();

        if (!is_int($result)) {
            throw new \RuntimeException(message: 'Une erreur est survenue lors du comptage des invitations non acceptées.');
        }

        return $result;
    }

    /**
     * @return array<int, array{invitationsSent: int|string, invitationsAccepted: int|string, userId: string}>
     */
    public function getUserInvitationStats(): array
    {
        return $this->createQueryBuilder(alias: 'i')
            ->select('COUNT(i.id) as invitationsSent', 'SUM(CASE WHEN i.accepted = true THEN 1 ELSE 0 END) as invitationsAccepted', 'u.id as userId')
            ->leftJoin(join: 'i.friend', alias: 'u')
            ->groupBy(groupBy: 'u.id')
            ->getQuery()
            ->getResult();
    }
}
