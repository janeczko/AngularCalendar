<?php

class Api
{
    public function run($action)
    {
        $this->$action();
    }

    private function errorAction($message = '404')
    {
        $this->jsonResponse(['error' => $message], false);
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

    private function calendarAction()
    {
        if ($this->hasAccess())
        {
            $firstDay = DateTime::createFromFormat('d_m_Y', $_GET['first_day'])->setTime(0, 0, 0);
            $lastDay = DateTime::createFromFormat('d_m_Y', $firstDay->format('t_m_Y'))->setTime(0, 0, 0);

            $days = [];
            $firstDayNumber = $this->numberOfDay($firstDay->format('D'));
            $help = DateTime::createFromFormat('d_m_Y', $firstDay->format('d_m_Y'));

            if ($firstDayNumber > 1)
            {
                $days[] = $help->modify('-' . ($firstDayNumber - 1) . ' days')->format('d.m.Y');

                for ($i = 0; $i < ($firstDayNumber - 2); $i++)
                    $days[] = $help->modify('+1 days')->format('d.m.Y');
            }

            $days[] = $firstDay->format('d.m.Y');
            $help = DateTime::createFromFormat('d_m_Y', $firstDay->format('d_m_Y'));

            for ($i = 1; $i < $lastDay->format('d'); $i++)
                $days[] = $help->modify('+1 days')->format('d.m.Y');

            $lastDayNumber = $this->numberOfDay($lastDay->format('D'));
            $help = DateTime::createFromFormat('d_m_Y', $lastDay->format('d_m_Y'));

            if ($lastDayNumber < 7)
            {
                for ($i = 0; $i < (7 - $lastDayNumber); $i++)
                    $days[] = $help->modify('+1 days')->format('d.m.Y');
            }

            $weeks = [];
            $loop = 0;
            $i = 0;

            foreach ($days as $day)
            {
                if ($loop > 6)
                {
                    $loop = 0;
                    $i++;
                }

                $weeks[$i]['days'][] = [
                    'day' => $day,
                    'in_month' => $this->isDayInMonth($day, $firstDay)
                ];

                $loop++;
            }

            $this->jsonResponse([
                'weeks' => $weeks
            ]);
        }
        else
            $this->errorAction('access denied');
    }

    private function jsonResponse($data = [], $status = true)
    {
        $data['status'] = $status;

        echo json_encode($data);
        exit;
    }

    private function hasAccess()
    {
        if (!isset($_GET['ac_key']))
            return false;
        else
        {
            $key = explode('_', $_GET['ac_key']);

            if (!isset($key[1]))
                return false;

            $user = DB::oneQuery('SELECT id FROM user WHERE id=? AND password=?', [$key[0], $key[1]]);

            return !$user ? false : true;
        }
    }

    private function numberOfDay($day)
    {
        switch ($day)
        {
            case 'Mon':
                return 1;
            case 'Tue':
                return 2;
            case 'Wed':
                return 3;
            case 'Thu':
                return 4;
            case 'Fri':
                return 5;
            case 'Sat':
                return 6;
            case 'Sun':
                return 7;
            default:
                return 0;
        }
    }

    private function isDayInMonth($day, DateTime $date)
    {
        return explode('.', $day)[1] == $date->format('m');
    }
}