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
	 * @used-by \Df\YandexCheckout\Method::redirectUrl()
	 * @param Method $m
	 * @return string
	 */
	static function get(M $m) {
		$yc = new YC(); /** @var YC $yc */
		$yc->setAuth('649593', 'test_GAYN1K-abG3t0cUwLRFuLdeLQXlz60SFVDqiuO4B_Eg');
		$yp = $yc->createPayment([
			/**
			 * 2019-11-10
			 * «Payment amount.
			 * Sometimes Yandex.Checkout's partners charge additional commission from the users
			 * that is not included in this amount.»
			 * https://checkout.yandex.com/developers/api#payment_object_amount
			 */
			'amount' => ['currency' => 'RUB', 'value' => 100.0]
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
			 * 1)
			 *	,'recipient' => [
			 *		'account_id' => ''
			 *		,'gateway_id' => ''
			 *	]
			 * 2) Required. Object. «Payment recipient»
			 * https://checkout.yandex.com/developers/api#payment_object_recipient
			 * 3) Despite the documentation says that it is required, actually, it is not.
			 * 4) The Yandex.Checkout library will fail if empty values are passed.
			 * @see \YandexCheckout\Request\Payments\CreatePaymentRequestBuilder::setRecipient()
			 * https://github.com/yandex-money/yandex-checkout-sdk-php/blob/1.5.4/lib/Request/Payments/CreatePaymentRequestBuilder.php#L120-L137
			 * @see \YandexCheckout\Model\Recipient::setAccountId()
			 * https://github.com/yandex-money/yandex-checkout-sdk-php/blob/1.5.4/lib/Model/Recipient.php#L67-L86
			 * @see \YandexCheckout\Model\Recipient::setGatewayId()
			 * https://github.com/yandex-money/yandex-checkout-sdk-php/blob/1.5.4/lib/Model/Recipient.php#L100-L121
			 */
		], uniqid('', true)); /** @var YP $yp */
		$c = $yp->getConfirmation(); /** @var Confirmation $c */
		return df_result_sne($c->getConfirmationUrl());
	}
}