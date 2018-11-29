<?php
namespace DelayQueue;


class Bucket
{
    public function __construct(Redis $redis)
    {
        $this->redis = $redis;
    }

}