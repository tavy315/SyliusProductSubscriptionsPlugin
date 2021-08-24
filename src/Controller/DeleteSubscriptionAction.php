<?php

declare(strict_types=1);

namespace Tavy315\SyliusProductSubscriptionsPlugin\Controller;

use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Customer\Context\CustomerContextInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tavy315\SyliusProductSubscriptionsPlugin\Entity\SubscriptionInterface;
use Tavy315\SyliusProductSubscriptionsPlugin\Repository\SubscriptionRepositoryInterface;

final class DeleteSubscriptionAction extends AbstractController
{
    private CustomerContextInterface $customerContext;

    private ProductRepositoryInterface $productRepository;

    private SubscriptionRepositoryInterface $repository;

    private TranslatorInterface $translator;

    public function __construct(
        CustomerContextInterface        $customerContext,
        ProductRepositoryInterface      $productRepository,
        SubscriptionRepositoryInterface $subscriptionRepository,
        TranslatorInterface             $translator
    ) {
        $this->customerContext = $customerContext;
        $this->productRepository = $productRepository;
        $this->repository = $subscriptionRepository;
        $this->translator = $translator;
    }

    public function __invoke(Request $request, string $productCode): Response
    {
        $product = $this->productRepository->findOneBy([ 'code' => $productCode ]);

        if (!$product instanceof ProductInterface) {
            $this->addFlash('error', $this->translator->trans('tavy315_sylius_product_subscriptions.form.subscription_not_found'));

            return $this->redirect($this->getRefererUrl($request));
        }

        /** @var SubscriptionInterface|null $subscription */
        $subscription = $this->repository->findOneBy([
            'customer' => $this->customerContext->getCustomer(),
            'product'  => $product,
        ]);

        if ($subscription === null) {
            $this->addFlash('error', $this->translator->trans('tavy315_sylius_product_subscriptions.form.subscription_not_found'));

            return $this->redirect($this->getRefererUrl($request));
        }

        $subscription->setStatus(SubscriptionInterface::STATUS_DELETED);

        $this->repository->add($subscription);

        $this->addFlash('info', $this->translator->trans('tavy315_sylius_product_subscriptions.form.subscription_deleted'));

        return $this->redirect($this->getRefererUrl($request));
    }

    private function getRefererUrl(Request $request): string
    {
        $referer = $request->headers->get('referer');

        return !is_string($referer) ? $this->generateUrl('sylius_shop_homepage') : $referer;
    }
}
