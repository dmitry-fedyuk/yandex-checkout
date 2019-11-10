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
			'amount' => ['currency' => 'RUB', 'value' => 100.0]
			,'capture' => true
			,'confirmation' => ['return_url' => 'https://www.merchant-website.com/return_url', 'type' => 'redirect']
			,'description' => 'Заказ №1'
		], uniqid('', true)); /** @var YP $yp */
		$c = $yp->getConfirmation(); /** @var Confirmation $c */
		return df_result_sne($c->getConfirmationUrl());
	}
}