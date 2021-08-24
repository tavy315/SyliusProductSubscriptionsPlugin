<?php

declare(strict_types=1);

namespace Tavy315\SyliusProductSubscriptionsPlugin\Controller;

use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\Customer;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Customer\Context\CustomerContextInterface;
use Sylius\Component\Inventory\Checker\AvailabilityCheckerInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tavy315\SyliusProductSubscriptionsPlugin\Entity\SubscriptionInterface;
use Tavy315\SyliusProductSubscriptionsPlugin\Factory\SubscriptionFactoryInterface;
use Tavy315\SyliusProductSubscriptionsPlugin\Form\SubscriptionType;
use Tavy315\SyliusProductSubscriptionsPlugin\Repository\SubscriptionRepositoryInterface;

final class AddSubscriptionAction extends AbstractController
{
    private AvailabilityCheckerInterface $availabilityChecker;

    private ChannelContextInterface $channelContext;

    private CustomerContextInterface $customerContext;

    private CustomerRepositoryInterface $customerRepository;

    private FactoryInterface $customerFactory;

    private SubscriptionFactoryInterface $factory;

    private LocaleContextInterface $localeContext;

    private ProductRepositoryInterface $productRepository;

    private SubscriptionRepositoryInterface $repository;

    private TranslatorInterface $translator;

    private ValidatorInterface $validator;

    public function __construct(
        AvailabilityCheckerInterface    $availabilityChecker,
        ChannelContextInterface         $channelContext,
        CustomerContextInterface        $customerContext,
        CustomerRepositoryInterface     $customerRepository,
        FactoryInterface                $customerFactory,
        SubscriptionFactoryInterface    $factory,
        LocaleContextInterface          $localeContext,
        ProductRepositoryInterface      $productRepository,
        SubscriptionRepositoryInterface $repository,
        TranslatorInterface             $translator,
        ValidatorInterface              $validator
    ) {
        $this->availabilityChecker = $availabilityChecker;
        $this->channelContext = $channelContext;
        $this->customerContext = $customerContext;
        $this->customerRepository = $customerRepository;
        $this->customerFactory = $customerFactory;
        $this->factory = $factory;
        $this->localeContext = $localeContext;
        $this->productRepository = $productRepository;
        $this->repository = $repository;
        $this->translator = $translator;
        $this->validator = $validator;
    }

    public function __invoke(Request $request, string $productCode): Response
    {
        $product = $this->productRepository->findOneBy([ 'code' => $productCode ]);
        if (!$product instanceof ProductInterface) {
            throw new NotFoundHttpException();
        }

        if ($this->availabilityChecker->isStockAvailable($product->getEnabledVariants()->first())) {
            return new JsonResponse([ 'error' => $this->translator->trans('tavy315_sylius_product_subscriptions.form.product_in_stock') ], 400);
        }

        $form = $this->createForm(SubscriptionType::class);

        $customer = $this->customerContext->getCustomer();
        if ($customer !== null && $customer->getEmail() !== null) {
            $form->remove('email');
        }

        $form->handleRequest($request);

        if (!$form->isSubmitted() || !$form->isValid()) {
            return new JsonResponse([ 'error' => $this->translator->trans('tavy315_sylius_product_subscriptions.form.invalid_form') ], 400);
        }

        $data = $form->getData();

        /** @var SubscriptionInterface $subscription */
        $subscription = $this->factory->createNew();
        $subscription->setProduct($product);
        $subscription->setCustomer($customer);

        if (array_key_exists('email', $data)) {
            $errors = $this->validator->validate((string) $data['email'], [ new Email(), new NotBlank() ]);
            if (count($errors) > 0) {
                return new JsonResponse([ 'error' => $this->translator->trans('tavy315_sylius_product_subscriptions.form.invalid_email') ], 400);
            }

            $subscription->setCustomer($this->getCustomerByEmail((string) $data['email']));
        }

        $this->updateSubscription($subscription);

        return new JsonResponse([
            'notification' => $this->renderView('@Tavy315SyliusProductSubscriptionsPlugin/_notification.html.twig'),
            'success'      => $this->renderView('@Tavy315SyliusProductSubscriptionsPlugin/_success.html.twig'),
        ], Response::HTTP_CREATED);
    }

    private function getCustomerByEmail(string $email): Customer
    {
        $customer = $this->customerRepository->findOneBy([ 'email' => $email ]);

        if ($customer === null) {
            /** @var Customer $customer */
            $customer = $this->customerFactory->createNew();
            $customer->setEmail($email);

            $this->customerRepository->add($customer);
        }

        return $customer;
    }

    private function updateSubscription(SubscriptionInterface $subscription): void
    {
        $alreadyExists = $this->repository->findOneBy([
            'customer' => $subscription->getCustomer(),
            'product'  => $subscription->getProduct(),
            'status'   => SubscriptionInterface::STATUS_NEW,
        ]);

        if ($alreadyExists === null) {
            $subscription->setChannel($this->channelContext->getChannel());
            $subscription->setCreatedAt(new \DateTime());
            $subscription->setLocaleCode($this->localeContext->getLocaleCode());
            $subscription->setUpdatedAt(new \DateTime());

            $this->repository->add($subscription);
        }
    }
}
