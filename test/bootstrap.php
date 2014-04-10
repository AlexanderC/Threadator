<?php
/**
 * @author AlexanderC <self@alexanderc.me>
 * @date 4/9/14
 * @time 9:46 AM
 */

define('T_MAX', 5);

require __DIR__ . '/../vendor/autoload.php';

$runtime = new \Threadator\Runtime();
$factory = new \Threadator\Factory($runtime);

// the last argument are the parameters for redis connect() method
$communication = \Threadator\Communication\Communication::create($runtime, 'redis', ['127.0.0.1']);
$runtime->setCommunication($communication);