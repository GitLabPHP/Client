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
        return $this->get('users/'.urlencode($id));
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
        return $this->put('users/'.urlencode($id), $params);
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function remove($id)
    {
        return $this->delete('users/'.urlencode($id));
    }

    /**
     * @param string $email
     * @param string $password
     * @return mixed
     */
    public function session($email, $password)
    {
        return $this->post('session', array(
            'email' => $email,
            'password' => $password
        ));
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
        return $this->get('user/keys/'.urlencode($id));
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
        return $this->delete('user/keys/'.urlencode($id));
    }
}
