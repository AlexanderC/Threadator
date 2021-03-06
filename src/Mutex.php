<?php
/**
 * @author AlexanderC <self@alexanderc.me>
 * @date 4/7/14
 * @time 9:09 PM
 */

namespace Threadator;


/**
 * Semaphore based mutex implementation
 *
 * @package Threadator
 */
class Mutex implements IMutexType
{
    const USLEEP_WAIT = 300;

    /**
     * @var string
     */
    protected $identifier;

    /**
     * @var int
     */
    protected $type;

    /**
     * @var mixed
     */
    protected $identity;

    /**
     * @var int
     */
    protected $systemVIPCKey;

    /**
     * @var resource
     */
    protected $semaphore;

    /**
     * @var bool
     */
    protected $acquired = false;

    /**
     * @param mixed $identity
     * @param int $type
     * @throws \RuntimeException
     */
    public function __construct($identity, $type = self::IDX)
    {
        $this->identity = $identity;
        $this->type = $type;

        $this->generateIdentifier();
        $this->generateSystemVIPCKey();

        if(false === ($this->semaphore = @sem_get($this->systemVIPCKey/*, 1, 0666, 1*/))) {
            throw new \RuntimeException("Unable to get semaphore");
        }
    }

    /**
     * @return bool
     */
    public function waitAcquire()
    {
        while(!$this->acquire()) {
            usleep(self::USLEEP_WAIT);
        }

        $this->acquired = true;

        return true;
    }

    /**
     * @return bool
     */
    public function acquire()
    {
        $result = sem_acquire($this->semaphore);

        if(true === $result) {
            $this->acquired = true;
        }

        return $result;
    }

    /**
     * @return bool
     */
    public function release()
    {
        if(true === $this->acquired) {
            $result = sem_release($this->semaphore);

            if(true === $result) {
                $this->acquired = false;
            }

            return $result;
        }

        return true;
    }

    /**
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * @return mixed
     */
    public function getIdentity()
    {
        return $this->identity;
    }

    /**
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return int
     */
    public function getSystemVIPCKey()
    {
        return $this->systemVIPCKey;
    }

    /**
     * @throws \UnexpectedValueException
     */
    protected function generateIdentifier()
    {
        switch($this->type) {
            case self::T_IDX: // case simple string identifier of an object with __toString implementation
                $this->identifier = sprintf("_thdt_mutex_tidx:%s", (string) $this->identity);
                break;
            case self::T_FILE: // case simple file path like /tmp/somefilehere.tmp
                $this->identifier = sprintf("_thdt_mutex_tfile:%s", (string) $this->identity);
                break;
            case self::T_FUNCTION:
                if($this->identity instanceof \Closure) { // case of lambda function
                    $this->identifier = sprintf("_thdt_mutex_tfunction:%s", spl_object_hash($this->identity));
                } else { // case simple function name like "foo"
                    $this->identifier = sprintf("_thdt_mutex_tfunction:%s", (string) $this->identity);
                }

                break;
            case self::T_METHOD:
                if(is_array($this->identity)) { // case callable like declaration [class|object, method]
                    $class = is_object($this->identity[0]) ? get_class($this->identity[0]) : $this->identity[0];
                    $this->identifier = sprintf("_thdt_mutex_tmethod:%s::%s", $class, $this->identity[1]);
                } else { // case static call method declaration like Foo::bar
                    $this->identifier = sprintf("_thdt_mutex_tmethod:%s", (string) $this->identity);
                }
                break;
            case self::T_CLASS:
                if(is_object($this->identity)) { // case object provided
                    $this->identifier = sprintf("_thdt_mutex_tclass:%s", get_class($this->identity));
                } else { // case simple class name like Foo
                    $this->identifier = sprintf("_thdt_mutex_tclass:%s", (string) $this->identity);
                }
                break;
            case self::T_OBJECT: // case an object required mutex during current session
                $this->identifier = sprintf("_thdt_mutex_tobject:%s", spl_object_hash($this->identity));
                break;

            default: throw new \UnexpectedValueException("Unknown Mutex type");
        }
    }

    /**
     * @return void
     */
    protected function generateSystemVIPCKey()
    {
        $identifierFile = sprintf("%s/%s", sys_get_temp_dir(), base64_encode($this->identifier));
        @touch($identifierFile);
        $this->systemVIPCKey = ftok($identifierFile, "m");
    }

    /**
     * @return void
     */
    public function __destruct()
    {
        $this->release();
    }
} 