<?php
/**
 * @author AlexanderC <self@alexanderc.me>
 * @date 4/10/14
 * @time 5:33 PM
 */

namespace Threadator;


class BalanceAwareRuntime
{
    use TBalanceAwareRuntimeProperties;

    const DEFAULT_STACK_SIZE = 15;

    /**
     * @param Runtime $runtime
     */
    public function __construct(Runtime $runtime)
    {
        $this->runtime = $runtime;
        $this->batchQueue = new \SplQueue();
    }

    /**
     * @return $this
     * @throws \UnexpectedValueException
     */
    public function runAndJoin()
    {
        while(!$this->batchQueue->isEmpty()) {
            $prevThreadsCount = 0;
            $threadsCount = 0;

            for($i = 0; $i < $this->stackSize && !$this->batchQueue->isEmpty(); $i++) {
                $prevThreadsCount = $threadsCount;

                $setup = $this->batchQueue->pop();

                // here you have to add a new thread
                call_user_func($setup, $this->runtime);

                // threads count should increase now
                $threadsCount = $this->runtime->getThreadsCount();

                if($prevThreadsCount === $threadsCount) {
                    throw new \UnexpectedValueException('You must add a new thread in the $setup callable');
                }
            }

            // run current batch of threads
            $this->runtime->run();

            if(is_callable($this->afterRunCallback)) {
                call_user_func($this->afterRunCallback, $this->runtime);
            }

            // join all this threads
            $this->runtime->join();
        }
        return $this;
    }
} 