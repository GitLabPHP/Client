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

use Symfony\Component\OptionsResolver\OptionsResolver;

class GroupsHooks extends AbstractApi
{
    public function all(int|string $group_id): mixed
    {
        return $this->get('groups/'.self::encodePath($group_id).'/hooks');
    }

    public function show(int|string $group_id, int $hook_id): mixed
    {
        return $this->get('groups/'.self::encodePath($group_id).'/hooks/'.self::encodePath($hook_id));
    }

    /**
     * Create a group hook.
     *
     * Hook parameters vary across GitLab versions and are passed through.
     *
     * @see https://docs.gitlab.com/api/group_webhooks/#create-a-group-hook
     *
     * @param array<string,mixed> $parameters
     */
    public function create(int|string $group_id, string $url, array $parameters = []): mixed
    {
        $parameters['url'] = $url;

        return $this->post('groups/'.self::encodePath($group_id).'/hooks', $parameters);
    }

    /**
     * Update a group hook.
     *
     * Hook parameters vary across GitLab versions and are passed through.
     *
     * @see https://docs.gitlab.com/api/group_webhooks/#update-a-group-hook
     *
     * @param array<string,mixed> $parameters
     */
    public function update(int|string $group_id, int $hook_id, array $parameters): mixed
    {
        return $this->put('groups/'.self::encodePath($group_id).'/hooks/'.self::encodePath($hook_id), $parameters);
    }

    public function remove(int|string $group_id, int $hook_id): mixed
    {
        return $this->delete('groups/'.self::encodePath($group_id).'/hooks/'.self::encodePath($hook_id));
    }

    /**
     * @param array $parameters {
     *
     *     @var int|string $status   response status code or status category
     * }
     */
    public function events(int|string $group_id, int $hook_id, array $parameters = []): mixed
    {
        $resolver = new OptionsResolver();
        $resolver->setDefined('status')
            ->setAllowedTypes('status', ['int', 'string'])
        ;

        return $this->get('groups/'.self::encodePath($group_id).'/hooks/'.self::encodePath($hook_id).'/events', $resolver->resolve($parameters));
    }

    public function resendEvent(int|string $group_id, int $hook_id, int $hook_event_id): mixed
    {
        return $this->post('groups/'.self::encodePath($group_id).'/hooks/'.self::encodePath($hook_id).'/events/'.self::encodePath($hook_event_id).'/resend');
    }

    public function test(int|string $group_id, int $hook_id, string $trigger): mixed
    {
        return $this->post('groups/'.self::encodePath($group_id).'/hooks/'.self::encodePath($hook_id).'/test/'.self::encodePath($trigger));
    }

    public function setCustomHeader(int|string $group_id, int $hook_id, string $key, string $value): mixed
    {
        return $this->put('groups/'.self::encodePath($group_id).'/hooks/'.self::encodePath($hook_id).'/custom_headers/'.self::encodePath($key), [
            'value' => $value,
        ]);
    }

    public function deleteCustomHeader(int|string $group_id, int $hook_id, string $key): mixed
    {
        return $this->delete('groups/'.self::encodePath($group_id).'/hooks/'.self::encodePath($hook_id).'/custom_headers/'.self::encodePath($key));
    }

    public function setUrlVariable(int|string $group_id, int $hook_id, string $key, string $value): mixed
    {
        return $this->put('groups/'.self::encodePath($group_id).'/hooks/'.self::encodePath($hook_id).'/url_variables/'.self::encodePath($key), [
            'value' => $value,
        ]);
    }

    public function deleteUrlVariable(int|string $group_id, int $hook_id, string $key): mixed
    {
        return $this->delete('groups/'.self::encodePath($group_id).'/hooks/'.self::encodePath($hook_id).'/url_variables/'.self::encodePath($key));
    }
}
