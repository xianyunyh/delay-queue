<?php

namespace DelayQueue;

use DelayQueue\Redis as Store;

class ReadyQueue
{
    /**
     * @var Redis;
     */
    protected  $redis;
    public function __construct()
    {
        $this->redis = Container::getInstance()['redis'];
    }

    public function pop($queue)
    {
        if(empty($queue)) {
            return false;
        }
        return $this->redis->lPop($queue);
    }


    public function push($queue,$data)
    {
        if(empty($queue) || empty($data)) {
            return false;
        }
        return $this->redis->push($queue,$data);
    }

}