<?php
namespace DelayQueue;
/**
 * Zset 桶
 * Class Bucket
 * @package DelayQueue
 */
class Bucket
{
    public function __construct()
    {
        $this->redis = Container::getInstance()['redis'];
    }

    public function append($bucket,$data)
    {
        $score = time();
        return $this->redis->zadd($bucket, $score,$data);
    }
}