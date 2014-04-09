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
     * @param string $communicationDriver
     */
    public function __construct($communicationDriver)
    {
        $this->pid = posix_getpid();

        $this->communication = Communication::create($communicationDriver, $this);
    }

    /**
     * @return $this
     */
    public function run()
    {
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