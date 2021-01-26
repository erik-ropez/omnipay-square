<?php

namespace Omnipay\Square\Message;

use Omnipay\Common\Message\AbstractRequest;
use SquareConnect;

/**
 * Square Purchase Request
 */
class CaptureRequest extends AbstractRequest
{
    public function getAccessToken()
    {
        return $this->getParameter('accessToken');
    }

    public function setAccessToken($value)
    {
        return $this->setParameter('accessToken', $value);
    }

    public function getPaymentId()
    {
        return $this->getParameter('transactionReference');
    }

    public function setPaymentId($value)
    {
        return $this->setParameter('transactionReference', $value);
    }

    public function getData()
    {
        return [];
    }

    public function sendData($data)
    {
        $defaultApiConfig = new \SquareConnect\Configuration();
        $defaultApiConfig->setAccessToken($this->getAccessToken());

        if($this->getParameter('testMode')) {
            $defaultApiConfig->setHost("https://connect.squareupsandbox.com");
        }

        $defaultApiClient = new \SquareConnect\ApiClient($defaultApiConfig);

        $api_instance = new SquareConnect\Api\PaymentsApi($defaultApiClient);

        try {
            $result = $api_instance->completePayment($this->getPaymentId(), '{}');

            if ($error = $result->getErrors()) {
                $response = [
                    'status' => 'error',
                    'code' => $error['code'],
                    'detail' => $error['detail']
                ];
            } else {
                $response = [
                    'status' => 'success',
                    'transactionId' => $result->getPayment()->getId(),
                    'referenceId' => $result->getPayment()->getReferenceId(),
                    'created_at' => $result->getPayment()->getCreatedAt(),
                    'orderId' => $result->getPayment()->getOrderId()
                ];
            }
        } catch (\Exception $e) {
            $error = $e->getResponseBody()->errors[0]->detail ?? $e->getMessage();

            $response = [
                'status' => 'error',
                'detail' => 'Exception when completing transaction: ' . $error
            ];
        }

        return $this->createResponse($response);
    }

    public function createResponse($response)
    {
        return $this->response = new ChargeResponse($this, $response);
    }
}
