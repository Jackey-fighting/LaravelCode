<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Gregwar\Captcha\CaptchaBuilder;

class ValidateController extends Controller
{
	//显示验证码页面
	public function showCaptch(){
		return view('captcha');
	}
    //生成验证码
    public function createValidateCode(){
    	$builder = new CaptchaBuilder;//生成验证码图片的Build对象，配置相应属性
    	$builder->build($width=250, $height=70, $font=null);//设置图片宽高及字体
    	$phrase = $builder->getPhrase();//获取验证码内容
    	session()->flash('validate_code', $phrase);//把内容存到session中
    	//生成图片
    	header('Cache-Control: no-cache, must-revalidate');
    	header('Content-Type: image/jpeg');
    	$builder->output();
    }
    //验证注册码是否正确
    public function verifyCaptcha(Request $request){
    	$captcha = $request->input('captcha');
    	if ($request->session()->get('validate_code') == $captcha) {
    		$status = [
    			'status'=>200,
    			'message' => '验证成功'
    		];
    		return $status;
    	}
    	return ['status'=>301, 'message'=>'验证失败'];
    }
}
