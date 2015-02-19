<?php

namespace Gitlab\HttpClient\Message;

use GuzzleHttp\Message\Response;

class Guzzle4Response implements ResponseInterface
{
    protected $response;

    public function __construct(Response $response)
    {
        $this->response = $response;
    }

    /**
     * {@inheritDoc}
     */
    public function getContent()
    {
        $response = $this->response->getBody(true);

        if ($this->response->getHeader("Content-Type") === "application/json") {
            $content  = json_decode($response, true);

            if (JSON_ERROR_NONE === json_last_error()) {
                return $content;
            }
        }

        return $response;
    }
}
