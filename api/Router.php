<?php

class Router
{
    private $action;

    public function __construct()
    {
        if (isset($_GET['api_key']))
        {
            if (isset($_GET['login']))
                $this->action = 'loginAction';
            else
                $this->action = 'errorAction';
        }
        else
            $this->action = 'errorAction';
    }

    public function getAction()
    {
        return $this->action;
    }
}