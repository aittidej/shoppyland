<?php

/* @var $this yii\web\View */

$this->title = 'My Yii Application';
?>
<style>
.search {
	width: 570px;
	height: 28px;
	border-top: 1px solid #bdbdbd;
	border-bottom: 1px solid #d3d3d3;
	border-right: 1px solid #d3d3d3;
	border-left: 1px solid #d3d3d3;
	padding-right: 30px;
	font: 16px arial, sans-serif;
	text-indent:5px;
	background: url('data:image/gif;base64,R0lGODlhHAAmAKIHAKqqqsvLy0hISObm5vf394uLiwAAAP///yH5BAEAAAcALAAAAAAcACYAAAO9eLpMIMYIQJi9DcYtKv6KtnHgB4yoAZSXKAyDy1rjoAzjzOQLrx8+4OanCAZnxiExGSEKmz3lj2lwUq3SZ3WZPbKuXGgxu9t4tLYDTkpIRQILF0x2G4lWipM7gj/oJQUkcXsCDCIFATULBCIcZ2tvB3QLDxETFnR/BgU/gRt9jX0gnpYMkJZpFzEoqQqJKAIBaQOVKHAXr3t7txgBjboSvB8EpLoFZywOAo3LFE5lYs/QW9LT1TRk1V7S2xYJADs');
	background-repeat:no-repeat;
	background-position:548px 4px;
	background-size:14px 19px;
	}

.button {
	border: 1px solid #d3d3d3;
	background: #f3f3f3;
	color:#696969;
	margin-left:4px;
	margin-right:4px;
	margin-top: 15px;
	font-family: arial, sans-serif;
	font-size: 11px;
	font-weight: bold;
	padding: 7px;
	border-radius:2px;
}

.button:hover {
	color: #2a2a2a;
	border: 1px solid #bdbdbd;
}

.search:hover {
	border:1px solid #aaaaaa;
}

</style>
<div class="site-index">
	<center>
		<div class='col-sm-12' style='margin-top: 14%;'>
			<div class='col-sm-12'>
				<img src="https://www.google.com/images/srpr/logo11w.png"/ style='width: 270px;height: 95px;margin:5px;'>
			</div>
			<div class='col-sm-12'><br></div>
			<div class='col-sm-12'>
				<form name="google" action="https://www.google.com/search" method="GET"><br>
					<input type="search" name='q' class="search"><br><br>
					<input type="submit" class="button" name="submit" value="Google Search">
					<input type="submit" class="button" name="lucky" value="I'm Feeling Lucky">
				</form>
			</div>
		</div>
	</center>
</div>
