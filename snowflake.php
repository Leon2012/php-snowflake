<?php
/**
 * 
 * @authors Leon Peng (leon.peng@live.com)
 * @date    2016-08-24 16:58:02
 * @version $Id$
 */

define('EPOCH', 1414213562373);
define('NUMWORKERBITS', 10);
define('NUMSEQUENCEBITS', 12);
define('MAXWORKERID', (-1 ^ (-1 << NUMWORKERBITS)));
define('MAXSEQUENCE', (-1 ^ (-1 << NUMSEQUENCEBITS)));

class Snowflake  
{

    private $_lastTimestamp;
    private $_sequence = 0;
    private $_workerId = 1;
    
    public function __construct($workerId)
    {
        if (($workerId < 0) || ($workerId > MAXWORKERID)) {
            return null;
        }
        $this->workerId = $workerId;
    }

    public function next()
    {
        $ts = $this->timestamp();
        if ($ts == $this->_lastTimestamp) {
            $this->_sequence = ($this->_sequence+1)&MAXSEQUENCE;
            if ($this->_sequence == 0) {
                $ts = $this->waitNextMilli($ts);
            }
        }else{
            $this->_sequence = 0;
        }

        if ($ts < $this->_lastTimestamp) {
            return 0;
        }

        $this->_lastTimestamp = $ts;
        return $this->pack();
    }

    private function pack()
    {
        return ($this->_lastTimestamp<<(NUMWORKERBITS+NUMSEQUENCEBITS))|($this->_workerId<<NUMSEQUENCEBITS)|$this->_sequence;
    }

    private function waitNextMilli($ts)
    {
        if ($ts = $this->_lastTimestamp) {
            sleep(0.1);
            $ts = $this->timestamp();
        }
        return $ts;
    }

    private function timestamp()
    {   
        return ($this->millitime() - EPOCH);
    }

    private function millitime() 
    {
        $microtime = microtime();
        $comps = explode(' ', $microtime);
        // Note: Using a string here to prevent loss of precision
        // in case of "overflow" (PHP converts it to a double)
        return sprintf('%d%03d', $comps[1], $comps[0] * 1000);
    }
}