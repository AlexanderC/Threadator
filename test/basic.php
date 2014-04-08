<?php
/**
 * @author AlexanderC <self@alexanderc.me>
 * @date 4/7/14
 * @time 10:30 PM
 */

define('T_MAX', 5);

require __DIR__ . '/../vendor/autoload.php';

$runtime = new \Threadator\Runtime("redis");
$factory = new \Threadator\Factory($runtime);

for($i = 0; $i < T_MAX; $i++) {
    /** @var \Threadator\Thread $thread */
    $thread = $factory->createCallable(function($thread) {
            if(!mt_rand(0, 1)) {
                // create mutex
                $mutex = new \Threadator\Mutex("echo", \Threadator\Mutex::T_FUNCTION);

                // acquire mutex
                if($mutex->waitAcquire()) {
                    echo "Mutex acquired for #{$thread->getPid()}\n";
                }

                sleep(mt_rand(1, 3));
                echo "[Mutex] Running #{$thread->getPid()}...\n";

                // release mutex
                if($mutex->release()) {
                    echo "Mutex released for #{$thread->getPid()}\n";
                }
            } else {
                echo "[No Mutex] Running #{$thread->getPid()}...\n";
            }

            //echo "Running thread #{$thread->getPid()}...\n";

            $thread->receiveMessage($message);
            $thread->sendMessage("#{$thread->getPid()}: {$message}");
        });
}

echo "Main process #{$runtime->getPid()} running\n";

// start all threads
$runtime->run();

// send a message to all threads
$runtime->broadcastMessage(microtime(true));

// receive thread messages
$messages = [];
foreach($runtime->receiveMessage() as $result => $message) {
    if($result) {
        $messages[] = $message;
    }
}
echo "Messages: " . implode(", ", $messages) . "\n";

// end work
$runtime->join();
exit("Main process #{$runtime->getPid()} stopped\n");