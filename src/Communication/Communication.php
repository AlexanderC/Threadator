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
     * @param string $identifier
     * @param string $driverName
     * @param array $driverParameters
     */
    public function __construct($identifier, $driverName, array $driverParameters = [])
    {
        $class = sprintf(self::DRIVER_TPL, ucfirst($driverName));
        $this->driver = new $class($identifier, $driverParameters);
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
     * @param Runtime $runtime
     * @param string $driverName
     * @param array $driverParameters
     * @return Communication
     */
    public static function create(Runtime $runtime, $driverName, array $driverParameters = [])
    {
        return new self(sprintf("_thdt_comm_%s", $runtime->getPid()), $driverName, $driverParameters);
    }

    /**
     * @return void
     */
    public function __clone()
    {
        $this->driver = clone $this->driver;
    }
} 