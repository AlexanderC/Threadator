<?php
/**
 * @author AlexanderC <self@alexanderc.me>
 * @date 4/9/14
 * @time 9:46 AM
 */

define('T_MAX', 5);

require __DIR__ . '/../vendor/autoload.php';

$runtime = new \Threadator\Runtime("redis");
$factory = new \Threadator\Factory($runtime);