<?php

namespace Gitlab\HttpClient\Message;

use Buzz\Message\Response as BaseResponse;

class BuzzResponse extends BaseResponse implements ResponseInterface
{
    /**
     * {@inheritDoc}
     */
    public function getContent()
    {
        $response = parent::getContent();

        if ($this->getHeader("Content-Type") === "application/json") {
            $content  = json_decode($response, true);

            if (JSON_ERROR_NONE === json_last_error()) {
                return $content;
            }
        }

        return $response;
    }
}
