<?php
include_once("./includes/Curlmulti.class.php");
include_once("./configs/config.php");
include_once("./includes/db.class.php");
$cm       = new CurlMulti();

// 官网直接登录
if (isset($_GET['username']) && isset($_GET['password'])) {

    $url      = "http://member.dianxinqipai.com/ClubOneApi/";
    $username = $_GET['username'];
    $password = $_GET['password'];
    $api      = 'login';
    $service  = '';
    $auto     = rand(10,1000000);
    $url      = $url."?username=".$username."&password=".$password."&api=".$api."&service=".$service."&auto=".$auto;
    $item     = array(
        'url'=>$url,
        'args' => array(
            'db' => new DBSql(),
        )
    );
    $cm->add($item,'cus_callback','error_callback');
    $cm->start();

} elseif(isset($_GET['u'])) {  // 第三方登陆
    $url = $_GET['u'];
    if (!$url) {
        
    } else {
        $item = array(
            'url' => $url
        );

        $cm->add($item,'otlogin_callback','error_callback');
        $cm->start();
    }

} else {
    $result['status'] = 403;
    $result['message'] = 'no parms';
    echo json_encode($result);
}




/**********************************************************************
 *  **********************************************
 *
 *  functions
 *
 *
 *
 *
 * ******************************************************************
 *********************************************************************/


function cus_callback($info,$args)
{
    $result = json_decode($info['content']);
    if ($result->status <= 0) {
        echo($info['content']);
    } else {
        session_start(); 
        $session_id =  session_id();
        $db = $args['db'];
        $data = array(
            'session_id' => $session_id,
            'uid' => $result->uid,
            'uname' => $result->username,
            'accesstoken' => $result->Accesstoken,
            'resurl' => $result->reurl,
            'dateline' => time()
        );
        $id = $db->insertData('api_session',$data);
        if ($id) {
            setcookie("session_id",$session_id,time()+3600*24, '/');
            setcookie("username",$result->username,time()+3600*24, '/');
            $return = array(
                'status'   => $result->status,
                'username' => $result->username
            );
        } else {
            $return = array(
                'status'   => -500,
                'username' => '网络故障稍后重试'
            );            
        }

        echo json_encode($return);
    }
}

function error_callback($error,$args)
{
    $result = array(
        'status' => -2000,
    );
    echo(json_encode($result));

}

function otlogin_callback($info, $args)
{
    echo $info['content'];
}

?>
