<?php

namespace Gitlab\HttpClient\Message;

use Guzzle\Http\Message;

class GuzzleResponse implements ResponseInterface
{
    protected $response;

    public function __construct(Message\Response $response)
    {
        $this->response = $response;
    }

    /**
     * {@inheritDoc}
     */
    public function getContent()
    {
        $response = $this->response->getBody(true);

        if ($this->response->getHeader("Content-Type")->hasValue("application/json")) {
            $content  = json_decode($response, true);

            if (JSON_ERROR_NONE === json_last_error()) {
                return $content;
            }
        }

        return $response;
    }
}
