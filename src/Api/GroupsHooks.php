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

class GroupsHooks extends AbstractApi
{
    /**
     * @param array $parameters {
     *     @var int $page       page number (default: 1)
     *     @var int $per_page   number of items to list per page (default: 20, max: 100)
     * }
     */
    public function all($group_id, array $parameters = []): mixed
    {
        $resolver = $this->createOptionsResolver();

        $path = 'groups/'.self::encodePath($group_id).'/hooks';

        return $this->get($path, $resolver->resolve($parameters));
    }

    public function show(int|string $group_id, int $hook_id): mixed
    {
        return $this->get('groups/'.self::encodePath($group_id).'/hooks/'.self::encodePath($hook_id));
    }

    public function create(int|string $group_id, array $params): mixed
    {
        return $this->post('groups/'.self::encodePath($group_id).'/hooks', $params);
    }

    public function update(int|string $group_id, int $hook_id, array $params): mixed
    {
        return $this->put('groups/'.self::encodePath($group_id).'/hooks/'.self::encodePath($hook_id), $params);
    }

    public function remove(int|string $group_id, int $hook_id): mixed
    {
        return $this->delete('groups/'.self::encodePath($group_id).'/hooks/'.self::encodePath($hook_id));
    }

    public function allEvents(int|string $group_id, int $hook_id): mixed
    {
        return $this->get('groups/'.self::encodePath($group_id).'/hooks/'.self::encodePath($hook_id).'/events');
    }

    public function resend(int|string $group_id, int $hook_id, int $hook_event_id): mixed
    {
        return $this->post('groups/'.self::encodePath($group_id).'/hooks/'.self::encodePath($hook_id).'/events/'.self::encodePath($hook_event_id).'/resend');
    }

    public function test(int|string $group_id, int $hook_id, string $trigger): mixed
    {
        return $this->post('groups/'.self::encodePath($group_id).'/hooks/'.self::encodePath($hook_id).'/test/'.self::encodePath($trigger));
    }

    public function setCustomHeader(int|string $group_id, int $hook_id, string $key, string $value): mixed
    {
        $params = [
            'value' => $value,
        ];

        return $this->put('groups/'.self::encodePath($group_id).'/hooks/'.self::encodePath($hook_id).'/custom_headers/'.self::encodePath($key), $params);
    }

    public function deleteCustomHeader(int|string $group_id, int $hook_id, string $key): mixed
    {
        return $this->delete('groups/'.self::encodePath($group_id).'/hooks/'.self::encodePath($hook_id).'/custom_headers/'.self::encodePath($key));
    }

    public function setUrlVariable(int|string $group_id, int $hook_id, string $key, string $value): mixed
    {
        $params = [
            'value' => $value,
        ];

        return $this->put('groups/'.self::encodePath($group_id).'/hooks/'.self::encodePath($hook_id).'/url_variables/'.self::encodePath($key), $params);
    }

    public function deleteUrlVariable(int|string $group_id, int $hook_id, string $key): mixed
    {
        return $this->delete('groups/'.self::encodePath($group_id).'/hooks/'.self::encodePath($hook_id).'/url_variables/'.self::encodePath($key));
    }
}
