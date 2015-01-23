<?php
$config = array(
    'ServerName' => '127.0.0.1',
    'UserName'   => 'root',
    'Password'   => '123456',
    'DBName'     => 'poker',
    'ServiceCode'   => 'xxxx',
    'SecretKey' => 'xxxxxxx',
    'ADD_MONEY_URL' => 'zzzzzzzzzzzzzz',
    'RED_MONEY_URL' => 'zzzzzzzzzzz',
);

foreach ($config as $k => $val) {
    define($k, $val);
}
?>
