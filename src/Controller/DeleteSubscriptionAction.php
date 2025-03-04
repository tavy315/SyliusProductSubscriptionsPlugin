<?php

declare(strict_types=1);

namespace Tavy315\SyliusProductSubscriptionsPlugin\Controller;

use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Customer\Context\CustomerContextInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tavy315\SyliusProductSubscriptionsPlugin\Entity\StatusAwareInterface;
use Tavy315\SyliusProductSubscriptionsPlugin\Repository\SubscriptionRepositoryInterface;

final readonly class DeleteSubscriptionAction
{
    public function __construct(
        private CustomerContextInterface        $customerContext,
        private ProductRepositoryInterface      $productRepository,
        private RouterInterface                 $router,
        private RequestStack                    $requestStack,
        private SubscriptionRepositoryInterface $repository,
        private TranslatorInterface             $translator
    ) {
    }

    public function __invoke(Request $request, string $productCode): Response
    {
        $session = $this->requestStack->getSession();

        $product = $this->productRepository->findOneBy([ 'code' => $productCode ]);

        if (!$product instanceof ProductInterface) {
            $session->getFlashBag()->add(
                'error',
                $this->translator->trans('tavy315_sylius_product_subscriptions.form.subscription_not_found')
            );

            return new RedirectResponse($this->getRefererUrl($request), 302);
        }

        $subscription = $this->repository->getNewSubscriptionByCustomerAndProduct(
            $this->customerContext->getCustomer(),
            $product
        );

        if ($subscription === null) {
            $session->getFlashBag()->add(
                'error',
                $this->translator->trans('tavy315_sylius_product_subscriptions.form.subscription_not_found')
            );

            return new RedirectResponse($this->getRefererUrl($request), 302);
        }

        $subscription->setStatus(StatusAwareInterface::STATUS_DELETED);

        $this->repository->add($subscription);

        $session->getFlashBag()->add(
            'info',
            $this->translator->trans('tavy315_sylius_product_subscriptions.form.subscription_deleted')
        );

        return new RedirectResponse($this->getRefererUrl($request), 302);
    }

    private function getRefererUrl(Request $request): string
    {
        $referer = $request->headers->get('referer');

        return !is_string($referer)
            ? $this->router->generate('sylius_shop_homepage', [], UrlGeneratorInterface::ABSOLUTE_PATH)
            : $referer;
    }
}
