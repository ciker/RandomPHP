<?php
/**
 * Created by PhpStorm.
 * User: dengjiaming
 * Date: 22/3/2016
 * Time: 下午2:28
 */

namespace Random;

use Random\Http\Response;


/**
 * Class Core
 * @package Random
 * @author DJM <op87960@gmail.com>
 * @todo 核心类实现路由分派和初始化
 */
class Core
{

    protected static $instance;
    private $_classLoader;
    private $_config;

    private function __construct($config, $classLoader)
    {
        $this->_classLoader = $classLoader;
        $this->_config = $config;
    }

    /**
     * @param $classLoader
     * @param $config array
     * @return Core
     * @author DJM <op87960@gmail.com>
     * @todo 取得Core的实例
     */
    static function getInstance($config, $classLoader)
    {
        if (empty(self::$instance)) {
            self::$instance = new self($config, $classLoader);
        }
        return self::$instance;
    }

    /**
     * @return $this
     * @author DJM <op87960@gmail.com>
     * @todo 系统初始化
     */
    function init()
    {
        //防止预先输出
        ob_start();
        //开启session
        session_start();

        //载入配置
        Factory::getConfig(__DIR__);

        //debug配置
        $debug = $this->_config['debug'];

        //错误文件显示偏移量
        $lineOffset = $this->_config['line_offset'];

        //注册异常处理
        Exception::Register($debug, $lineOffset);

        $routerConfig = $this->_config['router'];
        if (is_array($routerConfig)) {
            //根据配置载入命名空间
            foreach ($routerConfig as $key => $value) {
                $this->_classLoader->addNamespace($key, BASE_ROOT . $value);
            }
        }

        //加载钩子
        $hookConfig = $this->_config['hook'];
        if (is_array($hookConfig)) {
            foreach ($hookConfig as $key => $value) {
                Hook::add($key, $value);
            }
        }

        //注册自动载入类
        Register::set('autoload', $this->_classLoader);

        //设置时区
        date_default_timezone_set(Config::get('default_timezone'));

        //加载库全局函数
        require 'functions.php';

        //设置debug选项
        if ($debug) {
            ini_set("display_errors", "on");
            error_reporting(E_ALL | E_STRICT);
        } else {
            ini_set("display_errors", "Off");
            ini_set("error_log", BASE_ROOT . '/Temp/Logs/error_log.log');
        }

        return $this;
    }


    /**
     * @author DJM <op87960@gmail.com>
     * @todo 路由分派
     */
    function dispatch()
    {

        list($module, $controller, $method) = Router::parseUrl();

        //检查是否存在模块
        if (!is_dir($this->_config['path']['APP_ROOT'] . '/' . $module)) {
            throw new \Exception('404,module:' . $module . ' not found');
        }

        //拼凑命名空间
        $controllerNameSpace = $module . '\\Controller\\' . $controller;

        //检查是否存在controller
        if (!class_exists($controllerNameSpace)) {
            throw new \Exception('404,controller:' . $controller . ' not found');
        }

        //检查方法是否存在,method_exist只能判断方法存在,即使方法不能调用(非public),也返回true
        if (!is_callable(array($controllerNameSpace, $method))) {
            throw new \Exception('404,method:' . $method . ' not found');
        }

        //使用反射类
        $rClass = new \ReflectionClass($controllerNameSpace);

        $newRClass = $rClass->newInstanceArgs(array($this->_config, $module, $controller, $method));

        $rMethod = $rClass->getMethod($method);



        //实例化controller类
//        $class = new $controllerNameSpace($module, $controller, $method);

        //Controller的模块目录
        $modulePath = dirname(dirname($rClass->getFileName()));
//        $modulePath = dirname(dirname(Register::get('autoload')->loadClass($controllerNameSpace)));

        //载入配置
        Factory::getConfig($modulePath);

        //载入Request
        $request = Factory::getRequest();

        //注册Request对象
        Register::set('request', $request);

        //注册Cache对象
        Register::set('cache', Factory::getCache());

        //钩子
        Hook::listen('APP_START');

        //加载库函数
        if (file_exists($modulePath . 'functions.php')) {
            require $modulePath . 'functions.php';
        }

        //加载模块库函数
        if (file_exists(dirname($modulePath) . 'functions.php')) {
            require dirname($modulePath) . 'functions.php';
        }

        //构造传入参数
        $args[] = $request;

        //debug统计
        if ($this->_config['debug']) {
            Debug::startCount();
        }
        //执行目标方法
        $response = $rMethod->invokeArgs($newRClass, $args);
//        $response = call_user_func_array(array($class, $method), $args);

        //如果实例方法没有返回Response对象,则new一个空对象,防止send方法调用失败
        if (!($response instanceof Response)) {
            $response = new Response('');
            if ($this->_config['debug']) {
                trigger_error('response is no defined!');
            }
        }

        //钩子
        Hook::listen('APP_END');

        //统计输出
        if ($this->_config['debug']) {
            Debug::endCount('总运行时间:');
        }
        
        return $response;
    }
}