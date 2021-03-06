<?php
/**
 * @author AlexanderC <self@alexanderc.me>
 * @date 4/7/14
 * @time 9:09 PM
 */

namespace Threadator;


use Threadator\Implementation\CallableThread;

class Factory
{
    /**
     * @var Runtime
     */
    protected $runtime;

    /**
     * @param Runtime $runtime
     */
    public function __construct(Runtime $runtime)
    {
        $this->runtime = $runtime;
    }

    /**
     * @return \Threadator\Runtime
     */
    public function getRuntime()
    {
        return $this->runtime;
    }

    /**
     * @param string $threadClass
     * @return Thread
     * @throws \BadMethodCallException
     */
    public function create($threadClass)
    {
        if(!is_subclass_of($threadClass, Thread::class)) {
            throw new \BadMethodCallException("Threaded class should be an instance of Thread");
        }

        return new $threadClass($this->runtime);
    }

    /**
     * @param callable $callable
     * @return $this
     */
    public function createCallable(callable $callable)
    {
        return (new CallableThread($this->runtime))->setCallable($callable);
    }
} 