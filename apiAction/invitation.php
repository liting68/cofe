<?php
require_once APP_DIR.DS.'apiLib'.DS.'ext'.DS.'Umeng.php';
$act=filter($_REQUEST['act']);
switch ($act){
	case 'getNewInvitationCounts':
		getNewInvitationCounts();//获取未读邀请函
		break;
	case 'sendInvitation':
		sendInvitation();//发送邀请函
		break;
	case 'getInvitation':
		getInvitation();//查看邀请函
		break;
	case 'acceptInvitation'://接受邀请函
		acceptInvitation();
		break;
	case 'refuseInvitation'://拒绝邀请函
		refuseInvitation();
		break;
	case 'invitationBySend'://我发出的
		invitationBySend();
		break;
	case 'invitationByAccept'://我接受的
		invitationByAccept();
		break;
	case 'cancelInvitation'://取消邀请函
		cancelInvitation();
		break;
	default:
		break;
}

function getNewInvitationCounts(){//获取未读邀请函
	global $db;
	$userid=filter($_REQUEST['userid']);
	$count=$db->getRowBySql("select count(*) as num from ".DB_PREFIX."invitation where ( (user_id=$userid && isread_user=2) or (to_user_id=$userid && isread_to_user=2) ) limit 1");
	echo json_result(array('count'=>$count['num']));
	
}

//发送邀请函
function sendInvitation(){
	global $db;
	
	$userid=filter(!empty($_REQUEST['userid'])?$_REQUEST['userid']:'');
	$to_userid=filter(!empty($_REQUEST['touserid'])?$_REQUEST['touserid']:'');
	$title=filter(!empty($_REQUEST['title'])?$_REQUEST['title']:'');
	$datetime=filter(!empty($_REQUEST['datetime'])?$_REQUEST['datetime']:'');
	//$address=filter(!empty($_REQUEST['address'])?$_REQUEST['address']:'');
	$shopid=filter(!empty($_REQUEST['shopid'])?$_REQUEST['shopid']:'');
	$tel=filter(!empty($_REQUEST['tel'])?$_REQUEST['tel']:'');

	//待接收邀约数
	$count=$db->getCount('invitation',array('status'=>1,'user_id'=>$userid));
	if($count>0){
		echo json_result(null,'2','您还有一个待对方接收的邀请,或许您想取消');//是的,要取消,不,再耐心等等
		return;
	}
	
	$shop=$db->getRow('shop',array('id'=>$shopid));
	//isreaded 1已读 2未读
	$invitation=array('title'=>$title,'datetime'=>$datetime,'shop_id'=>$shopid,'address'=>$shop['address'],'lng'=>$shop['lng'],'lat'=>$shop['lat'],'tel'=>$tel,'user_id'=>$userid,'to_user_id'=>$to_userid,'isreaded_user'=>1,'isreaded_to_user'=>2,'status'=>1,'created'=>date("Y-m-d H:i:s"));
	$db->create('invitation', $invitation);
	
// 	$Aumeng=new Umeng('Android');
// 	$Aumeng->sendAndroidCustomizedcast("invitation",$to_userid,"您有新的邀约","咖啡约我","新的邀请函","go_app","");//go_activity
	
// 	$IOSumeng=new Umeng('IOS');
// 	$IOSumeng->sendIOSCustomizedcast("invitation", $to_userid, "您有新的邀约");

	echo json_result(array('success'=>'TRUE'));

}

//查看邀请函
function getInvitation(){
	global $db;
	
	$invitationid=filter(!empty($_REQUEST['invitationid'])?$_REQUEST['invitationid']:'');//邀请函id
	$userid=filter(!empty($_REQUEST['userid'])?$_REQUEST['userid']:'');//登录者id
	$invitation=$db->getRow('invitation',array('id'=>$invitationid));
	if(empty($invitation)){
		echo json_result(null,'2','您查看的内容不存在');
		return;
	}
	if($invitation['user_id']==$userid){
		$db->update('invitation', array('isreaded_user'=>1),array('id'=>$invitationid));
	}
	if($invitation['to_user_id']==$userid){
		$db->update('invitation', array('isreaded_to_user'=>1),array('id'=>$invitationid));
	}
	$from=$db->getRow('user',array('id'=>$invitation['user_id']));
	$invitation['user_photo']='';
	$touser=$db->getRow('user',array('id'=>$invitation['to_user_id']));
	$invitation['to_user_photo']='';
	if(!empty($from['head_photo_id'])){
		$fromphoto=$db->getRow('user_photo',array('id'=>$from['head_photo_id']));
		$invitation['user_photo']=$fromphoto['path'];
	}
	if(!empty($touser['head_photo_id'])){
		$tophoto=$db->getRow('user_photo',array('id'=>$touser['head_photo_id']));
		$invitation['to_user_photo']=$tophoto['path'];
	}
	$res['invitation']=$invitation;
	
// 	$info=$db->getRow('user',array('id'=>$invitation['user_id']));
// 	unset($info['user_password']);
// 	$me=$db->getRow('user',array('id'=>$userid));
// 	$info['distance']=(!empty($me['lat'])&&!empty($me['lng'])&&!empty($info['lat'])&&!empty($info['lng']))?getDistance($info['lat'],$info['lng'],$me['lat'],$me['lng']):lang_UNlOCATE;
// 	$info['lasttime']=time2Units(time()-strtotime($info['logintime']));
// 	$info['address']=getAddressFromBaidu($info['lng'],$info['lat']);
// 	//头像
// 	if(!empty($info['head_photo_id'])){
// 		$head=$db->getRow('user_photo',array('id'=>$info['head_photo_id']));
// 		$info['head_photo']=$head['path'];
// 	}
// 	$res['userInfo']=$info;
	
	echo json_result($res);
	
}

//接受
function acceptInvitation(){
	global $db;
	$invitationid=filter(!empty($_REQUEST['invitationid'])?$_REQUEST['invitationid']:'');//邀请函id
	$userid=filter(!empty($_REQUEST['userid'])?$_REQUEST['userid']:'');//登录者id
	$count=$db->getCount('invitation',array('id'=>$invitationid,'to_user_id'=>$userid));
	if ($count<=0){
		echo json_result(null,'2','数据不符,您不能接受不属于您的邀请函');
		return;
	}
	$db->update('invitation', array('isreaded_user'=>2,'isreaded_to_user'=>1,'status'=>2),array('id'=>$invitationid));
	echo json_result(array('success'=>'TRUE'));
	
}

//拒绝
function refuseInvitation(){
	global $db;
	$invitationid=filter(!empty($_REQUEST['invitationid'])?$_REQUEST['invitationid']:'');//邀请函id
	$userid=filter(!empty($_REQUEST['userid'])?$_REQUEST['userid']:'');//登录者id
	$count=$db->getCount('invitation',array('id'=>$invitationid,'to_user_id'=>$userid));
	if ($count<=0){
		echo json_result(null,'2','数据不符,您不能拒绝不属于您的邀请函');
		return;
	}
	$db->update('invitation', array('isreaded_user'=>2,'isreaded_to_user'=>1,'status'=>3),array('id'=>$invitationid));
	echo json_result(array('success'=>'TRUE'));
	
}

//我发出的邀请函
function invitationBySend(){
	global $db;
	$userid=filter(!empty($_REQUEST['userid'])?$_REQUEST['userid']:'');//登录者id
	if(empty($userid)){
		echo json_result(null,'2','请重新登录');
		return;
	}
	$sql="select inv.id,inv.title,tu.nick_name as to_nick_name,inv.to_user_id,inv.status,inv.isreaded_user,inv.isreaded_to_user,upt.path as photo from ".DB_PREFIX."invitation inv 
			left join ".DB_PREFIX."user tu on inv.to_user_id = tu.id 
			left join ".DB_PREFIX."user_photo upt on upt.id=tu.head_photo_id where 1=1 ";
	$sql.=" and inv.user_id=$userid ";
	$data=$db->getAllBySql($sql);
	echo json_result($data);
}

//我接受的邀请函
function invitationByAccept(){
	global $db;
	$userid=filter(!empty($_REQUEST['userid'])?$_REQUEST['userid']:'');//登录者id
	if(empty($userid)){
		echo json_result(null,'2','请重新登录');
		return;
	}
	$sql="select inv.id,inv.title,u.nick_name,inv.user_id,inv.status,inv.isreaded_user,inv.isreaded_to_user,upt.path as photo from ".DB_PREFIX."invitation inv 
			left join ".DB_PREFIX."user u on inv.user_id = u.id 
			left join ".DB_PREFIX."user_photo upt on upt.id=u.head_photo_id where 1=1 ";
	$sql.=" and inv.to_user_id=$userid ";
	$data=$db->getAllBySql($sql);
	echo json_result($data);
	
}

//取消邀请函
function cancelInvitation(){
	global $db;
	$userid=filter(!empty($_REQUEST['userid'])?$_REQUEST['userid']:'');//登录者id
	$invitationid=filter(!empty($_REQUEST['invitationid'])?$_REQUEST['invitationid']:'');//邀请函id
	$count=$db->getCount('invitation',array('id'=>$invitationid,'user_id'=>$userid));
	if(empty($userid)){
		echo json_result(null,'2','请重新登录');
		return;
	}
	if($count<=0){
		echo json_result(null,'3','请选择您发出的邀请函');
		return;
	}
	$db->update('invitation', array('status'=>4),array('id'=>$invitationid,'user_id'=>$userid));
	echo json_result(array('success'=>'TRUE'));
}

