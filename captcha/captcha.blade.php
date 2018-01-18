<!DOCTYPE html>
<html>
<head>
	<meta charset="utf8">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<title>Captcha</title>
	<script type="text/javascript" src="/js/jquery-3.1.1.min.js"></script>
</head>
<body>
<img src="/captcha-create" onclick="changeCaptcha();" id="img"><br/>
<input type="text" name="captcha" value="{{ old('captcha') }}">
<button type="button" id="btn">verifyCaptcha</button>

<script type="text/javascript">
	//改变验证码
	function changeCaptcha(){
		var img = document.getElementById('img');
		img.src = "/captcha-create?random="+Math.random();
	}
	$(document).ready(function(){
		$.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN' : $('meta[name=csrf-token]').attr('content')//这里不能有;
			}
		});
		//提交验证码
		$('#btn').click(function(){
			captcha_value = $('input[name=captcha]').val();
			  $.ajax({
			    url: 'captcha-verify',
			    type: "POST",
			    dateType: "json",
			    data: {captcha : captcha_value},
			    success: function(data){
			    	if (data.status == 301) {
			    		changeCaptcha();
			    	};
			        console.log(data);
			    },
			    error: function(xhr,status,err){
			    	console.log(xhr.status);
			    	changeCaptcha();
			    }
			  });
		});//验证码click结束
	});
</script>
</body>
</html>