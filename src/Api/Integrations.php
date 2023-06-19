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

use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Integrations extends AbstractApi
{
    /**
     * @param int|string $project_id
     *
     * @return mixed
     */
    public function all($project_id)
    {
        $path = $this->getProjectPath($project_id, 'integrations');
        return $this->get($path);
    }

    // Microsoft Teams

    /**
     * Create Microsoft Teams integration
     * Set Microsoft Teams integration for a project.
     *
     * @param int|string $project_id
     * @param array      $params {
     *
     *     @var string $webhook                      The Microsoft Teams webhook
     *     @var bool   $notify_only_broken_pipelines Send notifications for broken pipelines
     *     @var string $branches_to_be_notified      Branches to send notifications for. Valid options are all, default,
     *                                               protected, and default_and_protected. The default value is "default"
     *     @var bool   $push_events                  Enable notifications for push events
     *     @var bool   $issues_events                Enable notifications for issue events
     *     @var bool   $confidential_issues_events   Enable notifications for confidential issue events
     *     @var bool   $merge_requests_events        Enable notifications for merge request events
     *     @var bool   $tag_push_events              Enable notifications for tag push events
     *     @var bool   $note_events                  Enable notifications for note events
     *     @var bool   $confidential_note_events     Enable notifications for confidential note events
     *     @var bool   $pipeline_events              Enable notifications for pipeline events
     *     @var bool   $wiki_page_events             Enable notifications for wiki page events
     * }
     *
     *     @return mixed
     */
    public function createMicrosoftTeams($project_id, array $params = [])
    {
        $resolver = new OptionsResolver();
        $booleanNormalizer = function (Options $resolver, $value): string {
            return $value ? 'true' : 'false';
        };

        $resolver->setDefined('webhook')
            ->setAllowedTypes('webhook', 'string')
            ->setRequired('webhook')
        ;
        $resolver->setDefined('notify_only_broken_pipelines')
            ->setAllowedTypes('notify_only_broken_pipelines', 'bool')
            ->setNormalizer('notify_only_broken_pipelines', $booleanNormalizer)
        ;
        $resolver->setDefined('branches_to_be_notified')
            ->setAllowedTypes('branches_to_be_notified', 'string')
            ->setAllowedValues('branches_to_be_notified', ['all', 'default', 'protected', 'default_and_protected'])
        ;
        $resolver->setDefined('push_events')
            ->setAllowedTypes('push_events', 'bool')
            ->setNormalizer('push_events', $booleanNormalizer)
        ;
        $resolver->setDefined('issues_events')
            ->setAllowedTypes('issues_events', 'bool')
            ->setNormalizer('issues_events', $booleanNormalizer)
        ;
        $resolver->setDefined('confidential_issues_events')
            ->setAllowedTypes('confidential_issues_events', 'bool')
            ->setNormalizer('confidential_issues_events', $booleanNormalizer)
        ;
        $resolver->setDefined('merge_requests_events')
            ->setAllowedTypes('merge_requests_events', 'bool')
            ->setNormalizer('merge_requests_events', $booleanNormalizer)
        ;
        $resolver->setDefined('tag_push_events')
            ->setAllowedTypes('tag_push_events', 'bool')
            ->setNormalizer('tag_push_events', $booleanNormalizer)
        ;
        $resolver->setDefined('note_events')
            ->setAllowedTypes('note_events', 'bool')
            ->setNormalizer('note_events', $booleanNormalizer)
        ;
        $resolver->setDefined('confidential_note_events')
            ->setAllowedTypes('confidential_note_events', 'bool')
            ->setNormalizer('confidential_note_events', $booleanNormalizer)
        ;
        $resolver->setDefined('pipeline_events')
            ->setAllowedTypes('pipeline_events', 'bool')
            ->setNormalizer('pipeline_events', $booleanNormalizer)
        ;
        $resolver->setDefined('wiki_page_events')
            ->setAllowedTypes('wiki_page_events', 'bool')
            ->setNormalizer('wiki_page_events', $booleanNormalizer)
        ;

        return $this->put($this->getProjectPath($project_id, 'integrations/microsoft-teams'), $resolver->resolve($params));
    }

    /**
     * Update Microsoft Teams integration
     * Set Microsoft Teams integration for a project.
     *
     * @param int|string $project_id
     * @param array      $params {
     *
     *     @var string $webhook                      The Microsoft Teams webhook
     *     @var bool   $notify_only_broken_pipelines Send notifications for broken pipelines
     *     @var string $branches_to_be_notified      Branches to send notifications for. Valid options are all, default,
     *                                               protected, and default_and_protected. The default value is "default"
     *     @var bool   $push_events                  Enable notifications for push events
     *     @var bool   $issues_events                Enable notifications for issue events
     *     @var bool   $confidential_issues_events   Enable notifications for confidential issue events
     *     @var bool   $merge_requests_events        Enable notifications for merge request events
     *     @var bool   $tag_push_events              Enable notifications for tag push events
     *     @var bool   $note_events                  Enable notifications for note events
     *     @var bool   $confidential_note_events     Enable notifications for confidential note events
     *     @var bool   $pipeline_events              Enable notifications for pipeline events
     *     @var bool   $wiki_page_events             Enable notifications for wiki page events
     * }
     *
     *     @return mixed
     */
    public function updateMicrosoftTeams($project_id, array $params = [])
    {
        return $this->createMicrosoftTeams($project_id, $params);
    }

    /**
     * Get Microsoft Teams integration settings for a project.
     *
     * @param int|string $project_id
     *
     * @return mixed
     */
    public function getMicrosoftTeams($project_id)
    {
        return $this->get($this->getProjectPath($project_id, 'integrations/microsoft-teams'));
    }

    /**
     * Disable the Microsoft Teams integration for a project. Integration settings are reset.
     *
     * @param int|string $project_id
     *
     * @return mixed
     */
    public function removeMicrosoftTeams($project_id)
    {
        return $this->delete($this->getProjectPath($project_id, 'integrations/microsoft-teams'));
    }


    // JIRA

    /**
     * Create Jira integration
     * Set Jira integration for a project.
     *
     * @param int|string $project_id
     * @param array      $params {
     *
     *     @var string $url                     The URL to the Jira project which is being linked to this GitLab project
     *     @var bool   $api_url                 The base URL to the Jira instance API. Web URL value is used if not set
     *     @var string $username                The email or username to be used with Jira. For Jira Cloud use an email,
     *                                          for Jira Data Center and Jira Server use a username. Required when using
     *                                          Basic authentication (jira_auth_type is 0)
     *     @var string $password                The Jira API token, password, or personal access token to be used with
     *                                          Jira. When your authentication method is Basic (jira_auth_type is 0) use
     *                                          an API token for Jira Cloud, or a password for Jira Data Center or Jira
     *                                          Server. When your authentication method is Jira personal access token
     *                                          (jira_auth_type is 1) use a personal access token.
     *     @var string $active                  Activates or deactivates the integration. Defaults to false (deactivated).
     *     @var string $jira_auth_type          The authentication method to be used with Jira. 0 means Basic
     *                                          Authentication. 1 means Jira personal access token. Defaults to 0.
     *     @var string $jira_issue_prefix       Prefix to match Jira issue keys
     *     @var string $jira_issue_regex        Regular expression to match Jira issue keys
     *     @var string $jira_issue_transition_automatic     Enable automatic issue transitions. Takes precedence over
     *                                                      jira_issue_transition_id if enabled. Defaults to false
     *     @var string $jira_issue_transition_id            The ID of one or more transitions for custom issue
     *                                                      transitions. Ignored if jira_issue_transition_automatic is
     *                                                      enabled. Defaults to a blank string, which disables custom
     *                                                      transitions.
     *     @var string $commit_events           Enable notifications for commit events
     *     @var string $merge_requests_events   Enable notifications for merge request events
     *     @var string $comment_on_event_enabled            Enable comments inside Jira issues on each GitLab event
     *                                                      (commit / merge request)
     * }
     *
     *     @return mixed
     */
    public function createJira($project_id, array $params = [])
    {
        $resolver = new OptionsResolver();
        $booleanNormalizer = function (Options $resolver, $value): string {
            return $value ? 'true' : 'false';
        };

        $resolver->setDefined('url')
            ->setAllowedTypes('url', 'string')
            ->setRequired('url')
        ;
        $resolver->setDefined('api_url')
            ->setAllowedTypes('api_url', 'string')
        ;
        $resolver->setDefined('username')
            ->setAllowedTypes('username', 'string')
        ;
        $resolver->setDefined('password')
            ->setAllowedTypes('password', 'string')
            ->setRequired('password')
        ;
        $resolver->setDefined('active')
            ->setAllowedTypes('active', 'bool')
            ->setNormalizer('active', $booleanNormalizer)
        ;
        $resolver->setDefined('jira_auth_type')
            ->setAllowedTypes('jira_auth_type', 'int')
        ;
        $resolver->setDefined('jira_issue_prefix')
            ->setAllowedTypes('jira_issue_prefix', 'string')
        ;
        $resolver->setDefined('jira_issue_regex')
            ->setAllowedTypes('jira_issue_regex', 'string')
        ;
        $resolver->setDefined('jira_issue_transition_automatic')
            ->setAllowedTypes('jira_issue_transition_automatic', 'bool')
            ->setNormalizer('jira_issue_transition_automatic', $booleanNormalizer)
        ;
        $resolver->setDefined('jira_issue_transition_id')
            ->setAllowedTypes('jira_issue_transition_id', 'string')
        ;
        $resolver->setDefined('commit_events')
            ->setAllowedTypes('commit_events', 'bool')
            ->setNormalizer('commit_events', $booleanNormalizer)
        ;
        $resolver->setDefined('merge_requests_events')
            ->setAllowedTypes('merge_requests_events', 'bool')
            ->setNormalizer('merge_requests_events', $booleanNormalizer)
        ;
        $resolver->setDefined('comment_on_event_enabled')
            ->setAllowedTypes('comment_on_event_enabled', 'bool')
            ->setNormalizer('comment_on_event_enabled', $booleanNormalizer)
        ;

        return $this->put($this->getProjectPath($project_id, 'integrations/jira'), $resolver->resolve($params));
    }

    /**
     * Update Jira integration
     * Set Jira integration for a project.
     *
     * @param int|string $project_id
     * @param array      $params {
     *
     *     @var string $url                     The URL to the Jira project which is being linked to this GitLab project
     *     @var bool   $api_url                 The base URL to the Jira instance API. Web URL value is used if not set
     *     @var string $username                The email or username to be used with Jira. For Jira Cloud use an email,
     *                                          for Jira Data Center and Jira Server use a username. Required when using
     *                                          Basic authentication (jira_auth_type is 0)
     *     @var string $password                The Jira API token, password, or personal access token to be used with
     *                                          Jira. When your authentication method is Basic (jira_auth_type is 0) use
     *                                          an API token for Jira Cloud, or a password for Jira Data Center or Jira
     *                                          Server. When your authentication method is Jira personal access token
     *                                          (jira_auth_type is 1) use a personal access token.
     *     @var string $active                  Activates or deactivates the integration. Defaults to false (deactivated).
     *     @var string $jira_auth_type          The authentication method to be used with Jira. 0 means Basic
     *                                          Authentication. 1 means Jira personal access token. Defaults to 0.
     *     @var string $jira_issue_prefix       Prefix to match Jira issue keys
     *     @var string $jira_issue_regex        Regular expression to match Jira issue keys
     *     @var string $jira_issue_transition_automatic     Enable automatic issue transitions. Takes precedence over
     *                                                      jira_issue_transition_id if enabled. Defaults to false
     *     @var string $jira_issue_transition_id            The ID of one or more transitions for custom issue
     *                                                      transitions. Ignored if jira_issue_transition_automatic is
     *                                                      enabled. Defaults to a blank string, which disables custom
     *                                                      transitions.
     *     @var string $commit_events           Enable notifications for commit events
     *     @var string $merge_requests_events   Enable notifications for merge request events
     *     @var string $comment_on_event_enabled            Enable comments inside Jira issues on each GitLab event
     *                                                      (commit / merge request)
     * }
     *
     *     @return mixed
     */
    public function updateJira($project_id, array $params = [])
    {
        return $this->createJira($project_id, $params);
    }

    /**
     * Get Jira integration settings for a project.
     *
     * @param int|string $project_id
     *
     * @return mixed
     */
    public function getJira($project_id)
    {
        return $this->get($this->getProjectPath($project_id, 'integrations/jira'));
    }

    /**
     * Disable the Jira integration for a project. Integration settings are reset.
     *
     * @param int|string $project_id
     *
     * @return mixed
     */
    public function removeJira($project_id)
    {
        return $this->delete($this->getProjectPath($project_id, 'integrations/jira'));
    }

}
