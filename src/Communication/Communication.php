<?php
/**
 * @author AlexanderC <self@alexanderc.me>
 * @date 4/7/14
 * @time 9:15 PM
 */

namespace Threadator\Communication;


use Threadator\Runtime;

class Communication
{
    const DRIVER_TPL = "Threadator\\Communication\\Driver\\%s";

    /**
     * @var Driver\ADriver
     */
    protected $driver;

    /**
     * @param string $driverName
     * @param string $identifier
     */
    public function __construct($driverName, $identifier)
    {
        $class = sprintf(self::DRIVER_TPL, ucfirst($driverName));
        $this->driver = new $class($identifier);
    }

    /**
     * @return \Threadator\Communication\Driver\ADriver
     */
    public function getDriver()
    {
        return $this->driver;
    }

    /**
     * @param int $key
     * @param mixed $message
     * @return bool
     */
    public function send($key, $message)
    {
        return $this->driver->send($key, $message);
    }

    /**
     * Try to get message, but do not block
     *
     * @param int $key
     * @param mixed $message
     * @return bool
     */
    public function touch($key, & $message)
    {
        return $this->driver->touch($key, $message);
    }

    /**
     * Block until the first message arrives
     *
     * @param int $key
     * @param mixed $message
     * @return bool
     */
    public function receive($key, & $message)
    {
        return $this->driver->receive($key, $message);
    }

    /**
     * @param string $driverName
     * @param Runtime $runtime
     * @return Communication
     */
    public static function create($driverName, Runtime $runtime)
    {
        return new self($driverName, sprintf("_thdt_comm_%s", $runtime->getPid()));
    }

    /**
     * @return void
     */
    public function __clone()
    {
        $this->driver = clone $this->driver;
    }
} 