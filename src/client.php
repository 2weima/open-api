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

namespace api_2weima_com;


class client
{

	const HOST_URL = 'https://api.2weima.com';
	const USEMOCK_URL = 'https://2weima.usemock.com'; // usemock.com 模拟数据的

	public $is_mock = false;//返回模拟数据
	/**
	 * 在这获取 https://www.2weima.com/user/api_tokens/index.html   
	 * 需要授权 ： qr:encode  qr:decode
	 */
	public $token = ''; //如  3816|RMSuQC.....AGenrYf
	// public $timestamp = 0;

	public $auto_compress = true;//压缩图片后再解码
	public $compress_max_width = 800;//图片最大宽度 ，如果你的图片很大尺寸，建议调整到合适大小进行压缩，取得更快的速度
	public $compress_max_height = 1200;//图片最大高度
	public $compress_quality = 85;//图片质量

	
	//qrdecode https://www.usemock.com/docs/61dd9d92e403d6df879a6841/61e658cbcf071eb2c124d0f1/61dd9de2e403d6df879a6855
	public $qr_image = ''; //图片url连接  要解码的图片
	public $qr_image_local = ''; //本地图片  将图片压缩后base64后直接提交解码（当两个参数都提交，优先base64）
	public $qr_detype = 'jie2weima';//默认：jie2weima 解二维码，jie1weima 解条码
	public $qr_multi = 'one';//默认：one获取一个结果 ，multi 获取多个结果qr_content变成数组
	//qrencode https://www.usemock.com/docs/61dd9d92e403d6df879a6841/61dd9db4e403d6df879a684d/61dd9e04e403d6df879a686c
	public $qr_content = '';//二维码内容
	public $qr_output = 'png';//只支持：png或svg ，使用SVG时 模板中设置的logo图、前背景图无效
	public $qr_footer = '';//底部文字，一物一码，通常需要变动，但字体大小、颜色等则通过模板设置
	public $template_id = 0;//美化二维码模板ID，二维工坊免费模板或自己设计的都可以，一旦设置，下面 qr_* 开头参数就无效
	public $qr_size = 350;//二维码尺寸
	public $qr_margin = 10;//二维码与图片边距
	public $qr_level = 'M'; //纠错级别 L M Q H
	public $qr_version = 0; //0自动，版本 0-40
	public $qr_shape = 0; //码格形状 0直角1圆 2液化
	public $qr_mark_shape = 0; //码眼形状 0直角1圆 2圆角
	
	public function __construct($config)
	{
		foreach($config as $key => $val){
			if(in_array($key,[
				'is_mock','token','auto_compress','compress_max_width','compress_max_height','compress_quality',
				//qrdecode
				'qr_image',
				'qr_base64',
				'qr_detype',
				'qr_multi',
				//qrencode
				'qr_content',
				'qr_output',
				'qr_footer',
				'template_id',
				'qr_size',
				'qr_margin',
				'qr_level',
				'qr_version',
				'qr_shape',
				'qr_mark_shape'])){

				$this->$key= $val;
			}
		}
		// $this->timestamp = time();
	}

	

	/**
	 * 生成二维码
	 *  
	 * */	
	public function qrencode(){

		$error_rs = [
			'status'=>0,
			'message'=>'生成失败',
			'qr_image'=>'',
		];
		

		if(empty($this->qr_content)){
			$error_rs['msg'] = '请输入二维码内容';
			return $error_rs;
		}


		$path = '/api/qrencode';
		$query_data = [
			'qr_content'=>$this->qr_content,
			'qr_output'=>$this->qr_output,
			'qr_footer'=>$this->qr_footer,
			'template_id'=>$this->template_id,
			'qr_size'=>$this->qr_size,
			'qr_margin'=>$this->qr_margin,
			'qr_level'=>$this->qr_level,
			'qr_version'=>$this->qr_version,
			'qr_shape'=>$this->qr_shape,
			'qr_mark_shape'=>$this->qr_mark_shape,
		];
		// print_r($query_data );exit;

		$content = $this->curl_request($path,$query_data);
	
		//解析json
		$content = json_decode($content,true);
		if($content){
			return $content;
		}
		return $error_rs;
	}
	


	/*
		解码返回示例
		// array(3) {
		// 	["status"]=>int(200)
		// 	["message"]=>string(7) "success"
		// 	["qr_content"]=>string(44) "https://u.wechat.com/MCwqa9eki8owhpRib5J4HDE"
		//   }
  
	*/
	public function qrdecode(){
		

		$error_rs = [
			'status'=>0,
			'message'=>'解码失败',
			'qr_content'=>'',
		];

		$imgdata = '';
		if(empty($this->qr_image) && $this->qr_image_local){
			$imgdata = $this->base64_encode_image($this->qr_image_local);
		}

		//无效参数
		if(empty($this->qr_image) && empty($imgdata)){
			$error_rs['msg'] = '请设置您要解码的图片 qr_image 功 qr_image_local';
			return $error_rs;
		}
		if($imgdata){
			$this->qr_image = '';
		}

		$path = '/api/qrdecode';
		$query_data = [
			'qr_image'=>$this->qr_image,
			'qr_detype'=>$this->qr_detype,
			'qr_multi'=>$this->qr_multi,
			'qr_base64'=>$imgdata,
		];
		//   print_r($query_data );exit;
		$content = $this->curl_request($path,$query_data);
	
		//解析json
		$content = json_decode($content,true);
		if($content){
			return $content;
		}
		return $error_rs;

	}
	/**
	 * 设置二维码内容
	 */
	public function set_content($qr_content){
		$this->qr_content = trim($qr_content);
		return $this;
	}
	//只支持：png或svg ，使用SVG时 模板中设置的logo图、前背景图无效
	public function set_output($qr_output='png'){
		$this->qr_output = $qr_output == 'svg' ? 'svg' : 'png';
		return $this;
	}
	//底部文字，一物一码，通常需要变动，但字体大小、颜色等则通过模板设置
	public function set_footer($qr_footer=''){
		$this->qr_footer = $qr_footer;
		return $this;
	}
	//美化二维码模板ID，二维工坊免费模板或自己设计的都可以，一旦设置，下面 qr_* 开头参数就无效
	public function set_template_id($template_id=0){
		$this->template_id = intval($template_id);
		return $this;
	}
	//二维码尺寸
	public function set_size($qr_size=350){
		if($qr_size<100){
			$qr_size = 100;
		}
		if($qr_size>1200){
			$qr_size = 1200;
		}
		$this->qr_size = intval($qr_size);
		return $this;
	}
	//二维码与图片边距
	public function set_margin($qr_margin=10){
		$this->qr_margin = intval($qr_margin);
		return $this;
	}
	//纠错级别 L M Q H
	public function set_level($qr_level='M'){
		if(!in_array($qr_level,['L','M','Q','H'])){
			$qr_level = 'M';
		}
		$this->qr_level = $qr_level;
		return $this;
	}
	//0自动，版本 0-40
	public function set_version($qr_version=350){
		if($qr_version<0){
			$qr_version = 0;
		}
		if($qr_version>40){
			$qr_version = 40;
		}
		$this->qr_version = intval($qr_version);
		return $this;
	}
	//码格形状
	public function set_shape($qr_shape=0){
		$this->qr_shape = intval($qr_shape);
		return $this;
	}
	//码眼形状
	public function set_mark_shape($qr_mark_shape=0){
		$this->qr_mark_shape = intval($qr_mark_shape);
		return $this;
	}
	


	/**
	 * 设置要解码的图片
	 */
	public function set_image($qr_image){
		$this->qr_image = $qr_image;
		return $this;
	}
	/**
	 * 设置要解码的图片
	 * 本地路径： /root/images/qr.png
	 * 会自动压缩提高解码速度
	 */
	public function set_image_local($qr_image_local){
		$this->qr_image_local = $qr_image_local;
		return $this;
	}
	//默认：jie2weima 解二维码，jie1weima 解条码
	public function set_detype($qr_detype='jie2weima'){
		if(!in_array($qr_detype,['jie2weima','jie1weima','jie12weima'])){
			$qr_detype = 'jie2weima';
		}
		$this->qr_detype = $qr_detype;
		return $this;
	}
	//默认：one获取一个结果 ，multi 获取多个结果qr_content变成数组
	public function set_multi($qr_multi='one'){
		$this->qr_multi = $qr_multi == 'multi' || $qr_multi == '' ? 'multi' : 'one';
		return $this;
	}


	
	/*
		图片转base64
	*/
	public function base64_encode_image ($image_file) {
	
		//是否先压缩
		if($this->auto_compress)
		{
			$this->easy_compress($image_file);
		}
	
		$base64_image = '';
		$image_info = getimagesize($image_file);
		$image_data = fread(fopen($image_file, 'r'), filesize($image_file));
		$base64_image = 'data:' . $image_info['mime'] . ';base64,' . chunk_split(base64_encode($image_data));
		return $base64_image;
	}

	/** 
	* 简单的压缩图片为 jpg
	* @param sting $imgsrc 图片保存路径 
	* @param string $imgdst 压缩后保存路径 [空则替换原图，压缩非jpg格式建议传参数] 
	* @param int $max_width = 600;//压缩后最大宽度
	* @param int $max_height = 800;//压缩后最大高度
	* @param int $quality = 80;//压缩质量
	*/
	public function easy_compress($imgsrc,$imgdst='',$max_width = 600,$max_height = 800,$quality = 80){ 
	
		if($this->compress_max_width>0)
		{
			$max_width = $this->compress_max_width;
		}

		if($this->compress_max_height>0)
		{
			$max_height = $this->compress_max_height;
		}

		if($this->compress_quality>0)
		{
			$quality = $this->compress_quality;
		}
		if(empty($imgdst)){
			$imgdst = $imgsrc;
		}

		list($width,$height,$img_type) = getimagesize($imgsrc); 

		$new_width = $width;
		$new_height = $height;
	 
		if(($max_width && $width > $max_width) || ($max_height && $height > $max_height))
		{
			if($max_width && $width>$max_width)
			{
				$widthratio = $max_width/$width;
				$resizewidth_tag = true;
			}
	 
			if($max_height && $height>$max_height)
			{
				$heightratio = $max_height/$height;
				$resizeheight_tag = true;
			}
	 
			if($resizewidth_tag && $resizeheight_tag)
			{
				if($widthratio<$heightratio)
					$ratio = $widthratio;
				else
					$ratio = $heightratio;
			}
	 
			if($resizewidth_tag && !$resizeheight_tag)
				$ratio = $widthratio;
			if($resizeheight_tag && !$resizewidth_tag)
				$ratio = $heightratio;
	 
			$new_width = $width * $ratio;
			$new_height = $height * $ratio;
		}

	  switch($img_type){ 
		case 1: 
			$image_wp=imagecreatetruecolor($new_width, $new_height); 
			$image = imagecreatefromgif($imgsrc); 
			imagecopyresampled($image_wp, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height); 
			imagejpeg($image_wp, $imgdst,75); 
			imagedestroy($image_wp); 
			break; 
		case 2: 
			$image_wp=imagecreatetruecolor($new_width, $new_height); 
			$image = imagecreatefromjpeg($imgsrc); 
			imagecopyresampled($image_wp, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height); 
			imagejpeg($image_wp, $imgdst,75); 
			imagedestroy($image_wp); 
			break; 
		case 3: 
			$image_wp=imagecreatetruecolor($new_width, $new_height); 
			$image = imagecreatefrompng($imgsrc); 
			imagecopyresampled($image_wp, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height); 
			imagejpeg($image_wp, $imgdst,75); 
			imagedestroy($image_wp); 
			break; 
	  } 
	} 


	/**
	 * 
	 * curl_request 
	 * $path /qrdecode
	 * query_data [] 
	 */
	public function curl_request($path,$query_data){
		
		
		if(!$path){
			return false;
		}
		// $query_data['timestamp'] = $this->timestamp;
		// $bodys = http_build_query($query_data); // application/x-www-form-urlencoded

		$headers = [
			'Authorization: Bearer '.$this->token,
			'Accept: application/json'
			// 'Content-Type:application/x-www-form-urlencoded; charset=UTF-8'
		];
	
		$url = ($this->is_mock ? self::USEMOCK_URL : self::HOST_URL) . $path;
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_FAILONERROR, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HEADER, false);
		if (1 == strpos('@'.$url, "https://"))
		{
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		}
		curl_setopt($curl, CURLOPT_POSTFIELDS, $query_data);
		// curl_setopt($curl, CURLOPT_POSTFIELDS, $bodys); // application/x-www-form-urlencoded
		$content = curl_exec($curl);
		
		return $content;
	}

}

