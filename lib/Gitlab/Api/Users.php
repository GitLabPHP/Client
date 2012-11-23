<?php

namespace Gitlab\Api;

class Users extends AbstractApi
{
    public function all()
    {
        return $this->get('users');
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
}
