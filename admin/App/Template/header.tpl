<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>{$smarty.const.DEFAUT_TITLE}</title>
<link href="{$smarty.const.SITE}resource/css/style.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="{$smarty.const.SITE}resource/js/jQuery.js"></script>
<script type="text/javascript">
{literal}
$(function(){
	$(window).resize(function(){
		$('.main_l').height($(window).height()-160);
	});
	$('.main_l').height($(window).height()-160);
});
{/literal}
</script>
</head>
<body>
<div class="top">
	<div class="fl"><img src="resource/images/login_01.jpg"></div>
    <div class="fr top_fr">欢迎您 admin 管理员<a href="{url controller=Default action=LoginOut}">[ 退出系统 ]</a></div>
</div>
