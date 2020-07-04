<?php

namespace Gitlab\Model;

interface Noteable extends Stateful
{
    /**
     * @param string      $comment
     * @param string|null $created_at
     *
     * @return Note
     *
     * @deprecated addComment deprecated since version 9.18 and will be removed in 10.0. Use the addNote() method instead.
     */
    public function addComment($comment, $created_at = null);

    /**
     * @return Note[]
     *
     * @deprecated addComments deprecated since version 9.18 and will be removed in 10.0. Use the showNotes() method instead.
     */
    public function showComments();
}
