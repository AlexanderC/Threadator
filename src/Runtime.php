<?php
/**
 * @author AlexanderC <self@alexanderc.me>
 * @date 4/7/14
 * @time 9:09 PM
 */

namespace Threadator;

use Threadator\Communication\Communication;
use Threadator\Communication\TRuntimeCommunication;

class Runtime
{
    use TRuntimeProperties;
    use TRuntimeControl;
    use TRuntimeCommunication;

    /**
     * Set pid
     */
    public function __construct()
    {
        $this->pid = posix_getpid();
    }

    /**
     * @return $this
     * @throws \RuntimeException
     */
    public function run()
    {
        if(!($this->communication instanceof Communication)) {
            throw new \RuntimeException("You may set communication first");
        }

        /** @var Thread $thread */
        foreach($this->pool as $thread) {
            if($thread->isWaiting()) {
                $thread->run();
            }
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function join()
    {
        while(null !== $this->shift(true));

        return $this;
    }
} 