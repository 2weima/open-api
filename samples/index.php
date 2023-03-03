<?php
// +----------------------------------------------------------------------
// | 二维工坊 
// +----------------------------------------------------------------------
// | Copyright (c) 2022 https://www.2weima.com/ All rights reserved.
// +----------------------------------------------------------------------
// | Licensed MIT
// +----------------------------------------------------------------------
// | Author: yifeng <385858750@qq.com>
// +----------------------------------------------------------------------

namespace  samples;

/*
	安装方式
	1. 通用方式 include_once  若不用 命名空间，请注释掉  client.php  的 namespace api_2weima_com;
	2. 通过 composer 安装
*/
include_once '../src/client.php';

use api_2weima_com\client as api_2weima_client;

/**
 * token 在这获取 https://www.2weima.com/user/api_tokens/index.html   
 * 需要授权 ： qr:encode  qr:decode
 */

$config = array(
	'token'=> '3816|RMSuQC....uwAGenrYf', //换成你自己的token 
	'is_mock'=>false,
	'auto_compress'=>true, //本地图片压缩后再解码，速度更快
	'compress_max_width'=>800, //图片最大宽度 ，如果你的图片很大尺寸，建议调整到合适大小进行压缩，取得更快的速度
	'compress_max_height'=>1200,//图片最大高度
	'compress_quality'=>85,//图片质量
);

$client = new api_2weima_client($config);

//生成
$result = $client->set_content('https://www.2weima.com/?a=b&c=g&text=中文说明%2')
	->set_shape(1)
	->set_mark_shape(2)
	->set_output('png')
	->set_footer('')
	// ->set_template_id(3230)
	->set_size(500)
	->set_margin(20)
	->set_level('H')
	->set_version(10)
	->qrencode();

var_dump($result);
// array(3) {
// 	["status"]=> int(200)
// 	["message"]=> string(7) "success"
// 	["qr_image"]=> string(78) "https://img.2weima.com/qr_text/2022/11/18/732177f7c8fba0054500c3e7dcb4dcc6.png"
//   }

exit;



//解码
$result = $client
	->set_image('https://www.2weima.com/static/images/weixin-kefu.jpg')
	// ->set_image_local('./kefu.jpg')
	// ->set_multi('one')
	// ->set_detype('jie2weima')
	->qrdecode();
var_dump($result);

// array(3) {
// 	["status"]=>int(200)
// 	["message"]=>string(7) "success"
// 	["qr_content"]=>string(44) "https://u.wechat.com/MCwqa9eki8owhpRib5J4HDE"
//   }
  

exit;