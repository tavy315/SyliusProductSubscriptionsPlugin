const ProductSubscriptions = () => {
    const productSubscriptionForm = document.querySelector('#product_subscription');

    if (productSubscriptionForm !== null) {
        const syliusProductOutOfStockValidationError = document.querySelector('#sylius-product-out-of-stock-validation-error');

        productSubscriptionForm.addEventListener('submit', (e) => {
            e.preventDefault();

            syliusProductOutOfStockValidationError.classList.add('hidden');

            jQuery.ajax({
                type    : 'POST',
                url     : productSubscriptionForm.getAttribute('action'),
                dataType: 'html',
                data    : jQuery(productSubscriptionForm).serializeArray(),
                success : function (response) {
                    productSubscriptionForm.innerHTML = JSON.parse(response).success;
                    productSubscriptionForm.classList.remove('loading');
                },
                error   : function (error) {
                    const { status, responseText } = error;

                    productSubscriptionForm.classList.remove('loading');

                    if (status === 400) {
                        syliusProductOutOfStockValidationError.innerHTML = JSON.parse(responseText).errors[0];
                        syliusProductOutOfStockValidationError.classList.remove('hidden');
                    }
                }
            });

            return false;
        });
    }
};

document.addEventListener('DOMContentLoaded', function (event) {
    ProductSubscriptions();
});
