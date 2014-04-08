<?php
/**
 * @author AlexanderC <self@alexanderc.me>
 * @date 4/7/14
 * @time 9:19 PM
 */

namespace Threadator;


interface IThreadState
{
    const WAITING = 2;
    const RUNNING = 4;
    const JOINED = 8;
    const STOPPED = 16;
} 