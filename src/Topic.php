<?php

namespace DelayQueue;
use DelayQueue\Redis as Store;

class Topic
{

    protected $id;

    public function __construct(Store $redis)
    {
        $this->store  = $redis;
    }

    public function getTopicData($topicId)
    {
        return $this->store->getTopicJobs($topicId);
    }


}