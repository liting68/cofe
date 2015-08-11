<?php
require_once APP_DIR.DS.'apiLib'.DS.'ext'.DS.'Upload.php';
require_once APP_DIR.DS.'apiLib'.DS.'ext'.DS.'Huanxin.php';
require_once APP_DIR.DS.'apiLib'.DS.'ext'.DS.'Sms.php';
require_once APP_DIR.DS.'apiLib'.DS.'ext'.DS.'Umeng.php';
$act=filter($_REQUEST['act']);
switch ($act){
	case 'deposit'://寄存咖啡
		deposit();
		break;
	case 'nearCafe'://附近邂逅咖啡
		nearCafe();
		break;
        case 'cafeInfo'://咖啡详情
                cafeInfo();
                break;
        case 'receive'://领取咖啡
                receive();
                break;
	default:
		break;
}

//寄存/等候咖啡
function deposit(){
        global $db;
	$userid=filter(!empty($_REQUEST['loginid'])?$_REQUEST['loginid']:'');
	$type=filter(!empty($_REQUEST['type'])?$_REQUEST['type']:'');//1爱心2缘分3约会4传递5等候
	$shopid=filter(!empty($_REQUEST['shopid'])?$_REQUEST['shopid']:'');
	$days=filter(!empty($_REQUEST['days'])?$_REQUEST['days']:'');
	$menuprice1_id=filter(!empty($_REQUEST['menuprice1_id'])?$_REQUEST['menuprice1_id']:'');
	$menuprice2_id=filter(!empty($_REQUEST['menuprice2_id'])?$_REQUEST['menuprice2_id']:'');
	$question=filter(!empty($_REQUEST['question'])?$_REQUEST['question']:'');
	$topic=filter(!empty($_REQUEST['topic'])?$_REQUEST['topic']:'');
	$msg=filter(!empty($_REQUEST['msg'])?$_REQUEST['msg']:'');
	$tag_ids=filter(!empty($_REQUEST['tag_ids'])?$_REQUEST['tag_ids']:'');
        
        $data=array();
        if(empty($userid)){
                echo json_result(null,'2','请您先登录');return ;
        }else{
                $data['user_id']=$userid;
        }
        if(empty($type)){
                echo json_result(null,'3','请选择主题');return ;
        }else{
                $data['type']=$type;
        }
        if(empty($shopid)){
                echo json_result(null,'4','请选择店铺');return ;
        }else{
                $data['shop_id']=$shopid;
        }
        if(empty($days)){
                echo json_result(null,'5','请选择寄存天数');return ;
        }else{
                $data['days']=$days;
        }
        if(empty($menuprice1_id)){
                echo json_result(null,'6','请选择寄存咖啡');return ;
        }else{
                $menuprice=$db->getRow('shop_menu_price',array('id'=>$menuprice1_id));
                if($menuprice['shop_id']!=$shopid){
                        echo json_result(null,'61','您选择的咖啡店发生改变,请重新选择咖啡');return ;
                }
                $data['menuprice1_id']=$menuprice1_id;
                $data['menu1_id']=$menuprice['menu_id'];
                $data['price1']=$menuprice['price'];
                $menu=$db->getRow('shop_menu',array('id'=>$menuprice['menu_id']));
                $data['product1']=$menu['title'];
                $data['product_img1']=$menu['img'];
        }
        switch ($type){
                case 2://缘分咖啡
                        if(empty($question)){
                                echo json_result(null,'7','请输入你想问的问题');return ;
                        }else{
                                $data['question']=$question;
                        }
                        break;
                case 3://约会咖啡
                        if(empty($menuprice2_id)){
                                echo json_result(null,'8','请选择第二杯寄存咖啡');return ;
                        }else{
                                $menuprice=$db->getRow('shop_menu_price',array('id'=>$menuprice2_id));
                                if($menuprice['shop_id']!=$shopid){
                                        echo json_result(null,'81','您选择的咖啡店发生改变,请重新选择咖啡');return ;
                                }
                                $data['menuprice2_id']=$menuprice2_id;
                                $data['menu2_id']=$menuprice['menu_id'];
                                $data['price2']=$menuprice['price'];
                                $menu=$db->getRow('shop_menu',array('id'=>$menuprice['menu_id']));
                                $data['product2']=$menu['title'];
                                $data['product_img2']=$menu['img'];
                        }
                        break;
                case 4://传递
                        if(empty($topic)){
                                echo json_result(null,'9','请输入你的话题');return ;
                        }else{
                                $data['topic']=$topic;
                        }
                        break;
                case 5://上传三张图片
                        $flag=false;
                        $upload=new UpLoad();
                        $folder="upload/encouterPhoto/";
                        if (! file_exists ( $folder )) {
                                mkdir ( $folder, 0777 );
                        }
                        $upload->setDir($folder.date("Ymd")."/");
                        $upload->setPrefixName('user'.$userid);
                        $file=$upload->uploadFiles('photos');//$_File['photo'.$i]
                        if($file['status']!=0&&$file['status']!=1){
                                echo json_result(null,'701',$file['errMsg']);return;
                        }
                        if($file['status']==1){
                                $flag=true;
                        }
                        if(!$flag){
                                echo json_result(null,'11','请上传至少一张图片');return;
                        }
                        break;
                default :
                        break;
        }
        if(empty($msg)){
                echo json_result(null,'10','请输入你的寄语');return ;
        }else{
                $data['msg']=$msg;
        }
        $data['created']=date("Y-m-d H:i:s");
        $encouterid=$db->create('encouter',$data);
        //插入人物标签
        if(!empty($tag_ids)){
                $tags=explode(",", $tag_ids);
                $tgsql="";
                foreach ($tags as $tg){
                        $tgsql.=",(NULL, '".$encouterid."', '".$tg."')";
                }
                $tgsql =  substr($tgsql, 1);
                $insertTag="INSERT INTO cofe_encouter_usertag (`id`, `encouter_id`, `tag_id`) VALUES {$tgsql};";
                $db->excuteSql($insertTag);
        }
        //插入图片数据
        if($type==5&&$flag){
                foreach ($file['filepaths'] as $path){
                        $photo['img']=APP_SITE.$path;
                        $photo['user_id']=$userid;
                        $photo['encouter_id']=$encouterid;
                        $photo['created']=date("Y-m-d H:i:s");
                        $db->create('encounter_img', $photo);
                }
        }
        echo json_result(array('encouter_id'=>$encouterid));
        
}

//附近的邂逅咖啡
function nearCafe(){
        global $db;
	$lng=filter($_REQUEST['lng']);
	$lat=filter($_REQUEST['lat']);
	$city_code=filter($_REQUEST['city_code']);
	$area_id=filter($_REQUEST['area_id']);
	$circle_id=filter($_REQUEST['circel_id']);
	$tag_ids=filter($_REQUEST['tag_ids']);
        $type=filter($_REQUEST['type']);
	$page_no = isset ( $_GET ['page'] ) ? $_GET ['page'] : 1;
	$page_size = PAGE_SIZE;
	$start = ($page_no - 1) * $page_size;
	
	$sql="select encouter.id,encouter.user_id,encouter.type,user.head_photo as img "
                . "from ".DB_PREFIX."encouter encouter "
                . "left join ".DB_PREFIX."shop shop on encouter.shop_id=shop.id "
                . "left join ".DB_PREFIX."user user on encouter.user_id=user.id "
                . "left join ".DB_PREFIX."user_tag user_tag on user.id=user_tag.user_id "
                . "where encouter.status=1 or status=4 ";//1待付款2待领取3待到店领取4已领走4等候待付款5等候待到店领取6等候已领走
        if(!empty($city_code)){
                $city=$db->getRow('shop_addcity',array('code'=>$city_code));
                $sql.=(!empty($city['id']))?" and addcity_id={$city['id']} ":'';
        }
        $sql.=(!empty($area_id))?" and addarea_id={$area_id} ":'';
        $sql.=(!empty($circle_id))?" and addcircle_id={$circle_id} ":'';
        $sql.=(!empty($tag_ids))?" and user_tag.tag_id in ({$tag_ids}) ":'';
        $sql.=(!empty($type))?" and encouter.type = ({$type}) ":'';
        
        $sql.=(!empty($lng)&&!empty($lat))?" order by sqrt(power(lng-{$lng},2)+power(lat-{$lat},2)),id ":' order by id ';
	$sql .= " limit $start,$page_size";
        $sql="select * from ($sql) s group by s.user_id";
        
	$data=$db->getAllBySql($sql);
	//echo json_result(array('shops'=>$shops));
	echo json_result($data);
}

//查看邂逅咖啡信息
function cafeInfo(){
        global $db;
        $id = filter($_REQUEST['id']);
        $sql = "select encouter.id as encouter_id,encouter.type,encouter.user_id,user.head_photo,user.nick_name,encouter.shop_id,shop.title as shop_title,shop.lng,shop.lat,encouter.days,encouter.product1 as cafe1,encouter.product_img1 as cafe_img1,encouter.price1,encouter.product2 as cafe2,encouter.product_img2 as cafe_img2,encouter.price2,encouter.msg,encouter.question,encouter.topic from ".DB_PREFIX."encouter encouter "
                . "left join ".DB_PREFIX."user user on encouter.user_id=user.id "
                . "left join ".DB_PREFIX."shop shop on encouter.shop_id=shop.id "
                . "where encouter.id = {$id}";
        $data=$db->getRowBySql($sql);
        $tagsql="select tag.name from ".DB_PREFIX."encouter_usertag usertag "
                . "left join ".DB_PREFIX."base_user_tag tag on usertag.tag_id=tag.id "
                . "where usertag.encouter_id={$id}";
        $data['tags']=$db->getAllBySql($tagsql);
        $data['user_imgs']=$db->getAll('encouter_img',array('encouter_id'=>$id),array('img'));
        echo json_result($data);
}

//领取咖啡
function receive(){
       global $db;
       $userid=filter(!empty($_REQUEST['loginid'])?$_REQUEST['loginid']:'');
       $encouterid=filter(!empty($_REQUEST['encouterid'])?$_REQUEST['encouterid']:'');
       $msg=filter(!empty($_REQUEST['msg'])?$_REQUEST['msg']:'');
       $encouter=$db->getRow('encouter',array('id'=>$encouterid));
       $type=$encouter['type'];//1爱心2缘分3约会4传递5等候
       //$encouter['status'] 1待付款 2待领取 3待到店领取 4已领走 4等候待付款 5等候待到店领取 6等候已领走
       if($encouter['status']==1){
         echo json_result(null,'2','这杯咖啡正在等待付款中');
         return;
       }elseif($encouter['status']!=2){
         echo json_result(null,'3','很抱歉您晚了一步');
         return;
       }
       switch ($type){
               case 1://爱心
                       $isreceived=true;
                       $db->excuteSql("begin;");//使用事务查询状态并改变
                       $encouter=$db->getRow('encouter',array('id'=>$encouterid));
                       if($encouter['status']==2){
                               $isreceived=false;
                               $receive=array('from_user'=>$userid,'encouter_id'=>$encouterid,'type'=>$encouter['type'],'msg'=>$msg,'to_user'=>$encouter['user_id'],'verifycode'=>encouterVerify($db->getCount('encouter_receive',array('from_user'=>$userid))),'status'=>2,'created'=>date("Y-m-d H:i:s"));
                               $db->create('encouter_receive',$receive);
                               $db->update('encouter',array('status'=>3),array('id'=>$encouterid));
                       }
                       $db->excuteSql("commit;");
                       if($isreceived){
                               echo json_result(null,'3','很抱歉您晚了一步');
                               return;
                       }else{
                                //领取成功
                               if($userid!=$encouter['user_id']){
                                        $from=$db->getRow('user',array('id'=>$userid));
                                        $to=$db->getRow('user',array('id'=>$encouter['user_id']));
                                        //发送环信消息
                                        $HuanxinObj=Huanxin::getInstance();
                                        $huserObj=$HuanxinObj->sendmsgToUser($from['mobile'],$to['mobile'],'我领取了你的咖啡,很高兴认识你~');
                                        $huserObj=$HuanxinObj->sendmsgToUser($to['mobile'],$from['mobile'],'很高兴认识你~');
                               }
                               //发送短息
                               $shop=$db->getRow('shop',array('id'=>$encouter['shop_id']));
                               $sms=new Sms();
                               $sms->sendMsg("您的".$encouter['product1']."的验证码是:".$receive['verifycode'].",请尽快到<".$shop['title'].">领取!感谢是爱心的第一步~欢迎使用", $from['mobile']);
                               echo json_result(array('verifycode'=>$receive['verifycode']));
                               return;
                       }
                       break;
               case 2://缘分
                       $receive=array('from_user'=>$userid,'encouter_id'=>$encouterid,'type'=>$encouter['type'],'msg'=>$msg,'to_user'=>$encouter['user_id'],'status'=>1,'created'=>date("Y-m-d H:i:s"));
                       $db->create('encouter_receive',$receive);
//                       $IOSumeng=new Umeng('IOS');
//                       $IOSumeng->sendIOSCustomizedcast("invitation", $encouter['user_id'], '有人想领取您的咖啡,等待您的回复',array('notify'=>'encouter'));

                       echo json_result(array('success'=>'TRUE'));
                       return;
                       break;
               case 3://3约会
                        $choice_menu=filter(!empty($_REQUEST['choice_menu'])?$_REQUEST['choice_menu']:'');
                       if(empty($choice_menu)){
                              echo json_result(null,'4','请选择一杯咖啡');
                              return; 
                       }
                        $datetime=filter(!empty($_REQUEST['datetime'])?$_REQUEST['datetime']:'');
                       if(empty($datetime)){
                              echo json_result(null,'5','请选择应约时间');
                              return; 
                       }
                        $receive=array('from_user'=>$userid,'encouter_id'=>$encouterid,'type'=>$encouter['type'],'msg'=>$msg,'to_user'=>$encouter['user_id'],'status'=>1,'datetime'=>$datetime,'choice_menu'=>$choice_menu,'created'=>date("Y-m-d H:i:s"));
                        $db->create('encouter_receive',$receive);
//                       $IOSumeng=new Umeng('IOS');
//                       $IOSumeng->sendIOSCustomizedcast("invitation", $encouter['user_id'], '有人想领取您的咖啡,等待您的回复',array('notify'=>'encouter'));

                       echo json_result(array('success'=>'TRUE'));
                       return;
                       break;
               case 4://4传递
                       break;
               case 5://5等候 为Ta买单
                       break;
               default :
                       break;
       }
       $db->getLastSql();
}