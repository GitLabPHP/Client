<?php

declare(strict_types=1);

namespace Gitlab\Api;

use Gitlab\Client;

/**
 * Api interface.
 */
interface ApiInterface
{
    public function __construct(Client $client);
}
