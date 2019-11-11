<?php
namespace Df\YandexCheckout;
use Df\YandexCheckout\Method as M;
use YandexCheckout\Client as YC;
use YandexCheckout\Model\Confirmation\ConfirmationRedirect as Confirmation;
use YandexCheckout\Request\Payments\CreatePaymentResponse as YP;
// 2019-11-10
final class RedirectURL {
	/**
	 * 2019-11-06
	 * 2019-11-09
	 * A response:
	 *	{
	 *		"amount": {
	 *			"currency": "RUB",
	 *			"value": "100.00"
	 *		},
	 *		"confirmation": {
	 *			"confirmation_url": "https://money.yandex.ru/api-pages/v2/payment-confirm/epl?orderId=25587f31-000f-5000-8000-1e0377a11c18",
	 *			"enforce": false,
	 *			"type": "redirect"
	 *		},
	 *		"created_at": "2019-11-09T07:32:01+00:00",
	 *		"description": "Заказ №1",
	 *		"id": "25587f31-000f-5000-8000-1e0377a11c18",
	 *		"paid": false,
	 *		"recipient": {
	 *			"account_id": "649593",
	 *			"gateway_id": "1639577"
	 *		},
	 *		"refundable": false,
	 *		"status": "pending",
	 *		"test": true
	 *	}
	 * 2019-11-11 «Create a payment»: https://checkout.yandex.com/developers/api#create_payment
	 * @used-by \Df\YandexCheckout\Method::redirectUrl()
	 * @param Method $m
	 * @return string
	 */
	static function get(M $m) {
		$yc = new YC(); /** @var YC $yc */
		$yc->setAuth('649593', 'test_GAYN1K-abG3t0cUwLRFuLdeLQXlz60SFVDqiuO4B_Eg');
		/**
		 * 2019-11-09
		 * A response:
		 *	{
		 *		"amount": {
		 *			"currency": "RUB",
		 *			"value": "100.00"
		 *		},
		 *		"confirmation": {
		 *			"confirmation_url": "https://money.yandex.ru/api-pages/v2/payment-confirm/epl?orderId=25587f31-000f-5000-8000-1e0377a11c18",
		 *			"enforce": false,
		 *			"type": "redirect"
		 *		},
		 *		"created_at": "2019-11-09T07:32:01+00:00",
		 *		"description": "Заказ №1",
		 *		"id": "25587f31-000f-5000-8000-1e0377a11c18",
		 *		"paid": false,
		 *		"recipient": {
		 *			"account_id": "649593",
		 *			"gateway_id": "1639577"
		 *		},
		 *		"refundable": false,
		 *		"status": "pending",
		 *		"test": true
		 *	}
		 */
		$yp = $yc->createPayment([
			/**
			 * 2019-11-11
			 * Required. Object.
			 * «Payment amount.
			 * Sometimes Yandex.Checkout's partners charge additional commission from the users
			 * that is not included in this amount.»
			 * https://checkout.yandex.com/developers/api#create_payment_amount
			 * https://checkout.yandex.com/developers/api#payment_object_amount
			 */
			'amount' => [
				/**
				 * 2019-11-11
				 * Required. String.
				 * «Currency code in the ISO-4217 format.
				 * It should match the currency of your subaccount (recipient.gateway_id)
				 * if you separate payment flows,
				 * or the currency of the account (shopId in the Merchant Profile) if you don't.»
				 * https://checkout.yandex.com/developers/api#create_payment_amount_currency
				 * https://checkout.yandex.com/developers/api#payment_object_amount_currency
				 */
				'currency' => 'RUB'
				/**
				 * 2019-11-11
				 * Required. String.
				 * «Amount in the selected currency, in the form of a string with a dot separator, for example, 10.00.
				 * The number of digits after the dot depends on the selected currency.»
				 * https://checkout.yandex.com/developers/api#create_payment_amount_value
				 * https://checkout.yandex.com/developers/api#payment_object_amount_value
				 */
				,'value' => 100.0
			]
			/**
			 * 2019-11-11
			 * Optional. Boolean.
			 * «Automatic acceptance of an incoming payment.»
			 * https://checkout.yandex.com/developers/payments/basics/payment-process#capture-true
			 * https://checkout.yandex.com/developers/api#create_payment_capture
			 */
			,'capture' => true
			/**
			 * 2019-11-11
			 * Optional. Object.
			 * «Information required to initiate the selected payment confirmation scenario by the user.»
			 * https://checkout.yandex.com/developers/payments/basics/payment-process#user-confirmation
			 * https://checkout.yandex.com/developers/api#create_payment_confirmation
			 * https://checkout.yandex.com/developers/api#payment_object_confirmation
			 */
			,'confirmation' => [
				/**
				 * 2019-11-11
				 * Optional. Boolean.
				 * «A request for making a payment with authentication by 3-D Secure.
				 * It works if you accept bank card payments without user confirmation by default.
				 * In other cases, the 3-D Secure authentication will be handled by Yandex.Checkout.
				 * If you would like to accept payments without additional confirmation by the user,
				 * contact your Yandex.Checkout manager.»
				 * https://checkout.yandex.com/developers/api#create_payment_confirmation_redirect_enforce
				 */
				'enforce' => true
				/**
				 * 2019-11-11
				 * Optional. String.
				 * «Language of the interface, emails, and text messages that will be displayed and sent to the user.
				 * Formatted in accordance with ISO/IEC 15897.
				 * Possible values: ru_RU, en_US.»
				 * https://checkout.yandex.com/developers/api#create_payment_confirmation_locale
				 */
				,'locale' => 'ru_RU'
				/**
				 * 2019-11-11
				 * Required. String.
				 * «The URL that the user will return to after confirming or canceling the payment on the webpage.»
				 * https://checkout.yandex.com/developers/api#create_payment_confirmation_redirect_return_url
				 */
				,'return_url' => 'https://www.merchant-website.com/return_url'
				/**
				 * 2019-11-11
				 * Required. String. «Confirmation scenario code.»
				 * https://checkout.yandex.com/developers/api#create_payment_confirmation_redirect_type
				 */
				,'type' => 'redirect'
			]
			/**
			 * 2019-11-11
			 * «Description of the transaction (maximum 128 characters)
			 * displayed in your Yandex.Checkout Merchant Profile,
			 * and shown to the user during checkout.»
			 * https://checkout.yandex.com/developers/api#create_payment_description
			 * https://checkout.yandex.com/developers/api#payment_object_description
			 */
			,'description' => 'Заказ №1'
		], uniqid('', true)); /** @var YP $yp */
		$c = $yp->getConfirmation(); /** @var Confirmation $c */
		return df_result_sne($c->getConfirmationUrl());
	}
}