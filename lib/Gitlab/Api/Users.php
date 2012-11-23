<?php

namespace Gitlab\Api;

class Users extends AbstractApi
{
    public function all()
    {
        return $this->get('users');
    }

    public function show($id = null)
    {
        $path = 'users';

        if (null !== $id) {
            $path .= '/'.urlencode($id);
        }

        return $this->get($path);
    }

    public function create($email, $password, array $params = array())
    {
        $params['email']    = $email;
        $params['password'] = $password;

        return $this->post('users', $params);
    }

    public function session($email, $password)
    {
        return $this->post('session', array(
            'email' => $email,
            'password' => $password
        ));
    }
}
