<?php
namespace DelayQueue;


class Bucket
{
    public function __construct(Redis $redis)
    {
        $this->redis = $redis;
    }

    public function append($bucket,$data)
    {
        $score = time();
        return $this->redis->zadd($bucket, $score,$data);
    }
}