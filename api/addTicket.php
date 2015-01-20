<?php
include_once('./configs/config.php');
include_once('./includes/db.class.php');
include_once('./includes/DES.class.php');

$des    = new DES();

$result = array(
    'status'  => 0,
    'message' => '加券成功'
);

$uidStr   = $des->decrypt($_POST['uid']);
$dateline = $des->decrypt($_POST['dl']);
$tnums    = intval($_POST['tnums']);
$uArray   = explode('_',$uidStr);

if (!$uidStr || !$dateline || !$tnums) {

    $result['status']  = 404;
    $result['message'] = '参数不全';

} elseif ($dateline != end($uArray) || $tnums <= 0) {

    $result['status']  = 403;
    $result['message'] = '验证未通过';

} else {

    $uid      = $uArray[0];

    $db       = new DBSql();

    $oldidsql = 'SELECT uid FROM api_user WHERE uid = '.$uid;
    $olduid   = $db->select($oldidsql);

    if ($olduid) {

        $sql    = 'UPDATE api_user SET ticket_num = ticket_num + '.$tnums.' WHERE uid = '.$uid;
        $db_res = $db->update($sql);
    } else {

        $data = array(
            'uid'        => $uid,
            'ticket_num' => $tnums,
            'dateline'   => time()
        );
        $db_res = $db->insertData('api_user',$data);

    }
    if (!$db_res) {

        $result['status']  = 500;
        $result['message'] = '网络故障，稍后重试';
    }

}

echo json_encode($result);

?>
