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

    private function editInputAction()
    {
        if ($this->hasAccess() and isset($_GET['input_id']))
        {
            $from = DateTime::createFromFormat('d_m_Y', $_GET['date']);
            $time = explode('_', $_GET['from']);
            $from->setTime($time[0] == '00' || $time[0] == '0' ? 0 : $time[0], $time[1], 0);

            $to = DateTime::createFromFormat('d_m_Y', $_GET['date']);
            $time = explode('_', $_GET['to']);
            $to->setTime($time[0] == '00' || $time[0] == '0' ? 0 : $time[0], $time[1], 0);

            DB::query('UPDATE `input` SET `name`=?, `from`=?, `to`=?, `note`=? WHERE id=?', [
                $_GET['name'],
                $from->format('Y-m-d H:i:s'),
                $to->format('Y-m-d H:i:s'),
                $_GET['text'] == 'null' ? null : $_GET['text'],
                $_GET['input_id']
            ]);

            $this->jsonResponse();
        }
        else
            $this->errorAction('access denied');
    }

    private function inputAction()
    {
        if ($this->hasAccess() and isset($_GET['input_id']))
        {
            $input = DB::oneQuery('SELECT
                    `id`,
                    DATE_FORMAT(`from`, "%e.%m.%Y") AS `date`,
                    `name`,
                    DATE_FORMAT(`from`, "%H:%i") AS `from`,
                    DATE_FORMAT(`to`, "%H:%i") AS `to`,
                    `note`
                  FROM `input` WHERE id=?', [$_GET['input_id']]);

            $this->jsonResponse(['input' => $input]);
        }
        else
            $this->errorAction('access denied');
    }

    private function deleteInputAction()
    {
        if ($this->hasAccess() and isset($_GET['input_id']))
        {
            DB::query('DELETE FROM `input` WHERE id=?', [$_GET['input_id']]);

            $this->jsonResponse();
        }
        else
            $this->errorAction('access denied');
    }

    private function dayAction()
    {
        if ($this->hasAccess())
        {
            $data = DB::moreQuery('SELECT id,
                    `name`,
                    `from`,
                    `to`,
                    `note`,
                    CEIL(TIME_TO_SEC(DATE_FORMAT(`from`, "%H:%i:%s")) / 60) AS from_m,
                    CEIL(TIME_TO_SEC(DATE_FORMAT(`to`, "%H:%i:%s")) / 60)AS to_m,
                    CONCAT(DATE_FORMAT(`from`, "%H:%i"), " - ", DATE_FORMAT(`to`, "%H:%i")) AS `time`
                FROM input WHERE DATE_FORMAT(`from`, "%d_%m_%Y") LIKE ? ORDER BY `from`', [$_GET['date']]);

            $this->jsonResponse(['data' => $data]);
        }
        else
            $this->errorAction('access denied');
    }

    private function newInputAction()
    {
        if ($this->hasAccess())
        {
            $from = DateTime::createFromFormat('d_m_Y', $_GET['date']);
            $time = explode('_', $_GET['from']);
            $from->setTime($time[0] == '00' || $time[0] == '0' ? 0 : $time[0], $time[1], 0);

            $to = DateTime::createFromFormat('d_m_Y', $_GET['date']);
            $time = explode('_', $_GET['to']);
            $to->setTime($time[0] == '00' || $time[0] == '0' ? 0 : $time[0], $time[1], 0);

            $parameters = [
                $_GET['name'],
                $from->format('Y-m-d H:i:s'),
                $to->format('Y-m-d H:i:s'),
                $_GET['text'] == 'null' ? null : $_GET['text']
            ];

            DB::query('INSERT INTO input (`name`, `from`, `to`, `note`) VALUES (?, ?, ?, ?)', $parameters);

            $this->jsonResponse(['sql-data' => $parameters]);
        }
        else
            $this->errorAction('access denied');
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

                $dayNumber = explode('.', $day)[0];

                $weeks[$i]['days'][] = [
                    'day' => $dayNumber[0] == '0' ? $dayNumber[1] : $dayNumber,
                    'url' => str_replace('.', '_', $day),
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