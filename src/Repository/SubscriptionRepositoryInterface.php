<?php

declare(strict_types=1);

namespace Tavy315\SyliusProductSubscriptionsPlugin\Repository;

use Doctrine\ORM\QueryBuilder;
use Sylius\Component\Customer\Model\CustomerInterface;
use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Tavy315\SyliusProductSubscriptionsPlugin\Entity\SubscriptionInterface;

interface SubscriptionRepositoryInterface extends RepositoryInterface
{
    public function createByCustomerIdAndChannelIdQueryBuilder(int $customerId, int $channelId): QueryBuilder;

    /**
     * @return SubscriptionInterface[]
     */
    public function getNewProductSubscriptions(ProductInterface $product): array;

    public function getNewSubscriptionByCustomerAndProduct(
        CustomerInterface $customer,
        ProductInterface  $product
    ): ?SubscriptionInterface;

    /**
     * @return SubscriptionInterface[]
     */
    public function getNewSubscriptions(): array;
}
