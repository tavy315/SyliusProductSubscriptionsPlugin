<?php

declare(strict_types=1);

namespace Tavy315\SyliusProductSubscriptionsPlugin\Entity;

use Sylius\Component\Channel\Model\ChannelAwareInterface;
use Sylius\Component\Customer\Model\CustomerAwareInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;

interface SubscriptionInterface extends ResourceInterface, CustomerAwareInterface, ChannelAwareInterface, TimestampableInterface, ProductAwareInterface, StatusAwareInterface
{
    public function getLocaleCode(): ?string;

    public function setLocaleCode(string $localeCode): void;
}
