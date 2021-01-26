<?php

namespace Omnipay\Square\Message;

/**
 * Square Authorize Request
 */
class AuthorizeRequest extends ChargeRequest
{
    public function getData()
    {
        $data = parent::getData();

        $data['autocomplete'] = false;

        return $data;
    }

    public function createResponse($response)
    {
        return $this->response = new AuthorizeResponse($this, $response);
    }
}
