<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UploadFileController extends Controller
{
    //判断文件是否存在  可以使用advanced rest client来测试
    public function uploadFile(Request $request){
    	if ($request->hasFile('photo') && $request->file('photo')->isValid()) {
    		$file = $request->file('photo');
    		$extension = $file->getClientOriginalExtension('photo');
    		//$store_result = $photo->store('photo');
    		$store_result = $file->storeAs('photo',$file->getClientOriginalName().'.'.$extension);
    		$state = ['state'=>200, 'message'=>'返回成功'];
    		$output = [
    			'state' => $state,
    			'extension' => $extension,
    			'store_result' => $store_result
    		];
    		return $output;
    	}
    	exit('未获取到上传文件或上传过程中出错');
    }
}
