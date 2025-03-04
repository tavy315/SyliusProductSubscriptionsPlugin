<?php

declare(strict_types=1);

namespace Tavy315\SyliusProductSubscriptionsPlugin\EventSubscriber;

use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Core\Model\ProductVariant;
use Sylius\Component\Inventory\Checker\AvailabilityCheckerInterface;
use Sylius\Component\Product\Model\Product;
use Sylius\Component\Product\Model\ProductInterface;
use Tavy315\SyliusProductSubscriptionsPlugin\Entity\StatusAwareInterface;
use Tavy315\SyliusProductSubscriptionsPlugin\Repository\SubscriptionRepositoryInterface;
use Tavy315\SyliusProductSubscriptionsPlugin\Service\SubscriptionNotifierInterface;

final readonly class NotifierSubscriber
{
    public function __construct(
        private AvailabilityCheckerInterface $availabilityChecker,
        private SubscriptionNotifierInterface $subscriptionNotifier,
        private SubscriptionRepositoryInterface $subscriptionRepository
    ) {
    }

    public function productNotification(ResourceControllerEvent $event): void
    {
        /** @var Product $product */
        $product = $event->getSubject();

        if (!$product->isEnabled()) {
            return;
        }

        if ($product->getEnabledVariants()->count() === 0) {
            return;
        }

        $productVariant = $product->getEnabledVariants()->first();

        if (!$productVariant->isEnabled()) {
            return;
        }

        if (!$this->availabilityChecker->isStockAvailable($productVariant)) {
            return;
        }

        $this->sendProductNotification($product);
    }

    public function productVariantNotification(ResourceControllerEvent $event): void
    {
        /** @var ProductVariant $productVariant */
        $productVariant = $event->getSubject();

        if (!$this->availabilityChecker->isStockAvailable($productVariant)) {
            return;
        }

        $product = $productVariant->getProduct();

        if (!$product || !$product->isEnabled()) {
            return;
        }

        $this->sendProductNotification($product);
    }

    private function sendProductNotification(ProductInterface $product): void
    {
        $subscriptions = $this->subscriptionRepository->getNewProductSubscriptions($product);

        foreach ($subscriptions as $subscription) {
            $this->subscriptionNotifier->sendEmail($subscription, $product);

            $subscription->setStatus(StatusAwareInterface::STATUS_SENT);

            $this->subscriptionRepository->add($subscription);
        }
    }
}
