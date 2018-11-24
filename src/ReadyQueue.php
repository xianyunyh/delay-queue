<?php

namespace DelayQueue;

use DelayQueue\Redis as Store;

class ReadyQueue
{
    public function __construct(Store $store)
    {
        $this->store = $store;
    }




}