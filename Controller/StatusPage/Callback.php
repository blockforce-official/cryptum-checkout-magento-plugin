<?php

namespace Cryptum\Cryptum\Controller\StatusPage;

use Cryptum\Cryptum\Model\Payment as PaymentModel;
use Cryptum\Cryptum\Library\SCMerchantClient\Data\OrderCallback;

use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Request\InvalidRequestException;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Sales\Model\Order;
use Magento\Framework\App\Request\Http;


class Callback extends Action implements CsrfAwareActionInterface
{
    protected $order;
    protected $paymentModel;
    protected $client;
    protected $httpRequest;

    /**
     * @param Context $context
     * @param Order $order
     * @param PaymentModel $paymentModel
     * @internal param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param Http $request
     */
    public function __construct(
        Context $context,
        Order $order,
        PaymentModel $paymentModel,
        Http $request
    ) {

        parent::__construct($context);
        $this->order = $order;
        $this->paymentModel = $paymentModel;
        $this->client = $paymentModel->getSCClient();
        $this->httpRequest = $request;
    }

    public function createCsrfValidationException(RequestInterface $request): ?InvalidRequestException
    {
        return null;
    }

    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return true;
    }


    /**
     * Default customer account page
     *
     * @return void
     */
    public function execute()
    {

        $apikey = $_SERVER['HTTP_X_API_KEY'];

        if ($apikey === $this->paymentModel->getConfigData('api_key')) {

            $orderId = $_POST['orderId'];
            $message = $_POST['message'];

            $response = \Httpful\Request::get($this->paymentModel->getMerchantApiUrl() . '/order/' . $orderId)
                ->addHeader('x-api-key', $apikey)
                ->addHeader('Content-Type', 'application/json; charset=utf-8')
                ->expects(\Httpful\Mime::JSON)
                ->send();

            $this->getResponse()->setBody(print_r($response, true));
            if (isset($response->body)) {
                $request = [
                    'merchantId' => $this->paymentModel->getConfigData('merchant_id'),
                    'apiId' => $response->body->pluginCheckoutStore->apikeyId,
                    'orderId' => $response->body->pluginCheckoutEcommerce->orderId,
                    'payCurrency' => $response->body->paymentMethod,
                    'payAmount' => $response->body->convertedTotal,
                    'receiveCurrency' => $response->body->blockchain,
                    'receiveAmount' => $response->body->orderTotal,
                    'receivedAmount' => $response->body->convertedTotal,
                    'description' => $message,
                    'orderRequestId' => $response->body->id,
                    'status' => $response->body->paymentStatus
                ];

                $orderCallback = $this->client->parseCreateOrderCallback($request);

                if (!is_null($orderCallback)) {
                    $order = $this->order->loadByIncrementId($orderCallback->getOrderId());
                    if ($this->paymentModel->updateOrderStatus($orderCallback, $order)) {
                        $this->getResponse()->setBody('*ok*');
                    } else {
                        $this->getResponse()->setBody('*error*');
                    }
                } else {
                    $this->getResponse()->setBody(print_r('*error*', true));
                }
            }
        } else {
            $this->getResponse()->setBody('*Unauthorized*');
        }
    }
}
