<?php
/**
 * @author AlexanderC <self@alexanderc.me>
 * @date 4/9/14
 * @time 9:58 AM
 */

/** Class definition */
class EchoThread extends \Threadator\Thread
{
    /**
     * @var
     */
    protected $stringToEcho;

    /**
     * @param mixed $stringToEcho
     */
    public function setStringToEcho($stringToEcho)
    {
        $this->stringToEcho = (string) $stringToEcho;
    }

    /**
     * @return mixed
     */
    public function getStringToEcho()
    {
        return $this->stringToEcho;
    }

    /**
     * @return void
     */
    protected function _run()
    {
        // send a message
        $this->sendMessage("#{$this->getPid()} ok");

        sleep(mt_rand(1, 3));
        echo $this->stringToEcho, "\n";
    }

    /**
     * @return void
     */
    protected function unload()
    {
        // TODO: Implement unload() method.
    }

    /**
     * @return void
     */
    protected function init()
    {
        // TODO: Implement init() method.
    }
}