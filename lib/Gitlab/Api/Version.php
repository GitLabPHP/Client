<?php namespace Gitlab\Api;

class Version extends AbstractApi
{
    public function show()
    {
        return $this->get('version');
    }
}
