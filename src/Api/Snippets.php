<?php

declare(strict_types=1);

/*
 * This file is part of the Gitlab API library.
 *
 * (c) Matt Humphrey <matth@windsor-telecom.co.uk>
 * (c) Graham Campbell <hello@gjcampbell.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gitlab\Api;

class Snippets extends AbstractApi
{
    /**
     * List all snippets for a project.
     *
     * @see https://docs.gitlab.com/api/project_snippets/#list-all-snippets-for-a-project
     */
    public function all(int|string $project_id): mixed
    {
        return $this->get($this->getProjectPath($project_id, 'snippets'));
    }

    /**
     * Retrieve a snippet.
     *
     * @see https://docs.gitlab.com/api/project_snippets/#retrieve-a-snippet
     */
    public function show(int|string $project_id, int $snippet_id): mixed
    {
        return $this->get($this->getProjectPath($project_id, 'snippets/'.self::encodePath($snippet_id)));
    }

    /**
     * Create a snippet.
     *
     * @see https://docs.gitlab.com/api/project_snippets/#create-a-snippet
     */
    public function create(int|string $project_id, string $title, string $filename, string $code, string $visibility): mixed
    {
        return $this->post($this->getProjectPath($project_id, 'snippets'), [
            'title' => $title,
            'file_name' => $filename,
            'code' => $code,
            'visibility' => $visibility,
        ]);
    }

    /**
     * Update a snippet.
     *
     * @see https://docs.gitlab.com/api/project_snippets/#update-a-snippet
     */
    public function update(int|string $project_id, int $snippet_id, array $params): mixed
    {
        return $this->put($this->getProjectPath($project_id, 'snippets/'.self::encodePath($snippet_id)), $params);
    }

    /**
     * Retrieve snippet content.
     *
     * @see https://docs.gitlab.com/api/project_snippets/#retrieve-snippet-content
     */
    public function content(int|string $project_id, int $snippet_id): mixed
    {
        return $this->get($this->getProjectPath($project_id, 'snippets/'.self::encodePath($snippet_id).'/raw'));
    }

    /**
     * Delete a snippet.
     *
     * @see https://docs.gitlab.com/api/project_snippets/#delete-a-snippet
     */
    public function remove(int|string $project_id, int $snippet_id): mixed
    {
        return $this->delete($this->getProjectPath($project_id, 'snippets/'.self::encodePath($snippet_id)));
    }

    /**
     * List all snippet notes.
     *
     * @see https://docs.gitlab.com/api/notes/#list-all-snippet-notes
     */
    public function showNotes(int|string $project_id, int $snippet_id): mixed
    {
        return $this->get($this->getProjectPath($project_id, 'snippets/'.self::encodePath($snippet_id).'/notes'));
    }

    /**
     * Retrieve a snippet note.
     *
     * @see https://docs.gitlab.com/api/notes/#retrieve-a-snippet-note
     */
    public function showNote(int|string $project_id, int $snippet_id, int $note_id): mixed
    {
        return $this->get($this->getProjectPath($project_id, 'snippets/'.self::encodePath($snippet_id).'/notes/'.self::encodePath($note_id)));
    }

    /**
     * Create a snippet note.
     *
     * @see https://docs.gitlab.com/api/notes/#create-a-snippet-note
     */
    public function addNote(int|string $project_id, int $snippet_id, string $body, array $params = []): mixed
    {
        $params['body'] = $body;

        return $this->post($this->getProjectPath($project_id, 'snippets/'.self::encodePath($snippet_id).'/notes'), $params);
    }

    /**
     * Update a snippet note.
     *
     * @see https://docs.gitlab.com/api/notes/#update-a-snippet-note
     */
    public function updateNote(int|string $project_id, int $snippet_id, int $note_id, string $body): mixed
    {
        return $this->put($this->getProjectPath($project_id, 'snippets/'.self::encodePath($snippet_id).'/notes/'.self::encodePath($note_id)), [
            'body' => $body,
        ]);
    }

    /**
     * Delete a snippet note.
     *
     * @see https://docs.gitlab.com/api/notes/#delete-a-snippet-note
     */
    public function removeNote(int|string $project_id, int $snippet_id, int $note_id): mixed
    {
        return $this->delete($this->getProjectPath($project_id, 'snippets/'.self::encodePath($snippet_id).'/notes/'.self::encodePath($note_id)));
    }

    /**
     * List all emoji reactions for a snippet.
     *
     * @see https://docs.gitlab.com/api/emoji_reactions/#list-all-emoji-reactions-for-a-resource
     */
    public function awardEmoji(int|string $project_id, int $snippet_id): mixed
    {
        return $this->get($this->getProjectPath($project_id, 'snippets/'.self::encodePath($snippet_id).'/award_emoji'));
    }

    /**
     * Delete an emoji reaction from a snippet.
     *
     * @see https://docs.gitlab.com/api/emoji_reactions/#delete-an-emoji-reaction-from-a-resource
     */
    public function removeAwardEmoji(int|string $project_id, int $snippet_id, int $award_id): mixed
    {
        return $this->delete($this->getProjectPath($project_id, 'snippets/'.self::encodePath($snippet_id).'/award_emoji/'.self::encodePath($award_id)));
    }
}
