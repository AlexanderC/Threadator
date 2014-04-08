<?php
/**
 * @author AlexanderC <self@alexanderc.me>
 * @date 4/8/14
 * @time 12:37 AM
 */

namespace Threadator\Communication;


use Threadator\Thread;

trait TRuntimeCommunication
{
    /**
     * @param Thread $thread
     * @param mixed $message
     * @return bool
     */
    public function sendMessage(Thread $thread, $message)
    {
        return $this->communication->send(
            $thread->getPid() - $this->getPid() + ICommunicationConstants::COMM_RECEIVER_PAD,
            $message
        );
    }

    /**
     * @param mixed $message
     * @return array
     */
    public function broadcastMessage($message)
    {
        $results = array();

        foreach($this->getIterator() as $thread) {
            $result = $this->communication->send(
                $thread->getPid() - $this->getPid() + ICommunicationConstants::COMM_RECEIVER_PAD,
                $message
            );

            $results[] = [$result, $thread];

            // an issue here (((
            //yield $result => $thread;
        }

        return $results;
    }

    /**
     * Try to get message, but do not block
     *
     * @return \Generator
     */
    public function touchMessage()
    {
        foreach($this->getIterator() as $thread) {
            $result = $this->communication->touch($thread->getPid(), $message);

            yield $result => $message;
        }
    }

    /**
     * Block until the first message arrives
     *
     * @return \Generator
     */
    public function receiveMessage()
    {
        foreach($this->getIterator() as $thread) {
            $result = $this->communication->receive($thread->getPid(), $message);

            yield $result => $message;
        }
    }
} 