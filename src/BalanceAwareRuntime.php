<?php
/**
 * @author AlexanderC <self@alexanderc.me>
 * @date 4/10/14
 * @time 5:33 PM
 */

namespace Threadator;


class BalanceAwareRuntime
{
    const DEFAULT_STACK_SIZE = 15;

    /**
     * @var Runtime
     */
    protected $runtime;

    /**
     * @var int
     */
    protected $stackSize = self::DEFAULT_STACK_SIZE;

    /**
     * @var \SplQueue
     */
    protected $batchQueue;

    /**
     * @var callable
     */
    protected $afterRunCallback;

    /**
     * @param Runtime $runtime
     */
    public function __construct(Runtime $runtime)
    {
        $this->runtime = $runtime;
        $this->batchQueue = new \SplQueue();
    }

    /**
     * @param callable $setup
     * @return $this
     */
    public function pushToBatchQueue(callable $setup)
    {
        $this->batchQueue->push($setup);

        return $this;
    }

    /**
     * @return \Threadator\Runtime
     */
    public function getRuntime()
    {
        return $this->runtime;
    }

    /**
     * @param int $stackSize
     * @return $this
     */
    public function setStackSize($stackSize)
    {
        $this->stackSize = (int) $stackSize;
        $this->stackSize = $this->stackSize > 0 ? $this->stackSize : self::DEFAULT_STACK_SIZE;

        return $this;
    }

    /**
     * @return int
     */
    public function getStackSize()
    {
        return $this->stackSize;
    }

    /**
     * @param callable $afterRunCallback
     * @return $this
     */
    public function setAfterRunCallback(callable $afterRunCallback)
    {
        $this->afterRunCallback = $afterRunCallback;

        return $this;
    }

    /**
     * @return callable
     */
    public function getAfterRunCallback()
    {
        return $this->afterRunCallback;
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