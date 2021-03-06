<?php
/**
 * @author AlexanderC <self@alexanderc.me>
 * @date 4/8/14
 * @time 10:43 AM
 */

namespace Threadator\Communication\Driver;


class Redis extends ADriver
{
    const WAIT_USLEEP = 300;

    /**
     * @var \Redis
     */
    protected $client;

    /**
     * @var string
     */
    protected $prefix;

    /**
     * @return void
     */
    protected function init()
    {
        $this->prefix = sprintf("%s#", base64_encode($this->identifier));

        $this->client = new \Redis();
        call_user_func_array([$this->client, 'connect'], func_get_args());
        $this->client->setOption(\Redis::OPT_SERIALIZER, \Redis::SERIALIZER_PHP);
        $this->client->setOption(\Redis::OPT_PREFIX, $this->prefix);
    }

    /**
     * @return \Redis
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @param int $key
     * @param mixed $message
     * @return bool
     */
    public function send($key, $message)
    {
        $result = 0 < $this->client->rPush($key, $message);

        return $result;
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
        if($this->client->lSize($key) > 0) {
            $message = $this->client->lPop($key);

            return true;
        }

        return false;
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
        while($this->client->lSize($key) <= 0) {
            usleep(self::WAIT_USLEEP);
        }

        $message = $this->client->lPop($key);

        return true;
    }

    /**
     * @return void
     */
    public function __destruct()
    {
        // clean all messages sent and not parsed to skip unexpected errors
        // on next run...
        // Note: all keys have full name, trim keys until getting them without prefix
        $prefixOffset = strlen($this->prefix);
        $keys = array_map(function($key) use ($prefixOffset) {
                return substr($key, $prefixOffset);
            }, array_filter($this->client->keys("*"), function($key) {
                 return preg_match(sprintf("/^(%s)/", preg_quote($this->prefix, "/")), $key);
            }));

        $this->client->delete($keys);

        $this->client->close();
    }
} 