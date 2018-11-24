<?php

namespace DelayQueue;

/**
 *
 * 每个Job必须包含一下几个属性：
 * Topic：Job类型。可以理解成具体的业务名称。
 * Id：Job的唯一标识。用来检索和删除指定的Job信息。
 * Delay：Job需要延迟的时间。单位：秒。（服务端会将其转换为绝对时间）
 * TTR（time-to-run)：Job执行超时时间。单位：秒。
 * Body：Job的内容，供消费者做具体的业务处理，以json格式存储。
 *
 * 每个Job只会处于某一个状态下：
 * ready：可执行状态，等待消费。
 * delay：不可执行状态，等待时钟周期。
 * reserved：已被消费者读取，但还未得到消费者的响应（delete、finish）。
 * deleted：已被消费完成或者已被删除。
 *
 * Class Job
 * @package DelayQueue
 */
class Job
{

    protected $id;

    protected $delay;

    protected $body;

    protected $ttl;

    protected $topic;

    public function __construct(Redis $store)
    {
        $this->store = $store;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getDelay()
    {
        return $this->delay;
    }

    /**
     * @param mixed $delay
     */
    public function setDelay($delay)
    {
        $this->delay = $delay;
    }

    /**
     * @return mixed
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param mixed $body
     */
    public function setBody($body)
    {
        $this->body = $body;
    }

    /**
     * @return mixed
     */
    public function getTtl()
    {
        return $this->ttl;
    }

    /**
     * @param mixed $ttl
     */
    public function setTtl($ttl)
    {
        $this->ttl = $ttl;
    }

    /**
     * @return mixed
     */
    public function getTopic()
    {
        return $this->topic;
    }

    /**
     * @param mixed $topic
     */
    public function setTopic($topic)
    {
        $this->topic = $topic;
    }

    public function addJob($id,$data)
    {
        return $this->store->addJob($id,$data);
    }

    public function deleteJob($id)
    {
        return $this->store->removeJob($id);
    }

    public function getJob($id)
    {
        return $this->store->getOneJob($id);
    }


}