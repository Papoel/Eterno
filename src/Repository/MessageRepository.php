<?php

namespace App\Repository;

use App\Entity\Message;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

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
}
