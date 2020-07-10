<?php

declare(strict_types=1);

namespace Gitlab\Api;

class Version extends AbstractApi
{
    /**
     * @return mixed
     */
    public function show()
    {
        return $this->get('version');
    }
}
