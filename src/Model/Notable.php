<?php

declare(strict_types=1);

namespace Gitlab\Model;

/**
 * @deprecated since version 10.1 and will be removed in 11.0.
 */
interface Notable
{
    /**
     * @param string $body
     *
     * @return Note
     */
    public function addNote(string $body);
}
