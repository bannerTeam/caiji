<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:66:"D:\www\sex_caiji\public/../application/index\view\caiji\index.html";i:1538646365;}*/ ?>
<!DOCTYPE html>
<html>

	<head>
		<meta charset="UTF-8">
		<title>采集</title>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.1/jquery.js"></script>
	</head>

	<body>

		
		<p>
			请求页数：
			<input type="text" value="3" id="page" onkeyup="this.value=this.value.replace(/\D/g,'')" onkeydown="this.value=this.value.replace(/\D/g,'')" maxlength="2"  />
		</p>
		<ul id="list">
			
		</ul>
		
		<script>
			var cj = [
				{
					title: "AVHD101 高清女优在线A片",
					key: "avhd101"
				},
				{
					title: "Free Porn Videos - YouAV",
					key: "youav"
				}
			];
			
			var temp = "";
			for (var i = 0; i < cj.length; i++) {
				temp += `<li>${cj[i].title}<a onclick="startCj(${i})" href="javascript:;" >采集</a></li>`;
			}			
			$("#list").append(temp);
			
			function startCj(i){
				
				var pageLength = $("#page").val();
				
				location.href = `/index.php/index/caiji/${cj[i].key}?pl=${pageLength}`;
			}
			
		</script>
		
	</body>

</html>