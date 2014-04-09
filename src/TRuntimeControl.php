<?php
/**
 * @author AlexanderC <self@alexanderc.me>
 * @date 4/9/14
 * @time 3:55 PM
 */

namespace Threadator;


trait TRuntimeControl
{
    /**
     * @param Thread $thread
     * @return $this
     */
    public function push(Thread $thread)
    {
        $this->pool[] = $thread;

        return $this;
    }

    /**
     * @param Thread $thread
     * @param bool $safe
     * @return $this
     * @throws \OutOfBoundsException
     */
    public function remove(Thread $thread, $safe = true)
    {
        if(false === ($key = array_search($thread, $this->pool, true))) {
            throw new \OutOfBoundsException("No such thread found");
        }

        if(true === $safe) {
            /** @var Thread $thread */
            $thread = $this->pool[$key];

            if(!$thread->isJoined()) {
                $thread->join();
            }
        }

        unset($this->pool[$key]);

        return $this;
    }

    /**
     * @param callable $callback
     * @return $this
     */
    public function map(callable $callback)
    {
        foreach($this->pool as $thread) {
            call_user_func($callback, $thread);
        }

        return $this;
    }

    /**
     * @param bool $safe
     * @return null|Thread
     */
    public function pop($safe = true)
    {
        /** @var Thread $thread */
        $thread = array_pop($this->pool);

        if(null !== $thread) {
            if(!$thread->isJoined()) {
                $thread->join();
            }

            return $thread;
        }

        return null;
    }

    /**
     * @param bool $safe
     * @return null|Thread
     */
    public function shift($safe = true)
    {
        /** @var Thread $thread */
        $thread = array_shift($this->pool);

        if(null !== $thread) {
            if(!$thread->isJoined()) {
                $thread->join();
            }

            return $thread;
        }

        return null;
    }
} 