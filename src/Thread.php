<?php
/**
 * @author AlexanderC <self@alexanderc.me>
 * @date 4/7/14
 * @time 9:09 PM
 */

namespace Threadator;


use Threadator\Communication\Communication;
use Threadator\Communication\TThreadCommunication;

abstract class Thread implements IThreadState
{
    use TThreadCommunication;
    use TThreadMutex;

    /**
     * @var int
     */
    protected $state = self::WAITING;

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
     * @return void
     */
    abstract protected function _run();

    /**
     * @return void
     */
    abstract protected function unload();

    /**
     * @return void
     */
    abstract protected function init();

    /**
     * @param Runtime $runtime
     * @throws \RuntimeException
     */
    public function __construct(Runtime $runtime)
    {
        $this->runtime = $runtime;
        $this->communication = $this->runtime->getCommunication();
        $this->runtime->push($this);
        $this->parentPid = $this->runtime->getPid();

        $this->init();
    }

    /**
     * @return $this
     * @throws \RuntimeException
     */
    public function run()
    {
        $this->state = self::RUNNING;

        if(-1 === ($this->pid = pcntl_fork())) {
            throw new \RuntimeException("Unable to create fork");
        } else if(0 === $this->pid) { // we are in fork

            // not sure if this is needed
            $this->communication = clone $this->communication;

            $this->pid = posix_getpid();

            // manage SIGTERM signal
            pcntl_signal(SIGTERM, [$this, "handleSigTerm"]);

            // run the code...
            $this->_run();
            $this->unload();

            // hook that allow to keep resources
            // and not handle exit event internally
            $this->_exit();
        }

        return $this;
    }

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
     * @return void
     */
    protected function handleSigTerm()
    {
        $this->unload();

        // hook that allow to keep resources
        // and not handle exit event internally
        $this->_exit();
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
     * @throws \RuntimeException
     */
    public function detach()
    {
        if(-1 === posix_setsid()) {
            throw new \RuntimeException("Unable to detach thread");
        }

        $this->pid = posix_getpid();
        $this->parentPid = null;

        $this->detached = true;
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
        return $this->state === self::WAITING;
    }

    /**
     * @return bool
     */
    public function isRunning()
    {
        return $this->state === self::RUNNING || $this->state === self::JOINED;
    }

    /**
     * @return bool
     */
    public function isJoined()
    {
        return $this->state === self::JOINED;
    }

    /**
     * @return bool
     */
    public function isStopped()
    {
        return $this->state === self::STOPPED;
    }

    /**
     * @return $this
     */
    public function join()
    {
        pcntl_waitpid($this->pid, $status, WUNTRACED);

        $this->state = self::JOINED;

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

        $this->state = self::RUNNING;

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

        $this->state = self::STOPPED;

        return $this;
    }

    /**
     * Free resources and other things
     *
     * @return void
     */
    protected function tearDown()
    {
        // destruct communication
        unset($this->communication);
        // destruct all mutexes
        $this->unloadMutexSet();
    }

    /**
     * @return void
     */
    protected function _exit()
    {
        // teardown greacefully
        $this->tearDown();
        posix_kill(posix_getpid(), SIGKILL);
    }
} 