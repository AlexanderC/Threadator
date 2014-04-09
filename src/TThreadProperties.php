<?php
/**
 * @author AlexanderC <self@alexanderc.me>
 * @date 4/9/14
 * @time 3:46 PM
 */

namespace Threadator;


trait TThreadProperties
{
    /**
     * @var int
     */
    protected $state = IThreadState::WAITING;

    /**
     * @var bool
     */
    protected $detached = false;

    /**
     * @var Runtime
     */
    protected $runtime;

    /**
     * @var Communication
     */
    protected $communication;

    /**
     * @var int
     */
    protected $pid;

    /**
     * @var int
     */
    protected $parentPid;

    /**
     * @return \Threadator\Communication\Communication
     */
    public function getCommunication()
    {
        return $this->communication;
    }

    /**
     * @return \Threadator\Runtime
     */
    public function getRuntime()
    {
        return $this->runtime;
    }

    /**
     * @return int
     */
    public function getPid()
    {
        return $this->pid;
    }

    /**
     * @return int
     */
    public function getParentPid()
    {
        return $this->parentPid;
    }

    /**
     * @return bool
     */
    public function isDetached()
    {
        return $this->detached;
    }

    /**
     * @return bool
     */
    public function isWaiting()
    {
        return $this->state === IThreadState::WAITING;
    }

    /**
     * @return bool
     */
    public function isRunning()
    {
        return $this->state === IThreadState::RUNNING || $this->state === IThreadState::JOINED;
    }

    /**
     * @return bool
     */
    public function isJoined()
    {
        return $this->state === IThreadState::JOINED;
    }

    /**
     * @return bool
     */
    public function isStopped()
    {
        return $this->state === IThreadState::STOPPED;
    }
} 