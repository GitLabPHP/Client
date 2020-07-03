<?php

namespace Gitlab\Model;

interface Notable
{
    /**
     * @param string $body
     *
     * @return Note
     */
    public function addNote($body);
}
