<?php

class Router
{
    private $action;

    public function __construct()
    {
        if (isset($_GET['login']))
            $this->action = 'loginAction';
        else if (isset($_GET['calendar']))
            $this->action = 'calendarAction';
        else
            $this->action = 'errorAction';
    }

    public function getAction()
    {
        return $this->action;
    }
}