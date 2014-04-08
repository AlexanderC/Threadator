<?php
/**
 * @author AlexanderC <self@alexanderc.me>
 * @date 4/7/14
 * @time 11:00 PM
 */

namespace Threadator;


interface IMutexType
{
    const T_FILE = 2;
    const T_IDX = 8;
    const T_METHOD = 16;
    const T_FUNCTION = 32;
    const T_OBJECT = 64;
    const T_CLASS = 128;
} 