<?php

declare(strict_types=1);

namespace Tavy315\SyliusProductSubscriptionsPlugin\Controller;

use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Customer\Context\CustomerContextInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tavy315\SyliusProductSubscriptionsPlugin\Entity\SubscriptionInterface;
use Tavy315\SyliusProductSubscriptionsPlugin\Repository\SubscriptionRepositoryInterface;

final class DeleteSubscriptionAction
{
    private CustomerContextInterface $customerContext;

    private ProductRepositoryInterface $productRepository;

    private RouterInterface $router;

    private SessionInterface $session;

    private SubscriptionRepositoryInterface $repository;

    private TranslatorInterface $translator;

    public function __construct(
        CustomerContextInterface        $customerContext,
        ProductRepositoryInterface      $productRepository,
        RouterInterface                 $router,
        SessionInterface                $session,
        SubscriptionRepositoryInterface $subscriptionRepository,
        TranslatorInterface             $translator
    ) {
        $this->customerContext = $customerContext;
        $this->productRepository = $productRepository;
        $this->repository = $subscriptionRepository;
        $this->router = $router;
        $this->session = $session;
        $this->translator = $translator;
    }

    public function __invoke(Request $request, string $productCode): Response
    {
        $product = $this->productRepository->findOneBy([ 'code' => $productCode ]);

        if (!$product instanceof ProductInterface) {
            $this->session->getFlashBag()->add('error', $this->translator->trans('tavy315_sylius_product_subscriptions.form.subscription_not_found'));

            return new RedirectResponse($this->getRefererUrl($request), 302);
        }

        /** @var SubscriptionInterface|null $subscription */
        $subscription = $this->repository->findOneBy([
            'customer' => $this->customerContext->getCustomer(),
            'product'  => $product,
        ]);

        if ($subscription === null) {
            $this->session->getFlashBag()->add('error', $this->translator->trans('tavy315_sylius_product_subscriptions.form.subscription_not_found'));

            return new RedirectResponse($this->getRefererUrl($request), 302);
        }

        $subscription->setStatus(SubscriptionInterface::STATUS_DELETED);

        $this->repository->add($subscription);

        $this->session->getFlashBag()->add('info', $this->translator->trans('tavy315_sylius_product_subscriptions.form.subscription_deleted'));

        return new RedirectResponse($this->getRefererUrl($request), 302);
    }

    private function getRefererUrl(Request $request): string
    {
        $referer = $request->headers->get('referer');

        return !is_string($referer) ? $this->router->generate('sylius_shop_homepage', [], UrlGeneratorInterface::ABSOLUTE_PATH) : $referer;
    }
}
