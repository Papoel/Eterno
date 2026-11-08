<?php

namespace App\Repository;

use App\Entity\Light;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Light>
 *
 * @method Light|null find($id, $lockMode = null, $lockVersion = null)
 * @method Light|null findOneBy(array $criteria, array $orderBy = null)
 * @method Light[]    findAll()
 * @method Light[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LightRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Light::class);
    }

    /* Count Lights */
    public function countLights(): int
    {
        $result = $this->createQueryBuilder(alias: 'l')
            ->select('COUNT(l.id)')
            ->getQuery()
            ->getSingleScalarResult();

        if (!is_int($result)) {
            throw new \RuntimeException(message: 'Une erreur est survenue lors du comptage des lumières.');
        }

        return $result;
    }

    /**
     * Retourne toutes les Lumières avec leur propriétaire et leurs messages
     * chargés en une seule requête (évite le N+1).
     *
     * @return Light[]
     */
    public function findAllWithRelations(): array
    {
        return $this->createQueryBuilder(alias: 'l')
            ->addSelect('u', 'm')
            ->leftJoin('l.userAccount', 'u')
            ->leftJoin('l.messages', 'm')
            ->orderBy('l.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
