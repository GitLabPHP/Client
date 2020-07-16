<?php

declare(strict_types=1);

namespace Gitlab\Model;

interface Notable
{
    /**
     * @param string $body
     *
     * @return Note
     */
    public function addNote(string $body);
}
