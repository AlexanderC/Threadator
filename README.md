# Main Goal
The mail goal of [Threadator](https://github.com/AlexanderC/Threadator) package is to provide an easy way to run multithreaded applications in PHP.
You may notice that there are other packages like this, but:

- This is a modern package (Using Generators, Traits and other language sugar + build as a composer library)
- It is providing most native implementation ever (all that you need are posix, pcntl)
- You have full controll on your threads (Mutex and bidirectional Communication betweed threads and master)

# Installation
- Via [Composer](https://getcomposer.org/)

		"alexanderc/threadator": "dev-master"

# Basic usage
```php
<?php
require '/path/to/vendor/autoload.php';

$runtime = new \Threadator\Runtime();
$factory = new \Threadator\Factory($runtime);

// instead of built in drivers names ["msgQueue", "redis"] you can use your own driver
// by providing fully qualified class name (with NS), like My\CommunicationDriver\TestDriver
// IMPORTANT: in order to get a stable work of communication- use "redis" instead of "msgQueue"
$communication = \Threadator\Communication\Communication::create($runtime, 'msgQueue' /* 'redis', ['127.0.0.1'] */ );
$runtime->setCommunication($communication);

// now we can create some threads
for($i = 0; $i < 5; $i++) {
    /** @var \Threadator\Thread $thread */
       $thread = $factory->createCallable(function($thread) {
              // create mutex
			  // for more mutex types check \Threadator\Mutex::T_* constants
              $mutex = $thread->createMutex("echo", \Threadator\Mutex::T_FUNCTION);
			  
			  // wait until we aquire the mutex
			  $mutex->waitAcquire();
			  
			  // do some work here...
			  sleep(mt_rand(1, 3));
			  echo "Running Thread #{$thread->getPid()}...\n";
			  
			  // wait until we get a message from the main process
              $thread->receiveMessage($message);
			  
			  // send back this message
              $thread->sendMessage("#{$thread->getPid()}: {$message}");
           });
}

echo "Main process #{$runtime->getPid()} running!\n";

// start all threads
$runtime->run();

// send a message to all threads
foreach($runtime->broadcastMessage(microtime(true)) as list($result, $thread)) {
	// if result == 1 than message was sent
    echo "Result for msg #{$thread->getPid()} -> {$result}\n";
}

// wait until we receive thread messages
$messages = [];
foreach($runtime->receiveMessage() as $result => $message) {
    if($result) {
        $messages[] = $message;
    }
}
echo "Thread messages: " . implode(", ", $messages) . "\n";

// wait until all the threads runs
$runtime->join();

exit("Main process #{$runtime->getPid()} stopped!\n");
```

	For more examples check "test" folder

# Is it extendable
	
	Yes it is!
	
You can easily write your communication driver or an thread implementation.

# How to extend
If you need to add (for example) a new communication driver- you just need to extend Threadator\Communication\Driver like this

```php
<?php
/**
 * @author AlexanderC <self@alexanderc.me>
 * @date 4/10/14
 * @time 2:04 PM
 */

use  Threadator\Communication\Driver\ADriver;


class TestDriver extends ADriver
{
    /**
     * @return void
     */
    protected function init()
    {
        // TODO: Implement init() method.
    }

    /**
     * @param int $key
     * @param mixed $message
     * @return bool
     */
    public function send($key, $message)
    {
        // TODO: Implement send() method.
    }

    /**
     * Try to get message, but do not block
     *
     * @param int $key
     * @param mixed $message
     * @return bool
     */
    public function touch($key, & $message)
    {
        // TODO: Implement touch() method.
    }

    /**
     * Block until the first message arrives
     *
     * @param int $key
     * @param mixed $message
     * @return bool
     */
    public function receive($key, & $message)
    {
        // TODO: Implement receive() method.
    }

} 	
```

# ToDo
- Create unit tests
- Implement more communication drivers
- ...your suggestions

# Contributors
- [AlexanderC](mailto:self@alexanderc.me)
- ...[all contributors](https://github.com/AlexanderC/Threadator/graphs/contributors)