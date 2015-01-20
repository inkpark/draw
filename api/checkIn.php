<?php
include_once('./configs/config.php');
include_once('./configs/err_log.php');
include_once('./includes/db.class.php');
include_once('./includes/curl.class.php');



$session_id     = $_COOKIE['session_id'];
$index   = intval($_GET['index']);
$fetchs  = array(0,1,2,3,4,5,6);
$prize   = array(
    array(
        'ticket' => 1,
        'money'  => 2000
    ),
    array(
        'ticket' => 1,
        'money'  => 3000
    ),
    array(
        'ticket' => 2,
        'money'  => 4000
    ),
    array(
        'ticket' => 1,
        'money'  => 5000
    ),
    array(
        'ticket' => 1,
        'money'  => 5000
    ),
    array(
        'ticket' => 1,
        'money'  => 5000
    ),
    array(
        'ticket' => 2,
        'money'  => 5000
    ),
);

$result = $err_log[0];



if (!$session_id) {
    $result = $err_log[1];
} elseif (!isset($_GET['index']) || !in_array($index,$fetchs)) {
    $result = $err_log[2];
} else {
    $db   = new DBSql();
    $sql  = 'SELECT uid FROM api_session WHERE session_id = "'.$session_id.'" ORDER BY dateline DESC LIMIT 1'; 
    $a = $db->select($sql);
    $uid = $a[0]['uid'];

    $dayBegin = dayBegin();

    $sql      = 'SELECT * FROM api_user WHERE uid = '.$uid;
    $info     = $db->select($sql);
    if (!$info) {
        $id = $db->insertData('api_user',array('uid' => $uid,  'dateline' => time()));
        if (!$id) {
            $result = $err_log[3];
        } else{
            $sql  = 'SELECT * FROM api_user WHERE id = '.$id;
            $info = $db->select($sql);
        }
    }
    $info    = $info[0];
    $ltimes  = $info['login_times'];
    $checked = $ltimes >= 7 ? $ltimes%7 : $ltimes;
    if ($checked  != $index) {
        $result = $err_log[2];
    } elseif ($info['last_login'] > $dayBegin) {
        $result = $err_log[4];
    } else {
        $id = $info['id'];
    }
    
}

if ($result['status'] === 0) {
    $money  = $prize[$index]['money'];
    $ticket = $prize[$index]['ticket'];
    $parms  = array(
        'uid'         => $uid,
        'ServiceCode' => ServiceCode,
        'SecretKey'   => SecretKey,
        "CurrencyID"  => 2,
        "Money"       => $money
    );
    $curl    = new Curl('');
    $request = $curl->request( ADD_MONEY_URL, 'post', $parms );
    $request = json_decode($request);
    if (!isset($request->Result)) {
        $result = $err_log[3];
    } elseif ( $request->Result != 0) {
        $result['status']  = $request->Result;
        $result['message'] = $request->Message;
    } else {
        $sql = 'UPDATE api_user SET login_times = login_times + 1, last_login = '.time().', ticket_num = ticket_num + '.$ticket.' WHERE id = '.$id;
        $db->update($sql);
    }

}


echo json_encode($result);


#########################################################################################
####################   functions
###################
##########################################################################################

function dayBegin($time = 0)
{

    if (!$time) {
        $time = time();
    }
    if (!is_numeric($time)) {
        $time = strtotime($time);
    }
    $year     = date("Y",$time);
    $month    = date("m",$time);
    $day      = date("d",$time);
    $dayBegin = mktime(0,0,0,$month,$day,$year);//当天开始时间戳
    return $dayBegin;
}
