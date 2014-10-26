<?php

class DB
{
	private static $link;
	private static $lastInsertId = '';
	
	private static $options = array(
		PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
		PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
	);
	
	public static function connect($host, $user, $password, $database)
	{
		if (!isset(self::$link))
			self::$link = @new PDO("mysql:host=$host;dbname=$database" ,$user, $password, self::$options);
		
		return self::$link;
	}

    public static function connectFromIni($file)
    {
        $file = parse_ini_file($file);

        return self::connect($file['host'], $file['user'], $file['password'], $file['database']);
    }
	
	public static function query($sql, $parameters = array())
	{
		$query = self::$link->prepare($sql);
		$query->execute($parameters);
		
		self::$lastInsertId = self::$link->lastInsertId();
		
		return $query;
	}

    public static function oneQuery($sql, $parameters = array())
    {
        return self::query($sql, $parameters)->fetch();
    }

    public static function moreQuery($sql, $parameters = array())
    {
        return self::query($sql, $parameters)->fetchAll();
    }
	
	public static function lastInsertId()
	{
		return self::$lastInsertId;
	}
}