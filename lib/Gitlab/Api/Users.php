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
        return $this->get('users/'.rawurlencode($id));
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
        return $this->put('users/'.rawurlencode($id), $params);
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function remove($id)
    {
        return $this->delete('users/'.rawurlencode($id));
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
        return $this->get('user/keys/'.rawurlencode($id));
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
        return $this->delete('user/keys/'.rawurlencode($id));
    }

    /**
     * @param int $user_id
     * @return mixed
     */
    public function userKeys($user_id)
    {
        return $this->get('users/'.rawurlencode($user_id).'/keys');
    }

    /*
     * @param int $user_id
     * @param int $key_id
     * @return mixed
     */
    public function userKey($user_id, $key_id)
    {
        return $this->get('users/'.rawurlencode($user_id).'/keys/'.rawurlencode($key_id));
    }

    /**
     * @param int $user_id
     * @param string $title
     * @param string $key
     * @return mixed
     */
    public function createKeyForUser($user_id, $title, $key)
    {
        return $this->post('users/'.rawurlencode($user_id).'/keys', array(
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
        return $this->delete('users/'.rawurlencode($user_id).'/keys/'.rawurlencode($key_id));
    }
}
