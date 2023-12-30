<?php

namespace App\Repository;

use App\Entity\Message;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

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
     * @param int[] $lightsIds
     *
     * @return Message[]
     */
    public function findMessagesByUserAndLights(User $user, array $lightsIds): array
    {
        // 1. Get all messages from user (select * from messages where user_account_id = $user)
        $messages = $user->getMessages()->toArray();

        // from $messages, get messages to lights
        return array_filter(
            $messages,
            callback: static function (Message $message) use ($lightsIds) {
                $light = $message->getLight();
                if (null !== $light) {
                    return in_array(
                        needle: $light->getId(),
                        haystack: $lightsIds,
                        strict: true
                    );
                }

                return false;
                /*return in_array(
                    needle: $message->getLight()->getId(),
                    haystack: $lightsIds, strict: true
                );*/
            }
        );
    }
}
