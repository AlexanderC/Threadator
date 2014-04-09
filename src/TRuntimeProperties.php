<?php
/**
 * @author AlexanderC <self@alexanderc.me>
 * @date 4/9/14
 * @time 3:52 PM
 */

namespace Threadator;


trait TRuntimeProperties
{
    /**
     * @var array
     */
    protected $pool = [];

    /**
     * @var int
     */
    protected $pid;

    /**
     * @var Communication\Communication
     */
    protected $communication;

    /**
     * @return \Generator
     */
    public function getIterator()
    {
        foreach($this->pool as $thread) {
            yield $thread;
        }
    }

    /**
     * @param int $pid
     * @return null|Thread
     */
    public function findThreadByPid($pid)
    {
        foreach($this->pool as $thread) {
            if($thread->getPid() === $pid) {
                return $thread;
            }
        }

        return null;
    }

    /**
     * @return array
     */
    public function getThreadsPid()
    {
        $threadsPid = [];

        foreach($this->pool as $thread) {
            $threadsPid[] = $thread->getPid();
        }

        return $threadsPid;
    }

    /**
     * @return \Threadator\Communication\Communication\Communication
     */
    public function getCommunication()
    {
        return $this->communication;
    }

    /**
     * @return int
     */
    public function getPid()
    {
        return $this->pid;
    }
} 