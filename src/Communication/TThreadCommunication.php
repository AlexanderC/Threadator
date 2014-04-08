<?php
/**
 * @author AlexanderC <self@alexanderc.me>
 * @date 4/8/14
 * @time 12:37 AM
 */

namespace Threadator\Communication;


trait TThreadCommunication
{
    /**
     * @param mixed $message
     * @return bool
     */
    public function sendMessage($message)
    {
        return $this->communication->send($this->getPid(), $message);
    }

    /**
     * Try to get message, but do not block
     *
     * @param mixed $message
     * @return bool
     */
    public function touchMessage(& $message)
    {
        return $this->communication->touch(
            $this->getPid() - $this->getParentPid() + ICommunicationConstants::COMM_RECEIVER_PAD,
            $message
        );
    }

    /**
     * Block until the first message arrives
     *
     * @param mixed $message
     * @return bool
     */
    public function receiveMessage(& $message)
    {
        return $this->communication->receive(
            $this->getPid() - $this->getParentPid() + ICommunicationConstants::COMM_RECEIVER_PAD,
            $message
        );
    }
} 