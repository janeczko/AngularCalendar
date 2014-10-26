<?php

class Api
{
    public function run($action)
    {
        $this->$action();
    }

    private function errorAction()
    {
        $this->jsonResponse(['error' => '404']);
    }

    private function loginAction()
    {
        $user = DB::oneQuery('SELECT id, username, password FROM user WHERE username LIKE ?', [$_GET['username']]);

        if (!$user)
            $this->jsonResponse(['login_error' => 'username does not exist'], false);
        else if ($user['password'] != sha1($_GET['password']))
            $this->jsonResponse(['login_error' => 'bad password'],false);
        else
            $this->jsonResponse($user);
    }

    private function jsonResponse($data = [], $status = true)
    {
        $data['status'] = $status;

        echo json_encode($data);
        exit;
    }
}