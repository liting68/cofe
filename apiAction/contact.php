<?php
$act=filter($_REQUEST['act']);
switch ($act){
	case 'getFriends':
		getFriends();//好友/所有联系人(互相关注)
		break;
	case 'getFriendsByUsernames':
		getFriendsByUsernames();//根据咖啡号获取联系人
		break;
	case 'recentContacts':
		recentContacts();
		break;
	case 'myFavri':
		myFavri();//我关注的
		break;
	case 'myFuns':
		myFuns();//关注我的
		break;
	case 'myNewFunsCount':
		myNewFunsCount();//新关注我的人数
		break;
	case 'recommend':
		recommend();//推荐联系人
		break;
	case 'nearUsers'://附近想喝咖啡的人
		nearUsers();
		break;
	case 'createGroup':
		createGroup();//添加分组
		break;
	case 'updateGroup':
		updateGroup();//分组改名
		break;
	case 'myGroups':
		myGroups();//分组列表
		break;
	case 'myGroupWithUsers':
		myGroupWithUsers();//获取分组列表及好友
		break;
	case 'myAllGroupsWithUsers':
		myAllGroupsWithUsers();//获取所有分组列表及好友
		break;
	case 'divideIntoGroups':
		divideIntoGroups();//给联系人分组
		break;
	case 'follow';//邀约
		follow();
		break;
	case 'black';//拉黑
		black();
		break;
	case 'unblack';//转粉
		unblack();
		break;
	default:
		break;
}

function getFriends(){//好友/所有联系人(互相关注)
	global $db;
	$userid=filter($_REQUEST['userid']);
	$lng=filter($_REQUEST['lng']);
	$lat=filter($_REQUEST['lat']);
	$sql="select u.id as user_id,u.nick_name,u.user_name,u.signature as talk,u.sex,u.head_photo_id,upt.path as head_photo,u.lng,u.lat from ".DB_PREFIX."user u left join ".DB_PREFIX."user_relation ur1 on u.id=ur1.relation_id
			left join ".DB_PREFIX."user_relation ur2 on ur1.relation_id = ur2.user_id 
			left join ".DB_PREFIX."user_photo upt on u.head_photo_id = upt.id
			where ur2.relation_id = $userid and ur1.user_id = $userid and ur1.status=1 ";
	$data=$db->getAllBySql($sql);
	foreach ($data as $k=>$d){
		//根据经纬度获取地址 http://api.map.baidu.com/geocoder?location=纬度,经度&output=输出格式类型&key=用户密钥
// 		$add_json=file_get_contents("http://api.map.baidu.com/geocoder?location=".$d['lat'].",".$d['lng']."&output=json&ak=".BAIDU_AK);
// 		$add=json_decode($add_json);
// 		if($add->status==0){
// 			$data[$k]['current_address']=$add->result->formatted_address;//当前用户位置
// 		}
		$data[$k]['distance']=(!empty($d['lat'])&&!empty($d['lng'])&&!empty($lng)&&!empty($lat))?getDistance($lat,$lng,$d['lat'],$d['lng']):lang_UNlOCATE;
		
	}
	
	echo json_result($data);
	
}

function getFriendsByUsernames(){
	global $db;
	$usernames=filter($_REQUEST['usernames']);
	$users=split(",", $usernames);
	$data=array();
	foreach ($users as $u){
		$sql="select u.id as user_id,upt.path as head_photo,u.nick_name,u.user_name from ".DB_PREFIX."user u left join ".DB_PREFIX."user_photo upt on u.head_photo_id = upt.id where user_name ='$u' ";
		$obj=$db->getRowBySql($sql);
		if(isset($obj['user_name'])){
			$data[$u]=$obj;
		}
	}
	echo json_result($data);
}

function recentContacts(){//根据user_relation的updated时间判断最近更新的联系人
	global $db;
	$userid=filter($_REQUEST['userid']);
	$lng=filter($_REQUEST['lng']);
	$lat=filter($_REQUEST['lat']);
	$sql="select u.id as user_id,u.nick_name,u.user_name,u.signature as talk,u.sex,u.head_photo_id,upt.path as head_photo,u.lng,u.lat from ".DB_PREFIX."user u left join ".DB_PREFIX."user_relation ur1 on u.id=ur1.relation_id
			left join ".DB_PREFIX."user_relation ur2 on ur1.relation_id = ur2.user_id 
			left join ".DB_PREFIX."user_photo upt on u.head_photo_id = upt.id
			where ur2.relation_id = $userid and ur1.user_id = $userid and ur1.status=1  order by ur1.updated desc ";
	$data=$db->getAllBySql($sql);
	foreach ($data as $k=>$d){
		//根据经纬度获取地址 http://api.map.baidu.com/geocoder?location=纬度,经度&output=输出格式类型&key=用户密钥
// 		$add_json=file_get_contents("http://api.map.baidu.com/geocoder?location=".$d['lat'].",".$d['lng']."&output=json&ak=".BAIDU_AK);
// 		$add=json_decode($add_json);
// 		if($add->status==0){
// 			$data[$k]['current_address']=$add->result->formatted_address;//当前用户位置
// 		}
		$data[$k]['distance']=(!empty($d['lat'])&&!empty($d['lng'])&&!empty($lng)&&!empty($lat))?getDistance($lat,$lng,$d['lat'],$d['lng']):lang_UNlOCATE;
		
	}
	
	echo json_result($data);
}

function myFavri(){//我关注的
	global $db;
	$userid=filter($_REQUEST['userid']);
	$lng=filter($_REQUEST['lng']);
	$lat=filter($_REQUEST['lat']);
	$sql="select u.id as user_id,u.nick_name,u.user_name,u.talk,u.signature,u.sex,u.head_photo_id,upt.path as head_photo,u.lng,u.lat from ".DB_PREFIX."user u left join ".DB_PREFIX."user_relation ur1 on u.id=ur1.relation_id 
	left join ".DB_PREFIX."user_photo upt on u.head_photo_id = upt.id
	where allow_find=1 and ur1.user_id = $userid and ur1.status=1 ";
	$data=$db->getAllBySql($sql);
	foreach ($data as $k=>$d){
		//根据经纬度获取地址 http://api.map.baidu.com/geocoder?location=纬度,经度&output=输出格式类型&key=用户密钥
// 		$add_json=file_get_contents("http://api.map.baidu.com/geocoder?location=".$d['lat'].",".$d['lng']."&output=json&ak=".BAIDU_AK);
// 		$add=json_decode($add_json);
// 		if($add->status==0){
// 			$data[$k]['current_address']=$add->result->formatted_address;//当前用户位置
// 		}
		$data[$k]['distance']=(!empty($d['lat'])&&!empty($d['lng'])&&!empty($lng)&&!empty($lat))?getDistance($lat,$lng,$d['lat'],$d['lng']):lang_UNlOCATE;
		
	}
	
	echo json_result($data);

}

function myFuns(){//关注我的
	global $db;
	$userid=filter($_REQUEST['userid']);
	$lng=filter($_REQUEST['lng']);
	$lat=filter($_REQUEST['lat']);
	$sql="select u.id as user_id,u.nick_name,u.user_name,u.talk,u.signature,u.sex,u.head_photo_id,upt.path as head_photo,u.lng,u.lat from ".DB_PREFIX."user u left join ".DB_PREFIX."user_relation ur2 on u.id=ur2.user_id 
			left join ".DB_PREFIX."user_photo upt on u.head_photo_id = upt.id
			where ur2.relation_id = $userid and ur2.status=1 ";
	$data=$db->getAllBySql($sql);
	foreach ($data as $k=>$d){
		//根据经纬度获取地址 http://api.map.baidu.com/geocoder?location=纬度,经度&output=输出格式类型&key=用户密钥
// 		$add_json=file_get_contents("http://api.map.baidu.com/geocoder?location=".$d['lat'].",".$d['lng']."&output=json&ak=".BAIDU_AK);
// 		$add=json_decode($add_json);
// 		if($add->status==0){
// 			$data[$k]['current_address']=$add->result->formatted_address;//当前用户位置
// 		}
		$data[$k]['distance']=(!empty($d['lat'])&&!empty($d['lng'])&&!empty($lng)&&!empty($lat))?getDistance($lat,$lng,$d['lat'],$d['lng']):lang_UNlOCATE;
		
	}
	$db->update('user_relation', array('ischeck'=>1),array('relation_id'=>$userid));
	
	echo json_result($data);
	
}

function myNewFunsCount(){//新关注我的人数
	global $db;
	$userid=filter($_REQUEST['userid']);
	$count=$db->getCount('user_relation',array('relation_id'=>$userid,'ischeck'=>'0'));
	echo json_result(array('count'=>$count));
}

function recommend(){//推荐(附近常住地址) RANGE_KILO公里以内
	global $db;
	$userid=filter($_REQUEST['userid']);
	$lng=filter($_REQUEST['lng']);
	$lat=filter($_REQUEST['lat']);
	$page_no = isset ( $_GET ['page'] ) ? $_GET ['page'] : 1;
	$page_size = PAGE_SIZE;
	$start = ($page_no - 1) * $page_size;
	if(empty($lng)||empty($lat)){
		$user=$db->getRow('user',array('id'=>$userid));
		$lng=$user['ad_lng'];
		$lat=$user['ad_lat'];
	}
	if(!empty($lng)&&!empty($lat)){
		$sql="select *,u.head_photo_id,upt.user_id,upt.path as head_photo,u.lng,u.lat from ".DB_PREFIX."user u 
			left join ".DB_PREFIX."user_photo upt on u.head_photo_id = upt.id
			where u.allow_add = 1 and allow_find=1 and round(6378.138*2*asin(sqrt(pow(sin( ($lat*pi()/180-ad_lat*pi()/180)/2),2)+cos($lat*pi()/180)*cos(ad_lat*pi()/180)* pow(sin( ($lng*pi()/180-ad_lng*pi()/180)/2),2)))*1000) <= ".RANGE_KILO;
		$data=$db->getAllBySql($sql." limit $start,$page_size");
		foreach ($data as $k=>$d){
			//根据经纬度获取地址 http://api.map.baidu.com/geocoder?location=纬度,经度&output=输出格式类型&key=用户密钥
// 			$add_json=file_get_contents("http://api.map.baidu.com/geocoder?location=".$d['lat'].",".$d['lng']."&output=json&ak=".BAIDU_AK);
// 			$add=json_decode($add_json);
// 			if($add->status==0){
// 				$data[$k]['current_address']=$add->result->formatted_address;//当前用户位置
// 			}
			$data[$k]['distance']=(!empty($d['lat'])&&!empty($d['lng'])&&!empty($lng)&&!empty($lat))?getDistance($lat,$lng,$d['lat'],$d['lng']):lang_UNlOCATE;
			
		}
		echo json_result($data);
	}else{
		echo json_result(null,'40','获取不到经纬度,请设置允许获取位置');
	}
}

function nearUsers(){//附近想喝咖啡的人
	global $db;
	$lng=filter($_REQUEST['lng']);
	$lat=filter($_REQUEST['lat']);
	$page_no = isset ( $_GET ['page'] ) ? $_GET ['page'] : 1;
	$page_size = PAGE_SIZE;
	$start = ($page_no - 1) * $page_size;
	if(empty($lng)||empty($lat)){
		echo json_result(null,'40','获取不到经纬度,请设置允许获取位置');
		return;
	}
	$sql="select u.id,u.nick_name,u.user_name,u.talk,u.signature,u.head_photo_id,upt.user_id,upt.path as head_photo,u.lng,u.lat from ".DB_PREFIX."user u 
		left join ".DB_PREFIX."user_photo upt on u.head_photo_id = upt.id
		where u.allow_add = 1 and allow_find=1 and round(6378.138*2*asin(sqrt(pow(sin( ($lat*pi()/180-lat*pi()/180)/2),2)+cos($lat*pi()/180)*cos(lat*pi()/180)* pow(sin( ($lng*pi()/180-lng*pi()/180)/2),2)))*1000) <= ".RANGE_KILO;
	$data=$db->getAllBySql($sql." order by  sqrt(power(lng-{$lng},2)+power(lat-{$lat},2)) limit $start,$page_size");
	foreach ($data as $k=>$d){
		//根据经纬度获取地址 http://api.map.baidu.com/geocoder?location=纬度,经度&output=输出格式类型&key=用户密钥
// 		$add_json=file_get_contents("http://api.map.baidu.com/geocoder?location=".$d['lat'].",".$d['lng']."&output=json&ak=".BAIDU_AK);
// 		$add=json_decode($add_json);
// 		if($add->status==0){
// 			$data[$k]['current_address']=$add->result->formatted_address;//当前用户位置
// 		}
		$data[$k]['distance']=(!empty($d['lat'])&&!empty($d['lng'])&&!empty($lng)&&!empty($lat))?getDistance($lat,$lng,$d['lat'],$d['lng']):lang_UNlOCATE;
		
	}
	echo json_result($data);
}

function myGroups(){//分组列表
	global $db;
	$userid=filter($_REQUEST['userid']);
	$groups=$db->getAll('user_group',array('user_id'=>$userid));
	echo json_result($groups);
}

function myGroupWithUsers(){//获取分组好友
	global $db;
	$userid=filter($_REQUEST['userid']);
	$groupid=filter($_REQUEST['groupid']);
	$sql="select u.id as user_id,u.nick_name,u.user_name,u.signature as talk,u.sex,u.head_photo_id,upt.path as head_photo,u.lng,u.lat from ".DB_PREFIX."user u left join ".DB_PREFIX."user_relation ur1 on u.id=ur1.relation_id
		left join ".DB_PREFIX."user_relation ur2 on ur1.relation_id = ur2.user_id
		left join ".DB_PREFIX."user_photo upt on u.head_photo_id = upt.id
		where ur2.relation_id = $userid and ur1.user_id = $userid and ur1.status=1  and ur1.group_id in (".$groupid.")";
	$users=array();
	$users=$db->getAllBySql($sql);
	foreach ($users as $k=>$d){
		//根据经纬度获取地址 http://api.map.baidu.com/geocoder?location=纬度,经度&output=输出格式类型&key=用户密钥
// 		$add_json=file_get_contents("http://api.map.baidu.com/geocoder?location=".$d['lat'].",".$d['lng']."&output=json&ak=".BAIDU_AK);
// 		$add=json_decode($add_json);
// 		if($add->status==0){
// 			$users[$k]['current_address']=$add->result->formatted_address;//当前用户位置
// 		}
		
	}
	echo json_result($users);
}


function myAllGroupsWithUsers(){//获取所有分组列表及好友
	global $db;
	$userid=filter($_REQUEST['userid']);
	$groups=$db->getAll('user_group',array('user_id'=>$userid));
	$data=array();
	foreach ($groups as $g){
		$sql="select u.id as user_id,u.nick_name,u.user_name,u.signature as talk,u.sex,u.head_photo_id,upt.path as head_photo,u.lng,u.lat from ".DB_PREFIX."user u left join ".DB_PREFIX."user_relation ur1 on u.id=ur1.relation_id
			left join ".DB_PREFIX."user_relation ur2 on ur1.relation_id = ur2.user_id
			left join ".DB_PREFIX."user_photo upt on u.head_photo_id = upt.id
			where ur2.relation_id = $userid and ur1.user_id = $userid and ur1.status=1  and ur1.group_id=".$g['id'];
		$users=array();
		$users=$db->getAllBySql($sql);
		foreach ($users as $k=>$d){
			//根据经纬度获取地址 http://api.map.baidu.com/geocoder?location=纬度,经度&output=输出格式类型&key=用户密钥
// 			$add_json=file_get_contents("http://api.map.baidu.com/geocoder?location=".$d['lat'].",".$d['lng']."&output=json&ak=".BAIDU_AK);
// 			$add=json_decode($add_json);
// 			if($add->status==0){
// 				$users[$k]['current_address']=$add->result->formatted_address;//当前用户位置
// 			}
		}
		$g['users']=$users;
		$data[]=$g;
	}
	echo json_result($data);
}

function createGroup(){//添加分组
	global $db;
	$userid=filter($_REQUEST['userid']);
	$name=filter($_REQUEST['name']);//分组名称
	if(empty($userid)){
		echo json_result(null,'38','用户未登录');
		return;
	}
	if(empty($name)){
		echo json_result(null,'41','分组名称为空');
		return;
	}
	$info=array('user_id'=>$userid,'name'=>$name,'created'=>date("Y-m-d H:i:s"));
	$info['id']=$db->create('user_group', $info);
	echo json_result($info);
	
}

function updateGroup(){//修改分组名
	global $db;
	$userid=filter($_REQUEST['userid']);
	$groupid=filter($_REQUEST['groupid']);
	$name=filter($_REQUEST['name']);//分组名称
	if(empty($userid)){
		echo json_result(null,'38','用户未登录');
		return;
	}
	if(empty($groupid)){
		echo json_result(null,'45','分组id为空');
		return;
	}
	if(empty($name)){
		echo json_result(null,'46','分组名称为空');
		return;
	}
	$info=array('user_id'=>$userid,'id'=>$groupid);
	$db->update('user_group',array('name'=>$name), $info);
	$info['name']=$name;
	echo json_result($info);
}

function divideIntoGroups(){//给联系人分组
	global $db;
	$loginid=filter($_REQUEST['loginid']);
	$userid=filter($_REQUEST['userid']);
	$groupid=filter($_REQUEST['groupid']);
	if(empty($loginid)){
		echo json_result(null,'38','用户未登录');
		return;
	}
	if(empty($userid)){
		echo json_result(null,'42','未指定联系人');
		return;
	}
	if(empty($groupid)){
		echo json_result(null,'43','未指定分组');
		return;
	}
	$ur=$db->getRow('user_relation',array('user_id'=>$loginid,'relation_id'=>$userid));
	$db->update('user_relation',array('group_id'=>$groupid),array('user_id'=>$loginid,'relation_id'=>$userid));
	$gus=array();
	$group_old=$db->getRow('user_group',array('id'=>$ur['group_id'],'user_id'=>$loginid));
	$group_old['users']=getUsersByGroupId($loginid,$group_old['id']);
	$group_new=$db->getRow('user_group',array('id'=>$groupid,'user_id'=>$loginid,));
	$group_new['users']=getUsersByGroupId($loginid,$groupid);
	$gus['group_old']=$group_old;
	$gus['group_new']=$group_new;
	echo json_result($gus);
}

function follow(){//关注
	global $db;
	$loginid=filter($_REQUEST['loginid']);
	$userid=filter($_REQUEST['userid']);
	//默认分组好友
	$ginfo=array('user_id'=>$loginid,'name'=>'好友');
	$fgroup=$db->getRow('user_group',$ginfo);
	if(!is_array($fgroup)&&count($fgroup)==0){//没有好友分组
		$ginfo['created']=date("Y-m-d H:i:s");
		$gid=$db->create('user_group', $ginfo);
	}else{
		$gid=$fgroup['id'];
	}
	//好友关系
	$rinfo=array('user_id'=>$loginid,'relation_id'=>$userid);
	$relation=$db->getRow('user_relation',array('user_id'=>$loginid,'relation_id'=>$userid));
	$touser=$db->getRow('user',array('id'=>$userid));
	if($touser['allow_flow']==2){
		echo json_result(null,'47','对方不想被陌生人邀约');
		return;
	}
	if(!is_array($relation)||count($relation)==0){//没关注
		$rinfo['group_id']=$gid;
		$rinfo['created']=date("Y-m-d H:i:s");
		$db->create('user_relation', $rinfo);//关注
	}elseif ($relation['status']==2){//拉黑者
		$relation['status']=1;
		unset($relation['updated']);
		$db->update('user_relation', $relation,$rinfo);//重新关注
	}
	echo json_result(array('userid'=>$userid));
}

function black(){//拉黑
	global $db;
	$loginid=filter($_REQUEST['loginid']);
	$userid=filter($_REQUEST['userid']);
	//默认分组好友
	$ginfo=array('user_id'=>$loginid,'name'=>'好友');
	$fgroup=$db->getRow('user_group',$ginfo);
	if(!is_array($fgroup)&&count($fgroup)==0){//没有好友分组
		$ginfo['created']=date("Y-m-d H:i:s");
		$gid=$db->create('user_group', $ginfo);
	}else{
		$gid=$fgroup['id'];
	}
	//好友关系
	$rinfo=array('user_id'=>$loginid,'relation_id'=>$userid);
	$relation=$db->getRow('user_relation',array('user_id'=>$loginid,'relation_id'=>$userid));
	if(!is_array($relation)||count($relation)==0){//没关注
		$rinfo['group_id']=$gid;
		$rinfo['status']=2;
		$rinfo['created']=date("Y-m-d H:i:s");
		$db->create('user_relation', $rinfo);//关注
	}else{//拉黑者
		$relation['status']=2;
		unset($relation['updated']);
		$db->update('user_relation', $relation,$rinfo);//重新关注
	}
	$db->update('user_relation',array('status'=>2),array('user_id'=>$loginid,'relation_id'=>$userid));
	echo json_result(array('userid'=>$userid));
}

function unblack(){//转粉
	global $db;
	$loginid=filter($_REQUEST['loginid']);
	$userid=filter($_REQUEST['userid']);
	//默认分组好友
	$ginfo=array('user_id'=>$loginid,'name'=>'好友');
	$fgroup=$db->getRow('user_group',$ginfo);
	if(!is_array($fgroup)&&count($fgroup)==0){//没有好友分组
		$ginfo['created']=date("Y-m-d H:i:s");
		$gid=$db->create('user_group', $ginfo);
	}else{
		$gid=$fgroup['id'];
	}
	//好友关系
	$rinfo=array('user_id'=>$loginid,'relation_id'=>$userid);
	$relation=$db->getRow('user_relation',array('user_id'=>$loginid,'relation_id'=>$userid));
	if(!is_array($relation)||count($relation)==0){//没关注
		$rinfo['group_id']=$gid;
		$rinfo['status']=1;
		$rinfo['created']=date("Y-m-d H:i:s");
		$db->create('user_relation', $rinfo);//关注
	}else{//已关注
		$relation['status']=1;
		unset($relation['updated']);
		$db->update('user_relation', $relation,$rinfo);//重新关注
	}
	$db->update('user_relation',array('status'=>1),array('user_id'=>$loginid,'relation_id'=>$userid));
	echo json_result(array('userid'=>$userid));
}

function getUsersByGroupId($userid,$groupid){//获取分组好友
	global $db;
	$sql="select u.id as user_id,u.nick_name,u.user_name,u.signature as talk,u.sex,u.head_photo_id,upt.path as head_photo,u.lng,u.lat from ".DB_PREFIX."user u left join ".DB_PREFIX."user_relation ur1 on u.id=ur1.relation_id
		left join ".DB_PREFIX."user_photo upt on u.head_photo_id = upt.id
		where ur1.user_id = $userid and ur1.status=1  and ur1.group_id =".$groupid." order by ur1.created asc";
	$users=$db->getAllBySql($sql);
	foreach ($users as $k=>$d){
		//根据经纬度获取地址 http://api.map.baidu.com/geocoder?location=纬度,经度&output=输出格式类型&key=用户密钥
// 		$add_json=file_get_contents("http://api.map.baidu.com/geocoder?location=".$d['lat'].",".$d['lng']."&output=json&ak=".BAIDU_AK);
// 		$add=json_decode($add_json);
// 		if($add->status==0){
// 			$users[$k]['current_address']=$add->result->formatted_address;//当前用户位置
// 		}
	}
	return $users;
}

