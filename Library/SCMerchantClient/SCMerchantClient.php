<?php
namespace Cryptum\Cryptum\Library\SCMerchantClient;

use Cryptum\Cryptum\Library\SCMerchantClient\Message\CreateOrderRequest;
use Cryptum\Cryptum\Library\SCMerchantClient\Message\CreateOrderResponse;
use Cryptum\Cryptum\Library\SCMerchantClient\Data\ApiError;
use Cryptum\Cryptum\Library\SCMerchantClient\Data\OrderCallback;


class SCMerchantClient 
{
	private $merchantApiUrl;
	private $merchantId;
	private $apiId;
	private $environment;
	private $debug;

	/**
	 * @param $merchantApiUrl
	 * @param $merchantId
	 * @param $apiId
	 * @param bool $debug
	 */
	function __construct($merchantApiUrl, $merchantId, $apiId, $environment, $debug = false)
	{
		$this->merchantApiUrl = $merchantApiUrl;
		$this->merchantId = $merchantId;
		$this->apiId = $apiId;
		$this->debug = $debug;
		$this->environment = $environment;
	}

	/**
	 * @param CreateOrderRequest $request
	 * @return ApiError|CreateOrderResponse
	 */

	public function createOrder(CreateOrderRequest $request)
	{
		$payload = array(
			'storeId' => $this->merchantId,
			'ecommerceOrderId' => $request->getEcommerceOrderId(),
			'ecommerce' => $request->getEcommerce(),
			'orderCurrency' => $request->getOrderCurrency(),
			'orderTotal' => $request->getOrderTotal(),
			'storeMarkupPercentage' => $request->getStoreMarkupPercentage(),
			'storeDiscountPercentage' => $request->getStoreDiscountPercentage(),
			'callbackUrl' => $request->getCallbackUrl(),
			'successReturnUrl' => $request->getSuccessReturnUrl(),
			'firstName' => $request->getFirstName(),
			'lastName' => $request->getLastName(),
			'email' => $request->getEmail(),
			'city' => $request->getCity(),
			'country' => $request->getCountry(),
			'zip' => $request->getZip(),
			'address' => $request->getAddress(),
			'complement' => $request->getComplement(),
			'state' => $request->getState()
		);

		if (!$this->debug) {
		


			$response = \Httpful\Request::post($this->merchantApiUrl . '/order', $payload, \Httpful\Mime::FORM)
				->body($payload, \Httpful\Mime::FORM)
				->addHeader('x-api-key', $this->apiId)
				->addHeader('Content-Type', 'application/json; charset=utf-8')
				->expects(\Httpful\Mime::JSON)
				->send();

		\Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->log(100, print_r($response, true)); 

			if ($response != null) {
				$body = $response->body;
				if ($body != null) {
					if (!isset($response->body->sessionToken)) {
						return new ApiError($response->code, "ERROR");
					} else if (isset($body->id)) {
						return new CreateOrderResponse(
							$body->id,
							$body->createdAt,
							$body->updatedAt,
							$body->orderTotal,
							$body->orderCurrency,
							$body->storeMarkupPercentage,
							$body->storeDiscountPercentage,
							$body->pluginCheckoutStoreId,
							$body->paymentStatus,
							$body->marketRates,
							$body->pluginCheckoutEcommerceId,
							$body->pluginCheckoutConsumerId,
							$body->sessionToken,
							$request->getCancelReturnUrl(),
							$request->getSuccessReturnUrl(),
							$request->getCallbackUrl(),
							$request->getEcommerceOrderId(),
							$this->environment
						);
					}
				}
			}
		} else {
			$response = \Httpful\Request::post($this->merchantApiUrl . '/order', $payload, \Httpful\Mime::FORM)
				->send();
			exit('<pre>' . print_r($response, true) . '</pre>');
		}
	}

	/**
	 * @param $r $_REQUEST
	 * @return OrderCallback|null
	 */
	public function parseCreateOrderCallback($r)
	{
		$result = null;

		if ($r != null && isset($r['merchantId'])) {

			 $result = new OrderCallback(
			 	$r['merchantId'],
			 	$r['apiId'],
			 	$r['orderId'],
			 	$r['payCurrency'],
			 	$r['payAmount'],
			 	$r['receiveCurrency'],
			 	$r['receiveAmount'],
			 	$r['receivedAmount'],
			 	$r['description'],
			 	$r['orderRequestId'],
			 	$r['status']
			 );
		}

		return $result;
	}

	/**
	 * @param OrderCallback $c
	 * @return bool
	 */
	public function validateCreateOrderCallback(OrderCallback $c)
	{
		return true;
	}
}
