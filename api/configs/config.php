<?php
$config = array(
    'ServerName' => '127.0.0.1',
    'UserName'   => 'root',
    'Password'   => '123456',
    'DBName'     => 'poker',
    'ServiceCode'   => 'xxxx',
    'SecretKey' => 'xxxxxxx',
    'ADD_MONEY_URL' => 'http://yandui-lb2.chinacloudapp.cn/ClubOnemoney/api/MemberWallet/AddMoneyInterface',
    'RED_MONEY_URL' => 'http://yandui-lb2.chinacloudapp.cn/ClubOnemoney/api/MemberWallet/ReduceMoneyInterface',
);

foreach ($config as $k => $val) {
    define($k, $val);
}
?>
