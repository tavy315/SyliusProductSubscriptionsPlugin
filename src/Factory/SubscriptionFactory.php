<?php

declare(strict_types=1);

namespace Tavy315\SyliusProductSubscriptionsPlugin\Factory;

use Sylius\Component\Resource\Factory\FactoryInterface;
use Tavy315\SyliusProductSubscriptionsPlugin\Entity\SubscriptionInterface;

final class SubscriptionFactory implements SubscriptionFactoryInterface
{
    /** @var FactoryInterface */
    private $subscriptionFactory;

    public function __construct(FactoryInterface $subscriptionFactory)
    {
        $this->subscriptionFactory = $subscriptionFactory;
    }

    public function createNew(): SubscriptionInterface
    {
        /** @var SubscriptionInterface $subscription */
        $subscription = $this->subscriptionFactory->createNew();

        return $subscription;
    }
}
