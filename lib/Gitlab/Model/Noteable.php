<?php

namespace Gitlab\Model;

interface Noteable
{
    /**
     * @param string $comment
     * @return Note
     */
    public function addComment($comment);

    /**
     * @return Note[]
     */
    public function showComments();

    /**
     * @param string $comment
     * @return static
     */
    public function close($comment = null);

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
