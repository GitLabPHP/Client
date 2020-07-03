<?php

namespace Gitlab\Model;

interface Noteable
{
    /**
     * @return static
     */
    public function close();

    /**
     * @return static
     */
    public function open();

    /**
     * @return static
     */
    public function reopen();

    /**
     * @return bool
     */
    public function isClosed();
}
