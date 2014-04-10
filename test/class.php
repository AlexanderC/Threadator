<?php
/**
 * @author AlexanderC <self@alexanderc.me>
 * @date 4/7/14
 * @time 10:30 PM
 */

require "./bootstrap.php";
require "./classes/EchoThread.php";

for($i = 0; $i < T_MAX; $i++) {
    usleep(400);
    $thread = $factory->create(EchoThread::class);
    $thread->setStringToEcho(microtime(true));
}

echo "Main process #{$runtime->getPid()} running\n";

// start all threads
$runtime->run();

// receive thread messages
$messages = [];
/*foreach($runtime->receiveMessage() as $result => $message) {
    if($result) {
        $messages[] = $message;
    }
}*/
echo "Messages: " . implode(", ", $messages) . "\n";

// end work
$runtime->join();
exit("Main process #{$runtime->getPid()} stopped\n");