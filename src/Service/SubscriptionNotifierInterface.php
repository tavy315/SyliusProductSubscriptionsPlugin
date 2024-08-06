<?php

declare(strict_types=1);

namespace Tavy315\SyliusProductSubscriptionsPlugin\Service;

use Sylius\Component\Product\Model\ProductInterface;
use Tavy315\SyliusProductSubscriptionsPlugin\Entity\SubscriptionInterface;

interface SubscriptionNotifierInterface
{
    public function sendEmail(SubscriptionInterface $subscription, ProductInterface $product): void;
}
