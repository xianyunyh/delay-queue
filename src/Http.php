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
        $workerNum = $server->setting['worker_number'];
        //设置定时器
        $timeTicker = self::$config['time_ticker'];
        if($workerId % $workerNum === 0) {
            $server->tick($timeTicker,function(){
                echo "hello wolld".PHP_EOL;
            });
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
        $json = json_decode($request->rawContent(),true);
        if(json_last_error() !== JSON_ERROR_NONE) {
            $response->status(500);
            $response->write('parse data error');
        }
        $data = $this->router($request);
        var_dump($data);
        $response->end(json_encode($data));
    }

    protected function router(Request $request)
    {
        $route = $request->server['request_uri'];
        if($route === '/') {
            $redis = new Redis();
            $data = $redis->getOneJob('test');
        }
        return $data;
    }

    public function ticker()
    {

    }



}