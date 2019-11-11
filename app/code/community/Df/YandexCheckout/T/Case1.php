<?php
namespace Df\YandexCheckout\T;
use YandexCheckout\Client as YC;
use YandexCheckout\Model\Confirmation\ConfirmationRedirect as Confirmation;
use YandexCheckout\Request\Payments\CreatePaymentResponse as YP;
// 2019-11-06
final class Case1 extends \Df\Core\T\Base {
	/** 2019-11-06 */
	function t00() {}

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
	 */
	function t01() {
		$yc = new YC(); /** @var YC $yc */
		$yc->setAuth('649593', 'test_GAYN1K-abG3t0cUwLRFuLdeLQXlz60SFVDqiuO4B_Eg');
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
			,'capture' => true
			,'confirmation' => ['return_url' => 'https://www.merchant-website.com/return_url', 'type' => 'redirect']
			/**
			 * 2019-11-11
			 * «Description of the transaction (maximum 128 characters)
			 * displayed in your Yandex.Checkout Merchant Profile,
			 * and shown to the user during checkout.»
			 * https://checkout.yandex.com/developers/api#payment_object_description
			 */
			,'description' => 'Заказ №1'
			/**
			 * 2019-11-11
			 * 1)  Required. Object. «Payment method  used for this payment».
			 * https://checkout.yandex.com/developers/payments/basics/payment-methods
			 * https://checkout.yandex.com/developers/api#payment_object_recipient
			 * 2) Despite the documentation says that the parameter is required, actually it is not.
			 */
			,'payment_method' => []
			/**
			 * 2019-11-11
			 * 1) 'recipient' => ['account_id' => '', 'gateway_id' => '']
			 * 2) Required. Object. «Payment recipient».
			 * https://checkout.yandex.com/developers/api#payment_object_recipient
			 * 3) Despite the documentation says that it is required, actually, it is not.
			 * 4) The Yandex.Checkout library will fail if empty values are passed.
			 * 5) Actually, `account_id` is the same as the first parameter of
			 * @uses \YandexCheckout\Client\BaseClient::setAuth():
			 * it is shown in the @use \YandexCheckout\Client::createPayment() response.
			 * 6)
			 * @see \YandexCheckout\Request\Payments\CreatePaymentRequestBuilder::setRecipient()
			 * https://github.com/yandex-money/yandex-checkout-sdk-php/blob/1.5.4/lib/Request/Payments/CreatePaymentRequestBuilder.php#L120-L137
			 * @see \YandexCheckout\Model\Recipient::setAccountId()
			 * https://github.com/yandex-money/yandex-checkout-sdk-php/blob/1.5.4/lib/Model/Recipient.php#L67-L86
			 * @see \YandexCheckout\Model\Recipient::setGatewayId()
			 * https://github.com/yandex-money/yandex-checkout-sdk-php/blob/1.5.4/lib/Model/Recipient.php#L100-L121
			 */
		], uniqid('', true)); /** @var YP $yp */
		echo df_json_encode($yp->jsonSerialize());
	}

	/**
	 * @test
	 * 2019-11-10
	 * 2019-11-11 «Create a payment»: https://checkout.yandex.com/developers/api#create_payment
	 */
	function t02() {
		$yc = new YC(); /** @var YC $yc */
		$yc->setAuth('649593', 'test_GAYN1K-abG3t0cUwLRFuLdeLQXlz60SFVDqiuO4B_Eg');
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
			,'capture' => true
			,'confirmation' => ['return_url' => 'https://www.merchant-website.com/return_url', 'type' => 'redirect']
			/**
			 * 2019-11-11
			 * Optional. String.
			 * «Description of the transaction (maximum 128 characters)
			 * displayed in your Yandex.Checkout Merchant Profile,
			 * and shown to the user during checkout.»
			 * https://checkout.yandex.com/developers/api#payment_object_description
			 */
			,'description' => 'Заказ №1'
			/**
			 * 2019-11-11
			 * 1)  Required. Object. «Payment method  used for this payment».
			 * https://checkout.yandex.com/developers/payments/basics/payment-methods
			 * https://checkout.yandex.com/developers/api#payment_object_recipient
			 * 2) Despite the documentation says that the parameter is required, actually it is not.
			 */
			,'payment_method' => []
			/**
			 * 2019-11-11
			 * 1) 'recipient' => ['account_id' => '', 'gateway_id' => '']
			 * 2) Required. Object. «Payment recipient».
			 * https://checkout.yandex.com/developers/api#payment_object_recipient
			 * 3) Despite the documentation says that the parameter is required, actually it is not.
			 * 4) The Yandex.Checkout library will fail if empty values are passed.
			 * 5) Actually, `account_id` is the same as the first parameter of
			 * @uses \YandexCheckout\Client\BaseClient::setAuth():
			 * it is shown in the @use \YandexCheckout\Client::createPayment() response.
			 * 6)
			 * @see \YandexCheckout\Request\Payments\CreatePaymentRequestBuilder::setRecipient()
			 * https://github.com/yandex-money/yandex-checkout-sdk-php/blob/1.5.4/lib/Request/Payments/CreatePaymentRequestBuilder.php#L120-L137
			 * @see \YandexCheckout\Model\Recipient::setAccountId()
			 * https://github.com/yandex-money/yandex-checkout-sdk-php/blob/1.5.4/lib/Model/Recipient.php#L67-L86
			 * @see \YandexCheckout\Model\Recipient::setGatewayId()
			 * https://github.com/yandex-money/yandex-checkout-sdk-php/blob/1.5.4/lib/Model/Recipient.php#L100-L121
			 */
		], uniqid('', true)); /** @var YP $yp */
		$c = $yp->getConfirmation(); /** @var Confirmation $c */
		echo $c->getConfirmationUrl();
	}
}