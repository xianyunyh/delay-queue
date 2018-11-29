<?php
return [
    'task_prefix'=>"topic_task",
    'http'=>[
        'document_root' => '/www/task',
        'enable_static_handler' => true,
        'worker_num' => 1,    //worker process num
        'backlog' => 128,   //listen backlog
        'max_request' => 50,
        'dispatch_mode'=>1,
    ],
    'host'=>'0.0.0.0',//http host
    'port'=>'9501',//http端口号
    'bucket'=>'bucket',//zset名字
    'ready_queue'=>'ready_queue',//队列的名字
    'time_ticker'=>10*1000,//定时器的周期
];