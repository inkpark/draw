<?php
include_once("./configs/config.php");
include_once("./includes/db.class.php");
include_once("./includes/curl.class.php");

$result   = array(
    'status'  => 0,
    'message' => '成功',
);

$session_id = $_COOKIE['session_id'];

if (!$session_id) {

    $result['status']  = 303;
    $result['message'] = '请登录';
    echo json_encode($result);

} else {

    $db    = new DBSql();
    $sql   = 'SELECT uid FROM api_session WHERE session_id = "'.$session_id.'" ORDER BY dateline DESC LIMIT 1'; 
    $a = $db->select($sql);
    $uid = $a[0]['uid'];

    $sql = 'SELECT ticket_num, draw_times FROM api_user where uid = '.$uid;
    $uinfo = $db->select($sql);
    
    if (!$uinfo) {
        $id = $db->insertData('api_user',array(
            'uid' => $uid,
            'dateline' => time()
        ));

        $uinfo = $db->select($sql);
        
    } 


    if($uinfo[0]['ticket_num'] <= $uinfo[0]['draw_times']) {

        $result['status'] = 701;
        $result['message'] = '奖券不足，做任务去吧';

    } else {

        $sql   = 'SELECT * FROM api_prize WHERE isbig = 0';
        $prizeArr = $db->select($sql);
        $arr   = array();
        foreach ($prizeArr as $val) {
            if ($val['max_nums'] > 0) {
                $temp = $val['max_numx'] - $val['nums'];
                if ($temp <= 0) {
                    $prizeArr[5]['p_probability'] = $prizeArr[5]['p_probability'] + $val['max_nums'];
                    continue;
                } else {
                    $prizeArr[5]['p_probability'] = $prizeArr[5]['p_probability'] + $temp;
                }
            }

            if ($val['max_nums'] > 0 && $val['nums'] >= $val['max_nums']) {
                continue;
            }
            $arr[$val['p_id']] = $val['p_probability'];
        }
        $id              = get_rand($arr);

        $prize['p_id']   = $prizeArr[$id-1]['p_id'];
        $prize['p_name'] = $prizeArr[$id-1]['p_name'];

        
        
        $is_fenfa = TRUE;
        
        //游戏加钱
/*        if ($prizeArr[$id-1]['p_type'] == 1) {
            $parms = array(
                'UID'         => $uid,
                'ServiceCode' => ServiceCode,
                'SecretKey'   => SecretKey,
                "CurrencyID"  => 2,
                "Money"       => $prizeArr[$id-1]['p_value']
            );
            // var_dump($parms);
            $curl    = new Curl('');
            $request = $curl->request( ADD_MONEY_URL, 'post', $parms );

            if (!$request) {
                $result['status']  = 500;
                $result['message'] = '网络异常 稍后重试';
                $is_fenfa          = FALSE;
            } else {
                $req = json_decode($request);
                if ($req->Result !== 0) {
                    $result['status']  = $req->Result ? $req->Result : 500;
                    $result['message'] = $req->Message;
                    $is_fenfa          = FALSE;
                }
            }
        }*/

        if ($is_fenfa) {
            $db->begintransaction();

            //更新奖品表
            $psql    = 'UPDATE api_prize SET nums = nums + 1 WHERE p_id = '.$prize['p_id'];
            $upprize = $db->update($psql);

            //更新用户表
            $usql    = 'UPDATE api_user SET draw_times = draw_times + 1 WHERE uid = '.$uid;
            $upuser  = $db->update($usql);

            //更新关联表
            $upup = $db->insertData('api_uprize',array(
                'uid'      => $uid,
                'p_id'     => $prize['p_id'],
                'dateline' => time()
            ));

            if (!$upprize || !$upuser || !$upup) {
                $result['status']  = 1000;
                $result['message'] = '数据异常稍后重试';
                $db->rollback();
                if ($prizeArr[$id-1]['p_type'] == 1) {
                    $request = $curl->request( RED_MONEY_URL, 'post', $parms );
                }
            } else {
                $result['info'] = $prize;
                $db->commit();
            }

        }


    }

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





//抽奖方法
function get_rand($proArr) { 
    $result = '';   
    //概率数组的总概率精度
    $proSum = array_sum($proArr);  
    //概率数组循环
    foreach ($proArr as $key => $proCur) { 
        $randNum = mt_rand(1, $proSum); 
        if ($randNum <= $proCur) { 
            $result = $key; 
            break; 
        } else { 
            $proSum -= $proCur; 
        } 
    } 
    unset ($proArr);  
    return $result; 
}
?>
