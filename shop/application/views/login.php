<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>咖啡约我店家后台</title>
<link href="<?php echo base_url() ?>css/style.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="<?php echo base_url() ?>resource/js/common.js"></script>
</head>
<body style="background:#977342;">
<div class="login_box">
	<div class="login_t"><img src="<?php echo base_url() ?>images/login_t_03.jpg"></div>
   <form action="" method="post">
    <div class="login_in">
	 <?php if (!empty($error_msg)){ ?>
    	<p style="color:red;margin-left:50px;"><?php echo $error_msg ?></p>
     <?php } ?>
    	<ul>
        	<li>账号：<input id="admname" name="username" type="text"></li>
            <li>密码：<input id="pass" name="password" type="password"></li>
        </ul>
    </div>
    <div class="login_btn"><input type="image" src="<?php echo base_url() ?>images/login_btn_03.jpg" /><input onclick="document.getElementById('admname').value='';document.getElementById('pass').value='';return false;" type="image" src="<?php echo base_url() ?>images/login_btn_05.jpg" /></div>
    </form>
</div>

</body>
</html>