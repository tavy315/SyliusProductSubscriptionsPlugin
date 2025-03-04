<?php

declare(strict_types=1);

namespace Tavy315\SyliusProductSubscriptionsPlugin\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Customer\Model\CustomerInterface;
use Sylius\Component\Product\Model\ProductInterface;
use Tavy315\SyliusProductSubscriptionsPlugin\Entity\StatusAwareInterface;
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
            ->setParameter('status', StatusAwareInterface::STATUS_NEW)
            ->setParameter('customerId', $customerId)
            ->setParameter('channelId', $channelId);
    }

    /**
     * @return SubscriptionInterface[]
     */
    public function getNewProductSubscriptions(ProductInterface $product): array
    {
        return $this->findBy([
            'product' => $product,
            'status'  => StatusAwareInterface::STATUS_NEW,
        ]);
    }

    public function getNewSubscriptionByCustomerAndProduct(
        CustomerInterface $customer,
        ProductInterface  $product
    ): ?SubscriptionInterface {
        return $this->findOneBy([
            'customer' => $customer,
            'product'  => $product,
            'status'   => StatusAwareInterface::STATUS_NEW,
        ]);
    }

    /**
     * @return SubscriptionInterface[]
     */
    public function getNewSubscriptions(): array
    {
        return $this->findBy([
            'status' => StatusAwareInterface::STATUS_NEW,
        ]);
    }
}
