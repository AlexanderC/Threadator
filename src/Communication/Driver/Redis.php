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
     * @return void
     */
    protected function init()
    {
        $this->client = new \Redis();
        $this->client->connect('127.0.0.1');
        $this->client->setOption(\Redis::OPT_SERIALIZER, \Redis::SERIALIZER_PHP);
        $this->client->setOption(\Redis::OPT_PREFIX, sprintf("%s#", base64_encode($this->identifier)));
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
        $this->client->close();
    }
} 