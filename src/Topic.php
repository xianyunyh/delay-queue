<?php

namespace DelayQueue;

use DelayQueue\Redis as Store;

class Topic
{

    /**
     * @var $id
     */
    protected $id;

    public function __construct()
    {
        $this->store = Container::getInstance()['redis'];
    }

    public function getTopicData($topicId)
    {
        return $this->store->getTopicJobs($topicId);
    }


}