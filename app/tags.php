<?php
/**
 * JYmusic音乐管理系统
 *
 * @version     2.0
 * @author      战神~~巴蒂 [jyuucn@163.com]
 * @license     http://jyuu.cn [未经授权严禁私自出售，否者承担法律责任]
 * @copyright   2014 - 2017 JYmusic
 */

// 应用行为扩展定义文件
return [
    // 应用初始化
//    'app_init'     => [
//        'app\\common\\behavior\\InitApp'
//    ],
    // 应用开始
    'app_begin' => [
        'app\\common\\behavior\\InitHook'
    ],
    // 模块初始化
    'module_init'  => [],
    // 操作开始执行
    'action_begin' => [],
    
    // 视图内容过滤
    'view_filter'  => [
		'app\\common\\behavior\\FilterTpl'
	],
    // 日志写入
    'log_write'    => [],
    // 应用结束
    'app_end'      => [],
];
