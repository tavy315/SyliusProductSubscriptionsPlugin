<?php

declare(strict_types=1);

namespace Tavy315\SyliusProductSubscriptionsPlugin\Menu;

use Sylius\Bundle\UiBundle\Menu\Event\MenuBuilderEvent;

final class AccountMenuListener
{
    public function addMenuItems(MenuBuilderEvent $event): void
    {
        $event->getMenu()
              ->addChild('customer_product_subscriptions', [ 'route' => 'tavy315_sylius_product_subscriptions_shop_account_subscription_index' ])
              ->setLabel('tavy315_sylius_product_subscriptions.my_account_section.menu_label')
              ->setLabelAttribute('icon', 'bell');
    }
}
