<?php

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
