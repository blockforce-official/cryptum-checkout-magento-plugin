define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (
        Component,
        rendererList
    ) {
        'use strict';
        rendererList.push(
            {
                type: 'cryptum_cryptum',
                component: 'Cryptum_Cryptum/js/view/payment/method-renderer/cryptum-method'
            }
        );
        /** Add view logic here if needed */
        return Component.extend({});
    }
);
