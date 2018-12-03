<?php
namespace DelayQueue;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\Http\Server as HttpServer;
use DelayQueue\Redis;
use Swoole\Server;

class Http
{

    protected $isEnd = false;

    /**
     * @var array
     */
    public static $config = [];

    public function __construct(array $config = [])
    {
        self::$config = array_merge(self::$config,$config);
    }

    /**
     * 创建HTTP Server
     */
    public function init()
    {
        $host  = self::$config['host'] ?? '0.0.0.0';
        $port = self::$config['port'] ?? '9501';
        $server = new HttpServer($host,$port);
        $server->set(self::$config['http']);
        $server->on('WorkerStart',[$this,'onWorkStart']);
        $server->on('request',[$this,'onRequest']);
        $server->start();
    }

    /**
     * Worker 启动回调
     *
     * @param Server $server
     * @param int $workerId
     */
    public function onWorkStart(Server $server,int $workerId)
    {
        $workerNum = $server->setting['worker_number'] ?? 4;
        //设置定时器
        $timeTicker = self::$config['time_ticker'];
        if($workerId % $workerNum === 0) {
            $server->tick($timeTicker,[$this,'ticker']);
        }
    }


    /**
     * Request 回调
     * @param Request $request
     * @param Response $response
     */
    public function onRequest(Request $request,Response $response)
    {

        if($request->server['request_method'] !== 'POST') {
            $response->status('403');
            $response->end('Not allowed');
            $this->isEnd = true;
        }
        if($this->isEnd) {
            return ;
        }

        $data = $this->router($request,$response);
        $response->end(json_encode($data));
    }

    protected function router(Request $request,Response $response)
    {
        $route = $request->server['request_uri'];
        $json = json_decode($request->rawContent(),true);
        if(json_last_error() !== JSON_ERROR_NONE) {
            $response->status(500);
            $response->write('parse data error');
        }
        if($route === '/push') {
            $redis = new Redis();
            $data = json_decode($request->rawcontent(),true);
            $data['run_time'] = ($data['ttl'] ?? 100) + time();
            $data['status'] = 'delay';
            $jobId = $data['id'] ?? md5($data);
            $redis->addJob($jobId,$data);
        }
        return $data;
    }

    /**
     * 每个Job只会处于某一个状态下：
     * ready：可执行状态，等待消费。
     * delay：不可执行状态，等待时钟周期。
     * reserved：已被消费者读取，但还未得到消费者的响应（delete、finish）。
     * deleted：已被消费完成或者已被删除。
     */
    public function ticker()
    {
        $bucket = self::$config['bucket'];
        $redis = new Redis();
        $ids = $redis->getBucketJobs($bucket);
        $jobService = new Job($redis);
        $readyQueue = self::$config['ready_queue'];
        if(empty($ids)) {
            return ;
        }
        foreach ($ids as $id) {
            $job = $redis->getOneJob($id);
            var_dump($job,$id);
            if($job['run_time'] >= time() && $job['status'] == 'delay') {
                $jobService->updateJob($job['id'],'status','ready');
                $redis->push($readyQueue,$job['id']);
            }
        }

    }



}