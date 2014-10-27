<?php

class Router
{
    private $action;

    public function __construct()
    {
        if (isset($_GET['login']))
            $this->action = 'login';
        else if (isset($_GET['calendar']))
            $this->action = 'calendar';
        else if (isset($_GET['day']))
            $this->action = 'day';
        else if (isset($_GET['new_input']))
            $this->action = 'newInput';
        else
            $this->action = 'error';

        $this->action .= 'Action';
    }

    public function getAction()
    {
        return $this->action;
    }
}