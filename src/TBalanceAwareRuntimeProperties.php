<?php
/**
 * @author AlexanderC <self@alexanderc.me>
 * @date 4/10/14
 * @time 6:08 PM
 */

namespace Threadator;


trait TBalanceAwareRuntimeProperties
{
    /**
     * @var Runtime
     */
    protected $runtime;

    /**
     * @var int
     */
    protected $stackSize = BalanceAwareRuntime::DEFAULT_STACK_SIZE;

    /**
     * @var \SplQueue
     */
    protected $batchQueue;

    /**
     * @var callable
     */
    protected $afterRunCallback;

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
} 