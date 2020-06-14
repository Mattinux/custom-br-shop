<?php

namespace Aimeos\MShop\Service\Provider\Payment;


/**
 * Payment provider for payment gateways supported by the Omnipay library.
 *
 * @package MShop
 * @subpackage Service
 */
class NewOmniPay extends \Aimeos\MShop\Service\Provider\Payment\OmniPay
{
	/**
	 * Returns the data passed to the Omnipay library
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Iface $base Basket object
	 * @param $orderid string Unique order ID
	 * @param array $params Request parameter if available
	 */
	protected function getData( \Aimeos\MShop\Order\Item\Base\Iface $base, $orderid, array $params )
	{
		$addresses = $base->getAddress( \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_PAYMENT );

		if( ( $address = current( $addresses ) ) === false ) {
			$langid = $this->getContext()->getLocale()->getLanguageId();
		} else {
			$langid = $address->getLanguageId();
		}

		$data = array(
			'language' => $langid,
			'transactionId' => $orderid,
			'amount' => $this->getAmount( $base->getPrice() ),
			'currency' => $base->getLocale()->getCurrencyId(),
			'description' => sprintf( $this->getContext()->getI18n()->dt( 'mshop', 'Order %1$s' ), $orderid ),
			'clientIp' => $this->getValue( 'client.ipaddress' ),
		);

		if( $this->getValue( 'createtoken', false ) ) {
			$data['createCard'] = true;
		}

		if( $this->getValue( 'onsite', false ) || $this->getValue( 'address', false ) ) {
			$data['card'] = $this->getCardDetails( $base, $params );
		}

		return $data + $this->getPaymentUrls();
	}

}
