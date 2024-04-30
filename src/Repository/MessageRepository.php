<?php

namespace App\Repository;

use App\Entity\Message;
use App\Entity\User;
use DateTimeInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Func;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;
use Vtiful\Kernel\Format;

/**
 * @extends ServiceEntityRepository<Message>
 *
 * @method Message|null find($id, $lockMode = null, $lockVersion = null)
 * @method Message|null findOneBy(array $criteria, array $orderBy = null)
 * @method Message[]    findAll()
 * @method Message[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }

    /**
     * SELECT * FROM messages WHERE user_account_id = $user
     * AND light_id IN ($lightsIds).
     *
     * @param Uuid[] $lightsIds
     *
     * @return Message[]
     */
    public function findMessagesByUserAndLights(User $user, array $lightsIds): array
    {
        // 1. Get all messages from user (select * from messages where user_account_id = $user)
        $messages = $user->getMessages()->toArray();

        // from $messages, keep only messages where light_id is in $lightsIds
        $messages = array_filter($messages, callback: static function (Message $message) use ($lightsIds) {
            $lightId = $message->getLight()?->getId();

            if (null === $lightId) {
                return false;
            }

            /* @phpstan-ignore-next-line */
            return in_array($lightId->toRfc4122(), $lightsIds, strict: true);
        });

        return $messages;
    }

    public function countMessages(): int
    {
        $result = $this->createQueryBuilder(alias: 'm')
            ->select(select: 'COUNT(m.id)')
            ->getQuery()
            ->getSingleScalarResult();

        // Vérifier si le résultat est un entier, sinon retourner 0
        if (!is_int($result)) {
            throw new \RuntimeException(message: 'Une erreur est survenue lors du comptage des messages.');
        }

        return $result;
    }

    public function countMessagesByMonth(): array
    {
        $queryBuilder = $this->createQueryBuilder(alias: 't')
            ->select(select: 'SUBSTRING(t.createdAt, 6, 2) AS mois, COUNT(t) AS nombre_messages')
            ->where(predicates: 'SUBSTRING(t.createdAt, 1, 4) = :annee')
            ->setParameter(key: 'annee', value: date('Y'))
            ->groupBy(groupBy: 'mois')
            ->orderBy(sort: 'mois', order: 'ASC');

        $results = $queryBuilder->getQuery()->getResult();

        $message_mensuel = [];

        // Initialiser le tableau avec tous les mois de l'année en cours et un nombre de messages de zéro
        $mois_de_l_annee = [];
        for ($mois = 1; $mois <= 12; $mois++) {
            $mois_de_l_annee[$mois] = 0;
        }

        // Remplir le tableau avec les résultats de la requête
        foreach ($results as $result) {
            $mois = \DateTime::createFromFormat(format: '!m', datetime: $result['mois'])->format(format: 'F');
            $mois_de_l_annee[(int)$result['mois']] = $result['nombre_messages'];
        }

        // Formater les noms des mois en français
        foreach ($mois_de_l_annee as $mois => $nombre_messages) {
            $nom_mois = \DateTime::createFromFormat(format: '!m', datetime: $mois)->format(format: 'F');
            $message_mensuel[$nom_mois] = $nombre_messages;
        }

        return $message_mensuel;
    }


    /*public function countMessagesPerMonth(int $month): int
    {
        $results = $this->createQueryBuilder('m')
            ->select(select: 'm.createdAt')
            ->getQuery()
            ->getResult();

        $count = 0;

        foreach ($results as $result) {
            $createdAt = $result['createdAt'];
            $createdAtMonth = (int)$createdAt->format('m');

            if ($createdAtMonth === $month) {
                $count++;
            }
        }


        return $count;
    }*/



}
