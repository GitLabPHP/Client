<?php

declare(strict_types=1);

namespace Gitlab\Model;

/**
 * @deprecated since version 10.1 and will be removed in 11.0.
 *
 */
interface Stateful
{
    /**
     * @param string|null $note
     *
     * @return static
     */
    public function close(?string $note = null);

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
