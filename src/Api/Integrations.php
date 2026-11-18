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

class Integrations extends AbstractApi
{
    /**
     * List all active integrations of a project.
     *
     * @see https://docs.gitlab.com/api/project_integrations/#list-all-active-integrations
     */
    public function all(int|string $project_id): mixed
    {
        return $this->get($this->getProjectPath($project_id, 'integrations'));
    }

    /**
     * Get the settings of a project integration by slug.
     *
     * The response fields vary by integration slug; see the dedicated section for
     * that integration (e.g. Asana, Jira, Slack notifications, ...) on the page below.
     *
     * @see https://docs.gitlab.com/api/project_integrations/
     */
    public function show(int|string $project_id, string $integration_slug): mixed
    {
        return $this->get($this->getProjectPath($project_id, 'integrations/'.self::encodePath($integration_slug)));
    }

    /**
     * Configure a project integration by slug.
     *
     * Integration parameters vary by slug and are passed through to GitLab; see the
     * dedicated section for that integration (e.g. Asana, Jira, Slack notifications, ...)
     * on the page below.
     *
     * @see https://docs.gitlab.com/api/project_integrations/
     *
     * @param array<string,mixed> $parameters
     */
    public function set(int|string $project_id, string $integration_slug, array $parameters = []): mixed
    {
        return $this->put($this->getProjectPath($project_id, 'integrations/'.self::encodePath($integration_slug)), $parameters);
    }

    /**
     * Disable a project integration by slug.
     *
     * See the dedicated section for that integration (e.g. Asana, Jira, Slack
     * notifications, ...) on the page below.
     *
     * @see https://docs.gitlab.com/api/project_integrations/
     */
    public function remove(int|string $project_id, string $integration_slug): mixed
    {
        return $this->delete($this->getProjectPath($project_id, 'integrations/'.self::encodePath($integration_slug)));
    }
}
