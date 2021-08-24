<?php

declare(strict_types=1);

namespace Tavy315\SyliusProductSubscriptionsPlugin\Repository;

use Doctrine\ORM\QueryBuilder;
use Sylius\Component\Resource\Repository\RepositoryInterface;

interface SubscriptionRepositoryInterface extends RepositoryInterface
{
    public function createByCustomerIdAndChannelIdQueryBuilder(int $customerId, int $channelId): QueryBuilder;
}
