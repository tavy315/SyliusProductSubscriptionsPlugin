<?php

declare(strict_types=1);

namespace Tavy315\SyliusProductSubscriptionsPlugin\EventSubscriber;

use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Core\Model\ProductVariant;
use Sylius\Component\Product\Model\Product;
use Sylius\Component\Product\Model\ProductInterface;
use Tavy315\SyliusProductSubscriptionsPlugin\Entity\SubscriptionInterface;
use Tavy315\SyliusProductSubscriptionsPlugin\Repository\SubscriptionRepositoryInterface;
use Tavy315\SyliusProductSubscriptionsPlugin\Service\SubscriptionNotifier;

final class NotifierSubscriber
{
    private SubscriptionNotifier $subscriptionNotifier;

    private SubscriptionRepositoryInterface $repository;

    public function __construct(SubscriptionRepositoryInterface $repository, SubscriptionNotifier $subscriptionNotifier)
    {
        $this->repository = $repository;
        $this->subscriptionNotifier = $subscriptionNotifier;
    }

    public function productNotification(ResourceControllerEvent $event): void
    {
        /** @var Product $product */
        $product = $event->getSubject();

        if ($product->getEnabledVariants()->count() === 0) {
            return;
        }

        if (!$product->getEnabledVariants()->first()->isInStock()) {
            return;
        }

        $this->sendProductNotification($product);
    }

    public function productVariantNotification(ResourceControllerEvent $event): void
    {
        /** @var ProductVariant $productVariant */
        $productVariant = $event->getSubject();

        if (!$productVariant->isInStock()) {
            return;
        }

        $this->sendProductNotification($productVariant->getProduct());
    }

    private function sendProductNotification(ProductInterface $product): void
    {
        $subscriptions = $this->repository->findBy([
            'product' => $product,
            'status'  => SubscriptionInterface::STATUS_NEW,
        ]);

        foreach ($subscriptions as $subscription) {
            $this->subscriptionNotifier->sendEmail($subscription, $product);

            $subscription->setStatus(SubscriptionInterface::STATUS_SENT);

            $this->repository->add($subscription);
        }
    }
}
