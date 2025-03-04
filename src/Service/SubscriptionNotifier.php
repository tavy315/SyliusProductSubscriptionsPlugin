<?php

declare(strict_types=1);

namespace Tavy315\SyliusProductSubscriptionsPlugin\Service;

use Sylius\Component\Mailer\Sender\SenderInterface;
use Sylius\Component\Product\Model\ProductInterface;
use Tavy315\SyliusProductSubscriptionsPlugin\Entity\SubscriptionInterface;

final readonly class SubscriptionNotifier implements SubscriptionNotifierInterface
{
    public function __construct(private SenderInterface $sender)
    {
    }

    public function sendEmail(SubscriptionInterface $subscription, ProductInterface $product): void
    {
        $customer = $subscription->getCustomer();

        if (!$customer || !$customer->getEmail()) {
            return;
        }

        $this->sender->send('product_subscription', [ $customer->getEmail() ], [
            'channel'      => $subscription->getChannel(),
            'localeCode'   => $subscription->getLocaleCode(),
            'product'      => $product,
            'subscription' => $subscription,
        ]);
    }
}
