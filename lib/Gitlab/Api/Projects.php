<?php namespace Gitlab\Api;

class Projects extends AbstractApi
{
    const ORDER_BY = 'created_at';
    const SORT = 'asc';

    /**
     * @param int $page
     * @param int $per_page
     * @param string $order_by
     * @param string $sort
     * @return mixed
     */
    public function all($page = 1, $per_page = self::PER_PAGE, $order_by = self::ORDER_BY, $sort = self::SORT)
    {
        return $this->get('projects/all', array(
            'page' => $page,
            'per_page' => $per_page,
            'order_by' => $order_by,
            'sort' => $sort
        ));
    }

    /**
     * @param int $page
     * @param int $per_page
     * @param string $order_by
     * @param string $sort
     * @return mixed
     */
    public function accessible($page = 1, $per_page = self::PER_PAGE, $order_by = self::ORDER_BY, $sort = self::SORT)
    {
        return $this->get('projects', array(
            'page' => $page,
            'per_page' => $per_page,
            'order_by' => $order_by,
            'sort' => $sort
        ));
    }

    /**
     * @param int $page
     * @param int $per_page
     * @param string $order_by
     * @param string $sort
     * @return mixed
     */
    public function owned($page = 1, $per_page = self::PER_PAGE, $order_by = self::ORDER_BY, $sort = self::SORT)
    {
        return $this->get('projects/owned', array(
            'page' => $page,
            'per_page' => $per_page,
            'order_by' => $order_by,
            'sort' => $sort
        ));
    }

    /**
     * @param string $query
     * @param int $page
     * @param int $per_page
     * @param string $order_by
     * @param string $sort
     * @return mixed
     */
    public function search($query, $page = 1, $per_page = self::PER_PAGE, $order_by = self::ORDER_BY, $sort = self::SORT)
    {
        return $this->get('projects/search/'.$this->encodePath($query), array(
            'page' => $page,
            'per_page' => $per_page,
            'order_by' => $order_by,
            'sort' => $sort
        ));
    }

    /**
     * @param int $project_id
     * @return mixed
     */
    public function show($project_id)
    {
        return $this->get('projects/'.$this->encodePath($project_id));
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

        return $this->post('projects/user/'.$this->encodePath($user_id), $params);
    }

    /**
     * @param int $project_id
     * @param array $params
     * @return mixed
     */
    public function update($project_id, array $params)
    {
        return $this->put('projects/'.$this->encodePath($project_id), $params);
    }

    /**
     * @param int $project_id
     * @return mixed
     */
    public function remove($project_id)
    {
        return $this->delete('projects/'.$this->encodePath($project_id));
    }

    /**
     * @param int $project_id
     * @return mixed
     */
    public function archive($project_id){
        return $this->post("projects/".$this->encodePath($project_id)."/archive");
    }

    /**
     * @param int $project_id
     * @return mixed
     */
    public function unarchive($project_id){
        return $this->post("projects/".$this->encodePath($project_id)."/unarchive");
    }

    /**
     * @param int $project_id
     * @param array $scope
     * @return mixed
     */
    public function builds($project_id, $scope = null)
    {
        return $this->get($this->getProjectPath($project_id, 'builds'), array(
            'scope' => $scope
        ));
    }

    /**
     * @param $project_id
     * @param $build_id
     * @return mixed
     */
    public function build($project_id, $build_id)
    {
        return $this->get($this->getProjectPath($project_id, 'builds/'.$this->encodePath($build_id)));
    }

    /**
     * @param $project_id
     * @param $build_id
     * @return mixed
     */
    public function trace($project_id, $build_id)
    {
        return $this->get($this->getProjectPath($project_id, 'builds/'.$this->encodePath($build_id).'/trace'));
    }

    /**
     * @param int $project_id
     * @return mixed
     */
    public function pipelines($project_id)
    {
        return $this->get($this->getProjectPath($project_id, 'pipelines'));
    }

    /**
     * @param int $project_id
     * @param int $pipeline_id
     * @return mixed
     */
    public function pipeline($project_id, $pipeline_id)
    {
        return $this->get($this->getProjectPath($project_id, 'pipelines'.$this->encodePath($pipeline_id)));
    }

    /**
     * @param int $project_id
     * @param string $commit_ref
     * @return mixed
     */
    public function createPipeline($project_id, $commit_ref)
    {
        return $this->post($this->getProjectPath($project_id, 'pipelines'), array(
            'ref' => $commit_ref));
    }

    /**
     * @param int $project_id
     * @param int $pipeline_id
     * @return mixed
     */
    public function retryPipeline($project_id, $pipeline_id)
    {
        return $this->post($this->getProjectPath($project_id, 'pipelines'.$this->encodePath($pipeline_id)).'/retry');
    }

    /**
     * @param int $project_id
     * @param int $pipeline_id
     * @return mixed
     */
    public function cancelPipeline($project_id, $pipeline_id)
    {
        return $this->post($this->getProjectPath($project_id, 'pipelines'.$this->encodePath($pipeline_id)).'/cancel');
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
        return $this->get($this->getProjectPath($project_id, 'members/'.$this->encodePath($user_id)));
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
     * @return mixed
     */
    public function deployKeys($project_id)
    {
        return $this->get($this->getProjectPath($project_id, 'deploy_keys'));
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
        return $this->get($this->getProjectPath($project_id, 'hooks/'.$this->encodePath($hook_id)));
    }

    /**
     * @param int $project_id
     * @param string $url
     * @param array $params
     * @return mixed
     */
    public function addHook($project_id, $url, array $params = array())
    {
        if (empty($params)) {
            $params = array('push_events' => true);
        }

        $params['url'] = $url;

        return $this->post($this->getProjectPath($project_id, 'hooks'), $params);
    }

    /**
     * @param int $project_id
     * @param int $hook_id
     * @param array $params
     * @return mixed
     */
    public function updateHook($project_id, $hook_id, array $params)
    {
        return $this->put($this->getProjectPath($project_id, 'hooks/'.$this->encodePath($hook_id)), $params);
    }

    /**
     * @param int $project_id
     * @param int $hook_id
     * @return mixed
     */
    public function removeHook($project_id, $hook_id)
    {
        return $this->delete($this->getProjectPath($project_id, 'hooks/'.$this->encodePath($hook_id)));
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
        return $this->get($this->getProjectPath($project_id, 'keys/'.$this->encodePath($key_id)));
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
        return $this->delete($this->getProjectPath($project_id, 'keys/'.$this->encodePath($key_id)));
    }

    /**
     * @param int $project_id
     * @param int $key_id
     * @return mixed
     */
    public function enableKey($project_id, $key_id)
    {
        return $this->post($this->getProjectPath($project_id, 'keys/'.$this->encodePath($key_id).'/enable'));
    }

    /**
     * @param int $project_id
     * @param int $key_id
     * @return mixed
     */
    public function disableKey($project_id, $key_id)
    {
        return $this->delete($this->getProjectPath($project_id, 'keys/'.$this->encodePath($key_id).'/disable'));
    }

    /**
     * @param int $project_id
     * @param int $page
     * @param int $per_page
     * @return mixed
     */
    public function events($project_id, $page = 1, $per_page = self::PER_PAGE)
    {
        return $this->get($this->getProjectPath($project_id, 'events'), array(
            'page' => $page,
            'per_page' => $per_page
        ));
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
        return $this->post($this->getProjectPath($project_id, 'labels'), $params);
    }

    /**
     * @param int $project_id
     * @param array $params
     * @return mixed
     */
    public function updateLabel($project_id, array $params)
    {
        return $this->put($this->getProjectPath($project_id, 'labels'), $params);
    }

    /**
     * @param int $project_id
     * @param string $name
     * @return mixed
     */
    public function removeLabel($project_id, $name)
    {
        return $this->delete($this->getProjectPath($project_id, 'labels'), array(
            'name' => $name
        ));
    }

    /**
     * @param int $project_id
     * @return mixed
     */
    public function fork($project_id)
    {
        return $this->post('projects/fork/'.$this->encodePath($project_id));
    }

    /**
     * @param int $project_id
     * @param int $forked_project_id
     * @return mixed
     */
    public function createForkRelation($project_id, $forked_project_id)
    {
        return $this->post($this->getProjectPath($project_id, 'fork/'.$this->encodePath($forked_project_id)));
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
        return $this->put($this->getProjectPath($project_id, 'services/'.$this->encodePath($service_name)), $params);
    }

    /**
     * @param int $project_id
     * @param string $service_name
     * @return mixed
     */
    public function removeService($project_id, $service_name)
    {
        return $this->delete($this->getProjectPath($project_id, 'services/'.$this->encodePath($service_name)));
    }

    /**
     * @param int $project_id
     * @return mixed
     */
    public function variables($project_id)
    {
        return $this->get($this->getProjectPath($project_id, 'variables'));
    }

    /**
     * @param int $project_id
     * @param string $key
     * @return mixed
     */
    public function variable($project_id, $key)
    {
        return $this->get($this->getProjectPath($project_id, 'variables/'.$this->encodePath($key)));
    }

    /**
     * @param int $project_id
     * @param string $key
     * @param string $value
     * @return mixed
     */
    public function addVariable($project_id, $key, $value)
    {
        return $this->post($this->getProjectPath($project_id, 'variables'), array(
            'key'   => $key,
            'value' => $value
        ));
    }

    /**
     * @param int $project_id
     * @param string $key
     * @param string $value
     * @return mixed
     */
    public function updateVariable($project_id, $key, $value)
    {
        return $this->put($this->getProjectPath($project_id, 'variables/'.$this->encodePath($key)), array(
            'value' => $value,
        ));
    }

    /**
     * @param int $project_id
     * @param string $key
     * @return mixed
     */
    public function removeVariable($project_id, $key)
    {
        return $this->delete($this->getProjectPath($project_id, 'variables/'.$this->encodePath($key)));
    }

    /**
     * @param int $project_id
     * @param string $file
     * @return mixed
     */
    public function uploadFile($project_id, $file)
    {
        return $this->post($this->getProjectPath($project_id, 'uploads'), array(), array(), array('file' => $file));
    }

    /**
     * @param int $project_id
     * @param int $page
     * @param int $per_page
     * @return mixed
     */
    public function deployments($project_id, $page = 1, $per_page = self::PER_PAGE)
    {
        return $this->get($this->getProjectPath($project_id, 'deployments'), array(
            'page' => $page,
            'per_page' => $per_page
        ));
    }

    /**
     * @param int $project_id
     * @param int $deployment_id
     * @return mixed
     */
    public function deployment($project_id, $deployment_id)
    {
        return $this->get($this->getProjectPath($project_id, 'deployments/'.$this->encodePath($deployment_id)));
    }
}
