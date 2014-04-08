<?php
/**
 * @author AlexanderC <self@alexanderc.me>
 * @date 4/8/14
 * @time 12:01 AM
 */

namespace Threadator\Communication\Driver;


abstract class ADriver
{
    /**
     * @var string
     */
    protected $identifier;

    /**
     * @param string $identifier
     */
    public function __construct($identifier)
    {
        $this->identifier = (string) $identifier;

        $this->init();
    }

    /**
     * @return void
     */
    public function __clone()
    {
        $this->init();
    }

    /**
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * @return void
     */
    abstract protected function init();

    /**
     * @param int $key
     * @param mixed $message
     * @return bool
     */
    abstract public function send($key, $message);

    /**
     * Try to get message, but do not block
     *
     * @param int $key
     * @param mixed $message
     * @return bool
     */
    abstract public function touch($key, & $message);

    /**
     * Block until the first message arrives
     *
     * @param int $key
     * @param mixed $message
     * @return bool
     */
    abstract public function receive($key, & $message);
} 