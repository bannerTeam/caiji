<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:65:"D:\www\sex_caiji\public/../application/index\view\demo\video.html";i:1539061593;}*/ ?>
<!DOCTYPE html>
<html>

	<head>
		<meta charset="UTF-8">
		<title>videojs</title>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.1/jquery.js"></script>
		
		<style type="text/css">
			body {
				margin: 0;
				padding: 0px;
				font-family: "Microsoft YaHei", YaHei, "微软雅黑", SimHei, "黑体";
				font-size: 14px
			}
			#playleft{ position: relative; background: #000000;}
			

		</style>
		
		<link href="/static/js/video/video.css" rel="stylesheet">
		<script src="/static/js/video/video.min.js"></script>
		<script src="/static/js/video/videojs-contrib-hls.js"></script>
			
		
	</head>

	<body>
		
		<div>
			VIDEO - m3u8
		</div>
		<div class="MacPlayer" style="width: 500PX; height: 300px;">
			<video id="roomVideo1" class="video-js vjs-default-skin" controls preload="none" ></video>
		</div>
		
		
		
		<script>
			
			$(function(){
				
				$("#playleft").append('');
				
				var w = $("#playleft").width();
				var h = $("#playleft").height();
				var myPlayer = videojs('roomVideo1',{
					autoplay:false,
		            poster: "http://vjs.zencdn.net/v/oceans.png",
		            height:h, 
    				width:w
		       });
		       myPlayer.src('/down/video/lu78/JRea6V8t/index.m3u8');
		       
		       
		      
			})	
			
			
			
		</script>
		
		
	</body>

</html>