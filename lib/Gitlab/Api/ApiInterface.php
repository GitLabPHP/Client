<?php

namespace Gitlab\Api;

use Gitlab\Client;

/**
 * Api interface.
 */
interface ApiInterface
{
    public function __construct(Client $client);
}
