<?php

namespace Gitlab\Api;

class Users extends AbstractApi
{
    public function all($active = null)
    {
        if (!is_null($active)) {
            return $this->get('users', array(
                'active' => $active
            ));
        }

        return $this->get('users');
    }

    public function search($query, $active = null)
    {
        return $this->get('users', array(
            'search' => $query,
            'active' => $active
        ));
    }

    public function show($id)
    {
        return $this->get('users/'.urlencode($id));
    }

    public function create($email, $password, array $params = array())
    {
        $params['email']    = $email;
        $params['password'] = $password;

        return $this->post('users', $params);
    }
    
    public function update($id, $params)
    {
        return $this->put(
            sprintf("/users/%d", $id),
            $params
        );
    }
    
    public function remove($id)
    {
        return $this->delete(
            sprintf("/users/%d", $id)
        );
    }

    public function update($id, array $params)
    {
        return $this->put('users/'.urlencode($id), $params);
    }

    public function remove($id)
    {
        return $this->delete('users/'.urlencode($id));
    }

    public function session($email, $password)
    {
        return $this->post('session', array(
            'email' => $email,
            'password' => $password
        ));
    }

    public function me()
    {
        return $this->get('user');
    }

    public function keys()
    {
        return $this->get('user/keys');
    }

    public function key($id)
    {
        return $this->get('user/keys/'.urlencode($id));
    }

    public function createKey($title, $key)
    {
        return $this->post('user/keys', array(
            'title' => $title,
            'key' => $key
        ));
    }

    public function removeKey($id)
    {
        return $this->delete('user/keys/'.urlencode($id));
    }
}
