<?php

namespace DelayQueue;


class Redis
{

    protected $jobPrefix = 'topic_job';

    protected $set = 'bucket';
    public function __construct(array $config = [])
    {
        $redis = new \Swoole\Coroutine\Redis();
        $redis->connect('127.0.0.1', 6379);
        $this->redis = $redis;
    }

    /**
     * 返回所有的jobs
     */
    public function getAll()
    {
        $keys = $this->redis->keys($this->jobPrefix);
        if(empty($keys)) {
            return [];
        }
        $data = [];

        foreach ($keys as $key) {
            $data[] = $this->redis->hGetAll($key);
        }
        return $data;
    }


    /**
     * 添加一个新的job
     *
     * @param $jobId
     * @param $data
     */
    public function addJob($jobId,$data)
    {
        if(empty($jobId) || empty($data)) {
            return false;
        }
        try {
            $this->redis->hMSet($jobId,$data);
            $this->redis->zAdd($this->set,$jobId,time());
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * 删除单个任务
     * @param $jobId
     */
    public function removeJob($jobId)
    {
        if(empty($jobId)) {
            return ;
        }

        try {
        	$this->redis->delete($jobId);
        	$this->redis->zRem($this->set,$jobId);
        } catch (\Exception $e) {

        }

    }

    /**
     * 获取单个job的信息
     *
     * @param $jobId
     */
    public function getOneJob($jobId)
    {
        if(empty($jobId)) {
            return [];
        }
        return $this->redis->hGetAll($jobId);
    }

    /**
     * 获取topic中的jobs
     *
     * @param $topicId
     */
    public function getTopicJobs($topicId)
    {
        $data = [];
        while ($id = $this->redis->lPop($topicId))
        {
            $data[] = $this->redis->hGetAll($id);
        }
        return $data;
    }

    /**
     * 返回所有的主题列表
     *
     */
    protected function getTopics()
    {

    }

}