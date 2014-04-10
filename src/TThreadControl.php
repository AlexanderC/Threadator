<?php
/**
 * @author AlexanderC <self@alexanderc.me>
 * @date 4/9/14
 * @time 3:49 PM
 */

namespace Threadator;


trait TThreadControl
{
    /**
     * @throws \RuntimeException
     */
    final public function detach()
    {
        if(-1 === posix_setsid()) {
            throw new \RuntimeException("Unable to detach thread");
        }

        $this->pid = posix_getpid();
        $this->parentPid = null;

        $this->detached = true;
    }

    /**
     * @return $this
     */
    final public function join()
    {
        pcntl_waitpid($this->pid, $status, WUNTRACED);

        $this->state = IThreadState::JOINED;

        return $this;
    }

    /**
     * @return $this
     * @throws \RuntimeException
     */
    public function start()
    {
        if(!$this->isStopped()) {
            throw new \RuntimeException("Thread should be stopped before");
        }

        $this->state = IThreadState::RUNNING;

        return $this;
    }

    /**
     * @return $this
     * @throws \RuntimeException
     */
    public function stop()
    {
        if(!$this->isRunning()) {
            throw new \RuntimeException("Thread is not running");
        }

        $this->state = IThreadState::STOPPED;

        return $this;
    }
} 