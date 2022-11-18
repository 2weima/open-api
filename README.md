### 概述
二维工坊 - 开放接口

快速实现 【二维码解码、二维码模板美化、二维码生成】 等功能，具有AI加持，能识别微模糊和微变形的二维码，在线体验： https://jie.2weima.com/

目前测试情况十分优秀：
	测试图片，二维工坊和微信能全识别、支付宝可识别部分、其他平台几乎不能识别
	-samples
		test-1.png	超模糊问题
		test-2.png	LOGO过大问题
		test-3.png	模糊变形问题
		... 暂时仅提供小部分，已经足够大家思考和处理，是否应该自己去实现这些情况，有些问题仅使用开源程序是不能满足需求的，本接口依靠二维工坊每天处理海量二维码图片，从识别失败的图片的不同情况中训练而成。

任何语言都可以使用，非PHP语言，可以参考samples示例
主页： https://api.2weima.com/
接口文档：https://www.usemock.com/docs/61dd9d92e403d6df879a6841

### install
```
composer require "2weima/open-api"
```

### usage

使用方法请参考 samples 目录下的示例

```
use api_2weima_com\Client as api_2weima_client;


/**
 * token 在这获取 https://www.2weima.com/user/api_tokens/index.html   
 * 需要授权 ： qr:encode  qr:decode
 */

$config = array(
	'token'=> '3816|RMSuQC....uwAGenrYf', //换成你自己的token 
	'is_mock'=>false, // usemock.com 测试数据
	'auto_compress'=>true, //本地图片压缩后再解码，速度更快
	'compress_max_width'=>800, //图片最大宽度 ，如果你的图片很大尺寸，建议调整到合适大小进行压缩，取得更快的速度
	'compress_max_height'=>1200,//图片最大高度
	'compress_quality'=>85,//图片质量
);

$client = new api_2weima_client($config);

```

### 二维码解码

```

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
  
```


### 二维码生成

```


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

```


