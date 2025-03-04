<?php

declare(strict_types=1);

namespace Tavy315\SyliusProductSubscriptionsPlugin\Command;

use Sylius\Component\Inventory\Checker\AvailabilityCheckerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tavy315\SyliusProductSubscriptionsPlugin\Entity\StatusAwareInterface;
use Tavy315\SyliusProductSubscriptionsPlugin\Repository\SubscriptionRepositoryInterface;
use Tavy315\SyliusProductSubscriptionsPlugin\Service\SubscriptionNotifierInterface;

final class SendNotificationsCommand extends Command
{
    protected static $defaultName = 'tavy315-product-subscriptions:send';

    public function __construct(
        private readonly AvailabilityCheckerInterface $availabilityChecker,
        private readonly SubscriptionNotifierInterface $subscriptionNotifier,
        private readonly SubscriptionRepositoryInterface $subscriptionRepository,
        string $name = null
    ) {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this->setDescription('Send an email to the user if the product is returned in stock');
        $this->setHelp('Check the stock status of the products in the tavy315_sylius_product_subscriptions table and send and email to the user if the product is back in stock');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $subscriptions = $this->subscriptionRepository->getNewSubscriptions();

        foreach ($subscriptions as $subscription) {
            $product = $subscription->getProduct();

            if ($product === null || $subscription->getChannel() === null) {
                continue;
            }

            if ($product->getEnabledVariants()->count() === 0) {
                continue;
            }

            if (!$this->availabilityChecker->isStockAvailable($product->getEnabledVariants()->first())) {
                continue;
            }

            $this->subscriptionNotifier->sendEmail($subscription, $product);

            $subscription->setStatus(StatusAwareInterface::STATUS_SENT);

            $this->subscriptionRepository->add($subscription);
        }

        return Command::SUCCESS;
    }
}
