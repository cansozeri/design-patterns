<?php

abstract class Manager
{

    protected $drivers = [];

    abstract public function getDefaultDriver();

    public function driver($driver = null)
    {
        $driver = $driver ?: $this->getDefaultDriver();

        if (is_null($driver)) {
            throw new InvalidArgumentException(sprintf(
                'Unable to resolve NULL driver for [%s].', static::class
            ));
        }

        if (!isset($this->drivers[$driver])) {
            $this->drivers[$driver] = $this->createDriver($driver);
        }
        return $this->drivers[$driver];
    }

    protected function createDriver($driver)
    {

        $method = 'create' . ucfirst($driver) . 'Driver';
        if (method_exists($this, $method)) {
            return $this->$method();
        }

        throw new InvalidArgumentException("Driver [$driver] not supported.");
    }


    public function getDrivers()
    {
        return $this->drivers;
    }

    public function __call($method, $parameters)
    {
        return $this->driver()->$method(...$parameters);
    }
}

class SessionManager extends Manager
{

    protected function createFileDriver()
    {
        return new FileSession('/session/path', 30);
    }

    protected function createDatabaseDriver()
    {
        return new DatabaseSession('mysql_connection_instance', 'session', 60);
    }

    protected function createRedisDriver()
    {
        return new RedisSession('redis_connection_instance', 'redis_cache');
    }

    public function getDefaultDriver()
    {
        return 'redis';
    }
}

class FileSession
{

    protected $path;

    protected $minutes;

    public function __construct($path, $minutes)
    {
        $this->path = $path;
        $this->minutes = $minutes;
    }

    public function getParameters()
    {
        var_dump(get_object_vars($this));
    }
}

class DatabaseSession
{

    protected $connection;

    protected $table;

    protected $minutes;

    public function __construct($connection, $table, $minutes)
    {
        $this->table = $table;
        $this->minutes = $minutes;
        $this->connection = $connection;
    }

    public function getParameters()
    {
        var_dump(get_object_vars($this));
    }

}

class RedisSession
{

    protected $prefix;

    protected $connection;

    public function __construct($connection, $prefix)
    {
        $this->connection = $connection;
        $this->prefix = $prefix;
    }

    public function getParameters()
    {
        var_dump(get_object_vars($this));

    }
}

$manager = new SessionManager();
$driver = $manager->driver();
$driver->getParameters();
