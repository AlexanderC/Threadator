<?php
/**
 * @author AlexanderC <self@alexanderc.me>
 * @date 4/9/14
 * @time 3:22 PM
 */

namespace Threadator;


trait TThreadMutex
{
    protected $mutexSet = [];

    /**
     * @param mixed $identity
     * @param int $type
     * @return Mutex
     */
    public function createMutex($identity, $type = Mutex::T_IDX)
    {
        $mutex = new Mutex($identity, $type);
        $this->mutexSet[] = $mutex;
        return $mutex;
    }

    /**
     * @return void
     */
    public function unloadMutexSet()
    {
        foreach($this->mutexSet as $mutex) {
            $mutex->__destruct();
        }
    }
} 