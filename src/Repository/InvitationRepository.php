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
}
