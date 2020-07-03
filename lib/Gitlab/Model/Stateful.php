<?php

namespace Gitlab\Model;

interface Stateful
{
    /**
     * @param string|null $note
     *
     * @return static
     */
    public function close($note = null);

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
