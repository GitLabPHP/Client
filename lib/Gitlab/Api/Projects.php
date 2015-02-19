<?php namespace Gitlab\Api;

class Projects extends AbstractApi
{
    /**
     * @param int $page
     * @param int $per_page
     * @return mixed
     */
    public function all($page = 1, $per_page = self::PER_PAGE)
    {
        return $this->get('projects/all', array(
            'page' => $page,
            'per_page' => $per_page
        ));
    }

    /**
     * @param int $page
     * @param int $per_page
     * @return mixed
     */
    public function accessible($page = 1, $per_page = self::PER_PAGE)
    {
        return $this->get('projects', array(
            'page' => $page,
            'per_page' => $per_page
        ));
    }

    /**
     * @param int $page
     * @param int $per_page
     * @return mixed
     */
    public function owned($page = 1, $per_page = self::PER_PAGE)
    {
        return $this->get('projects/owned', array(
            'page' => $page,
            'per_page' => $per_page
        ));
    }

    /**
     * @param string $query
     * @param int $page
     * @param int $per_page
     * @return mixed
     */
    public function search($query, $page = 1, $per_page = self::PER_PAGE)
    {
        return $this->get('projects/search/'.urlencode($query), array(
            'page' => $page,
            'per_page' => $per_page
        ));
    }

    /**
     * @param int $project_id
     * @return mixed
     */
    public function show($project_id)
    {
        return $this->get('projects/'.urlencode($project_id));
    }

    /**
     * @param string $name
     * @param array $params
     * @return mixed
     */
    public function create($name, array $params = array())
    {
        $params['name'] = $name;

        return $this->post('projects', $params);
    }

    /**
     * @param int $user_id
     * @param string $name
     * @param array $params
     * @return mixed
     */
    public function createForUser($user_id, $name, array $params = array())
    {
        $params['name'] = $name;

        return $this->post('projects/user/'.urlencode($user_id), $params);
    }

    /**
     * @param int $project_id
     * @return mixed
     */
    public function remove($project_id)
    {
        return $this->delete('projects/'.urlencode($project_id));
    }

    /**
     * @param int $project_id
     * @param string $username_query
     * @return mixed
     */
    public function members($project_id, $username_query = null)
    {
        return $this->get($this->getProjectPath($project_id, 'members'), array(
            'query' => $username_query
        ));
    }

    /**
     * @param int $project_id
     * @param int $user_id
     * @return mixed
     */
    public function member($project_id, $user_id)
    {
        return $this->get($this->getProjectPath($project_id, 'members/'.urlencode($user_id)));
    }

    /**
     * @param int $project_id
     * @param int $user_id
     * @param int $access_level
     * @return mixed
     */
    public function addMember($project_id, $user_id, $access_level)
    {
        return $this->post($this->getProjectPath($project_id, 'members'), array(
            'user_id' => $user_id,
            'access_level' => $access_level
        ));
    }

    /**
     * @param int $project_id
     * @param int $user_id
     * @param int $access_level
     * @return mixed
     */
    public function saveMember($project_id, $user_id, $access_level)
    {
        return $this->put($this->getProjectPath($project_id, 'members/'.urldecode($user_id)), array(
            'access_level' => $access_level
        ));
    }

    /**
     * @param int $project_id
     * @param int $user_id
     * @return mixed
     */
    public function removeMember($project_id, $user_id)
    {
        return $this->delete($this->getProjectPath($project_id, 'members/'.urldecode($user_id)));
    }

    /**
     * @param int $project_id
     * @param int $page
     * @param int $per_page
     * @return mixed
     */
    public function hooks($project_id, $page = 1, $per_page = self::PER_PAGE)
    {
        return $this->get($this->getProjectPath($project_id, 'hooks'), array(
            'page' => $page,
            'per_page' => $per_page
        ));
    }

    /**
     * @param int $project_id
     * @param int $hook_id
     * @return mixed
     */
    public function hook($project_id, $hook_id)
    {
        return $this->get($this->getProjectPath($project_id, 'hooks/'.urlencode($hook_id)));
    }

    /**
     * @param int $project_id
     * @param string $url
     * @param bool $push_events
     * @param bool $issues_events
     * @param bool $merge_requests_events
     * @param bool $tag_push_events
     * @return mixed
     */
    public function addHook($project_id, $url, $push_events = true, $issues_events = false, $merge_requests_events = false, $tag_push_events = false)
    {
        return $this->post($this->getProjectPath($project_id, 'hooks'), array(
            'url' => $url,
            'push_events' => $push_events,
            'issues_events' => $issues_events,
            'merge_requests_events' => $merge_requests_events,
            'tag_push_events' => $tag_push_events
        ));
    }

    /**
     * @param int $project_id
     * @param int $hook_id
     * @param string $url
     * @param bool $push_events
     * @param bool $issues_events
     * @param bool $merge_requests_events
     * @param bool $tag_push_events
     * @return mixed
     */
    public function updateHook($project_id, $hook_id, $url, $push_events = true, $issues_events = false, $merge_requests_events = false, $tag_push_events = false)
    {
        $params = array(
            'url' => $url
        );

        if ($push_events) {
            $params['push_events'] = $push_events;
        }

        if ($issues_events) {
            $params['issues_events'] = $issues_events;
        }

        if ($merge_requests_events) {
            $params['merge_requests_events'] = $merge_requests_events;
        }

        if ($tag_push_events) {
            $params['tag_push_events'] = $tag_push_events;
        }

        return $this->put($this->getProjectPath($project_id, 'hooks/'.urlencode($hook_id)), $params);
    }

    /**
     * @param int $project_id
     * @param int $hook_id
     * @return mixed
     */
    public function removeHook($project_id, $hook_id)
    {
        return $this->delete($this->getProjectPath($project_id, 'hooks/'.urlencode($hook_id)));
    }

    /**
     * @param int $project_id
     * @return mixed
     */
    public function keys($project_id)
    {
        return $this->get($this->getProjectPath($project_id, 'keys'));
    }

    /**
     * @param int $project_id
     * @param int $key_id
     * @return mixed
     */
    public function key($project_id, $key_id)
    {
        return $this->get($this->getProjectPath($project_id, 'keys/'.urlencode($key_id)));
    }

    /**
     * @param int $project_id
     * @param string $title
     * @param string $key
     * @return mixed
     */
    public function addKey($project_id, $title, $key)
    {
        return $this->post($this->getProjectPath($project_id, 'keys'), array(
            'title' => $title,
            'key' => $key
        ));
    }

    /**
     * @param int $project_id
     * @param int $key_id
     * @return mixed
     */
    public function removeKey($project_id, $key_id)
    {
        return $this->delete($this->getProjectPath($project_id, 'keys/'.urlencode($key_id)));
    }

    /**
     * @param int $project_id
     * @return mixed
     */
    public function events($project_id)
    {
        return $this->get($this->getProjectPath($project_id, 'events'));
    }

    /**
     * @param int $project_id
     * @return mixed
     */
    public function labels($project_id)
    {
        return $this->get($this->getProjectPath($project_id, 'labels'));
    }

    /**
     * @param int $project_id
     * @param array $params
     * @return mixed
     */
    public function addLabel($project_id, array $params)
    {
        return $this->post($this->getProjectPath($project_id, 'labels', $params));
    }

    /**
     * @param int $project_id
     * @param array $params
     * @return mixed
     */
    public function updateLabel($project_id, array $params)
    {
        return $this->put($this->getProjectPath($project_id, 'labels', $params));
    }

    /**
     * @param int $project_id
     * @param array $params
     * @return mixed
     */
    public function removeLabel($project_id, array $params)
    {
        return $this->delete($this->getProjectPath($project_id, 'labels'), $params);
    }

    /**
     * @param int $project_id
     * @param int $forked_project_id
     * @return mixed
     */
    public function createForkRelation($project_id, $forked_project_id)
    {
        return $this->post($this->getProjectPath($project_id, 'fork/'.urlencode($forked_project_id)));
    }

    /**
     * @param int $project_id
     * @return mixed
     */
    public function removeForkRelation($project_id)
    {
        return $this->delete($this->getProjectPath($project_id, 'fork'));
    }

    /**
     * @param int $project_id
     * @param string $service_name
     * @param array $params
     * @return mixed
     */
    public function setService($project_id, $service_name, array $params = array())
    {
        return $this->put($this->getProjectPath($project_id, 'services/'.urlencode($service_name)), $params);
    }

    /**
     * @param int $project_id
     * @param string $service_name
     * @return mixed
     */
    public function removeService($project_id, $service_name)
    {
        return $this->delete($this->getProjectPath($project_id, 'services/'.urlencode($service_name)));
    }
}
