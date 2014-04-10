<?php
/**
 * @author AlexanderC <self@alexanderc.me>
 * @date 4/10/14
 * @time 5:57 PM
 */

require "./bootstrap.php";
require "./classes/EchoThread.php";

$balancedRuntime = new \Threadator\BalanceAwareRuntime($runtime);
$balancedRuntime->setStackSize(5);

// set callback run after runtime run() and before join() called
$balancedRuntime->setAfterRunCallback(function(\Threadator\Runtime $runtime) {
        // receive thread messages
        $messages = [];
        foreach($runtime->receiveMessage() as $result => $message) {
            if($result) {
                $messages[] = $message;
            }
        }
        echo "Messages for current batch " . implode(", ", $messages) . "\n";
    });

// push all threads we need
for($i = 0; $i < 13; $i++) {
    // we should create a new thread in the $setup callable
    $balancedRuntime->pushToBatchQueue(function() use ($factory) {
            usleep(400);
            $thread = $factory->create(EchoThread::class);
            $thread->setStringToEcho(microtime(true));
        });
}

echo "Main process #{$runtime->getPid()} running\n";

// run all threads and join them
$balancedRuntime->runAndJoin();

exit("Main process #{$runtime->getPid()} stopped\n");