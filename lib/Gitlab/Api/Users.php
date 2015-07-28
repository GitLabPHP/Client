<?php namespace Gitlab\Api;

class Users extends AbstractApi
{
    /**
     * @param null|true $active
     * @param int $page
     * @param int $per_page
     * @return mixed
     */
    public function all($active = null, $page = 1, $per_page = self::PER_PAGE)
    {
        return $this->get('users', array(
            'active' => $active,
            'page' => $page,
            'per_page' => $per_page
        ));
    }

    /**
     * @param string $query
     * @param null|true $active
     * @param int $page
     * @param int $per_page
     * @return mixed
     */
    public function search($query, $active = null, $page = 1, $per_page = self::PER_PAGE)
    {
        return $this->get('users', array(
            'search' => $query,
            'active' => $active,
            'page' => $page,
            'per_page' => $per_page
        ));
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function show($id)
    {
        return $this->get('users/'.$this->encodePath($id));
    }

    /**
     * @param string $email
     * @param string $password
     * @param array $params
     * @return mixed
     */
    public function create($email, $password, array $params = array())
    {
        $params['email']    = $email;
        $params['password'] = $password;

        return $this->post('users', $params);
    }

    /**
     * @param int $id
     * @param array $params
     * @return mixed
     */
    public function update($id, array $params)
    {
        return $this->put('users/'.$this->encodePath($id), $params);
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function remove($id)
    {
        return $this->delete('users/'.$this->encodePath($id));
    }

    /**
     * @param  int $id
     * @return mixed
     */
    public function block($id)
    {
        return $this->put('users/'.$this->encodePath($id).'/block');
    }

    /**
     * @param  int $id
     * @return mixed
     */
    public function unblock($id)
    {
        return $this->put('users/'.$this->encodePath($id).'/unblock');
    }

    /**
     * @param string $emailOrUsername
     * @param string $password
     * @return mixed
     */
    public function session($emailOrUsername, $password)
    {
        return $this->post('session', array(
            'login' => $emailOrUsername,
            'email' => $emailOrUsername,
            'password' => $password
        ));
    }

    /**
     * @param string $email
     * @param string $password
     * @return mixed
     */
    public function login($email, $password)
    {
        return $this->session($email, $password);
    }

    /**
     * @return mixed
     */
    public function me()
    {
        return $this->get('user');
    }

    /**
     * @return mixed
     */
    public function keys()
    {
        return $this->get('user/keys');
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function key($id)
    {
        return $this->get('user/keys/'.$this->encodePath($id));
    }

    /**
     * @param string $title
     * @param string $key
     * @return mixed
     */
    public function createKey($title, $key)
    {
        return $this->post('user/keys', array(
            'title' => $title,
            'key' => $key
        ));
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function removeKey($id)
    {
        return $this->delete('user/keys/'.$this->encodePath($id));
    }

    /**
     * @param int $user_id
     * @return mixed
     */
    public function userKeys($user_id)
    {
        return $this->get('users/'.$this->encodePath($user_id).'/keys');
    }

    /*
     * @param int $user_id
     * @param int $key_id
     * @return mixed
     */
    public function userKey($user_id, $key_id)
    {
        return $this->get('users/'.$this->encodePath($user_id).'/keys/'.$this->encodePath($key_id));
    }

    /**
     * @param int $user_id
     * @param string $title
     * @param string $key
     * @return mixed
     */
    public function createKeyForUser($user_id, $title, $key)
    {
        return $this->post('users/'.$this->encodePath($user_id).'/keys', array(
            'title' => $title,
            'key' => $key
        ));
    }

    /**
     * @param int $user_id
     * @param int $key_id
     * @return mixed
     */
    public function removeUserKey($user_id, $key_id)
    {
        return $this->delete('users/'.$this->encodePath($user_id).'/keys/'.$this->encodePath($key_id));
    }
}
