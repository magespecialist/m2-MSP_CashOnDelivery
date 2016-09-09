/**
 * IDEALIAGroup srl
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to info@idealiagroup.com so we can send you a copy immediately.
 *
 * @category   MSP
 * @package    MSP_CashOnDelivery
 * @copyright  Copyright (c) 2016 IDEALIAGroup srl (http://www.idealiagroup.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

define(
    [
        'ko',
        'jquery',
        'mage/storage',
        'Magento_Checkout/js/view/payment/default',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/action/get-totals',
        'Magento_Checkout/js/model/url-builder',
        'mage/url',
        'Magento_Checkout/js/model/full-screen-loader',
        'MSP_CashOnDelivery/js/view/checkout/cashondelivery',
        'Magento_Customer/js/model/customer',
        'Magento_Checkout/js/model/totals'
    ],
    function (ko, $, storage, Component, quote, getTotalsAction, urlBuilder, mageUrlBuilder, fullScreenLoader, cashondelivery, customer, totals) {
        'use strict';
        return Component.extend({
            defaults: {
                template: 'MSP_CashOnDelivery/payment/cashondelivery-form'
            },
            lastDetectedMethod: null,
            extraFeeText: ko.observable(''),
            refreshMethod: function () {
                var serviceUrl;

                var paymentData = quote.paymentMethod();
                if (paymentData['method'] === 'free') {
                    return;
                }

                fullScreenLoader.startLoader();
                
                if(customer.isLoggedIn()) {
                    serviceUrl = urlBuilder.createUrl('/carts/mine/selected-payment-method', {});
                }
                else {
                    serviceUrl = urlBuilder.createUrl('/guest-carts/:cartId/selected-payment-method', {
                        cartId: quote.getQuoteId()
                    });
                }

                var payload = {
                    cartId: quote.getQuoteId(),
                    method: paymentData
                };

                return storage.put(
                    serviceUrl, JSON.stringify(payload)
                ).done(function () {
                    cashondelivery.canShowCashOnDelivery(quote.paymentMethod().method == 'msp_cashondelivery');
                    getTotalsAction([]);
                    fullScreenLoader.stopLoader();
                });
            },
            initObservable: function () {
                this._super();
                var me = this;

                var serviceUrl = urlBuilder.createUrl('/carts/mine/msp-cashondelivery-information', {});
                storage.get(
                    serviceUrl
                ).done(function (data) {
                    me.extraFeeText(data.fee_label);
                });

                quote.paymentMethod.subscribe(function() {
                    if (quote.paymentMethod().method != me.lastDetectedMethod) {

                        if (
                            (quote.paymentMethod().method == 'msp_cashondelivery' || me.lastDetectedMethod == 'msp_cashondelivery')
                            || (totals.getSegment('msp_cashondelivery') && me.lastDetectedMethod === null)
                        ) {
                            me.refreshMethod();
                        }

                        me.lastDetectedMethod = quote.paymentMethod().method;
                    }
                });

                return this;
            }
        });
    }
);
