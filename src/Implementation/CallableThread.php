<?php
/**
 * @author AlexanderC <self@alexanderc.me>
 * @date 4/7/14
 * @time 10:32 PM
 */

namespace Threadator\Implementation;


use Threadator\Thread;

class CallableThread extends Thread
{
    /**
     * @var callable
     */
    protected $callable;

    /**
     * @param callable $callable
     * @return $this
     */
    public function setCallable(callable $callable)
    {
        $this->callable = $callable;

        return $this;
    }

    /**
     * @return callable
     */
    public function getCallable()
    {
        return $this->callable;
    }

    /**
     * @return void
     */
    protected function _run()
    {
        call_user_func($this->callable, $this);
    }

    /**
     * @return void
     */
    protected function unload()
    {   }

    /**
     * @return void
     */
    protected function init()
    {   }
} 