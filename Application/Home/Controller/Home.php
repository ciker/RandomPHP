<?php
/**
 * Created by PhpStorm.
 * User: dengjiaming
 * Date: 22/3/2016
 * Time: 下午5:08
 */

namespace Home\Controller;

use Home\Model\Accounts;
use Random\Controller;
use Random\Db\Pdo;
use Random\Factory;
use Random\Http\JsonResponse;
use Random\Http\Response;

class Home extends Controller
{

    /**
     * @param $config \Random\Config
     * @param $request \Random\Request
     * @return Response
     * @author DJM <op87960@gmail.com>
     * @todo 默认目录
     */
    function index($config, $request)
    {
//        $database = Factory::getDatabase();
//        var_dump($config['router']);
//        var_dump($request->get);
//        new Autoload();

//        return new Response('Hello RandomPHP!!');
        //test 模版渲染输出
//        $this->assign('ad', 'RandomPHP');
//        $this->assign('some', 'hi');
//        return new Response($this->display());

        $data['das'] = 123;
        $data['dd'] = 189;
        return new JsonResponse($data);
    }

    function test()
    {
        $map['name'] = 'linkaibin';
        $db_obj = Accounts::Findbyname($map);
        var_dump($db_obj[0]->account);

    }

//    function test()
//    {
////        $database = Factory::getDatabase();
////        var_dump($database->getArray('select * from user'));
//        $db = new Pdo('localhost','root','admin123','mysql');
//        var_dump($db->getArray('select User,Password from user'));
//    }
}