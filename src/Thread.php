<?php
/**
 * @author AlexanderC <self@alexanderc.me>
 * @date 4/7/14
 * @time 9:09 PM
 */

namespace Threadator;


use Threadator\Communication\TThreadCommunication;

abstract class Thread implements IThreadState
{
    use TThreadProperties;
    use TThreadControl;
    use TThreadCommunication;
    use TThreadMutex;

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
     * This method is called before running tht thread
     *
     * @return void
     */
    abstract protected function init();

    /**
     * This method would be called inside the thread
     *
     * @return void
     */
    abstract protected function _run();

    /**
     * This method would be called after _run() execution or
     * a SIGTERM signal sent
     *
     * @return void
     */
    abstract protected function unload();

    /**
     * @return $this
     * @throws \RuntimeException
     */
    final public function run()
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
     * @return void
     */
    final protected function handleSigTerm()
    {
        $this->unload();

        // hook that allow to keep resources
        // and not handle exit event internally
        $this->_exit();
    }

    /**
     * Free resources and other things
     *
     * @return void
     */
    final protected function tearDown()
    {
        // destruct communication [NOT NECESSARY]
        //unset($this->communication);
        // destruct all mutexes
        $this->unloadMutexSet();
    }

    /**
     * @return void
     */
    final protected function _exit()
    {
        // teardown greacefully
        $this->tearDown();
        posix_kill(posix_getpid(), SIGKILL);
    }
} 