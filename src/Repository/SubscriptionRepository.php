<?php

declare(strict_types=1);

namespace Tavy315\SyliusProductSubscriptionsPlugin\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Tavy315\SyliusProductSubscriptionsPlugin\Entity\Subscription;
use Tavy315\SyliusProductSubscriptionsPlugin\Entity\SubscriptionInterface;

final class SubscriptionRepository extends EntityRepository implements SubscriptionRepositoryInterface
{
    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct($entityManager, $entityManager->getClassMetadata(Subscription::class));
    }

    public function createByCustomerIdAndChannelIdQueryBuilder(int $customerId, int $channelId): QueryBuilder
    {
        return $this
            ->createQueryBuilder('subscription')
            ->andWhere('subscription.status = :status')
            ->andWhere('subscription.customer = :customerId')
            ->andWhere('subscription.channel = :channelId')
            ->setParameter('status', SubscriptionInterface::STATUS_NEW)
            ->setParameter('customerId', $customerId)
            ->setParameter('channelId', $channelId);
    }
}
