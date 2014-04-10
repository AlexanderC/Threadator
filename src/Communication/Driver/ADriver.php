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
     * @var array
     */
    protected $parameters;

    /**
     * @param $identifier
     * @param array $parameters
     */
    public function __construct($identifier, array $parameters = [])
    {
        $this->identifier = (string) $identifier;
        $this->parameters = $parameters;

        call_user_func_array([$this, 'init'], $this->parameters);
    }

    /**
     * @return void
     */
    public function __clone()
    {
        call_user_func_array([$this, 'init'], $this->parameters);
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