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
    public function all(int|string $project_id): mixed
    {
        return $this->get($this->getProjectPath($project_id, 'snippets'));
    }

    public function show(int|string $project_id, int $snippet_id): mixed
    {
        return $this->get($this->getProjectPath($project_id, 'snippets/'.self::encodePath($snippet_id)));
    }

    public function create(int|string $project_id, string $title, string $filename, string $code, string $visibility): mixed
    {
        return $this->post($this->getProjectPath($project_id, 'snippets'), [
            'title' => $title,
            'file_name' => $filename,
            'code' => $code,
            'visibility' => $visibility,
        ]);
    }

    public function update(int|string $project_id, int $snippet_id, array $params): mixed
    {
        return $this->put($this->getProjectPath($project_id, 'snippets/'.self::encodePath($snippet_id)), $params);
    }

    public function content(int|string $project_id, int $snippet_id): mixed
    {
        return $this->get($this->getProjectPath($project_id, 'snippets/'.self::encodePath($snippet_id).'/raw'));
    }

    public function remove(int|string $project_id, int $snippet_id): mixed
    {
        return $this->delete($this->getProjectPath($project_id, 'snippets/'.self::encodePath($snippet_id)));
    }

    public function showNotes(int|string $project_id, int $snippet_id): mixed
    {
        return $this->get($this->getProjectPath($project_id, 'snippets/'.self::encodePath($snippet_id).'/notes'));
    }

    public function showNote(int|string $project_id, int $snippet_id, int $note_id): mixed
    {
        return $this->get($this->getProjectPath($project_id, 'snippets/'.self::encodePath($snippet_id).'/notes/'.self::encodePath($note_id)));
    }

    public function addNote(int|string $project_id, int $snippet_id, string $body, array $params = []): mixed
    {
        $params['body'] = $body;

        return $this->post($this->getProjectPath($project_id, 'snippets/'.self::encodePath($snippet_id).'/notes'), $params);
    }

    public function updateNote(int|string $project_id, int $snippet_id, int $note_id, string $body): mixed
    {
        return $this->put($this->getProjectPath($project_id, 'snippets/'.self::encodePath($snippet_id).'/notes/'.self::encodePath($note_id)), [
            'body' => $body,
        ]);
    }

    public function removeNote(int|string $project_id, int $snippet_id, int $note_id): mixed
    {
        return $this->delete($this->getProjectPath($project_id, 'snippets/'.self::encodePath($snippet_id).'/notes/'.self::encodePath($note_id)));
    }

    public function awardEmoji(int|string $project_id, int $snippet_id): mixed
    {
        return $this->get($this->getProjectPath($project_id, 'snippets/'.self::encodePath($snippet_id).'/award_emoji'));
    }

    public function removeAwardEmoji(int|string $project_id, int $snippet_id, int $award_id): mixed
    {
        return $this->delete($this->getProjectPath($project_id, 'snippets/'.self::encodePath($snippet_id).'/award_emoji/'.self::encodePath($award_id)));
    }
}
