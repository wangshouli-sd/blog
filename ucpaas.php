<?php
if (!isset($_POST['phone'])) {
	exit;
}
$phone = $_POST['phone'];
define('BASE_URL', 'https://api.ucpaas.com/');
define('SOFT_VERSION','2014-06-30');

$accountSid = '2e24761cd9beb800c53e5bd5a2ffa7ca';
$authorToken = 'be91ff76da2879a42a483802412343b6';
//设置默认时区
date_default_timezone_set('PRC');
$timestamp = date('YmdHis');

$sigParameter = strtoupper(md5($accountSid.$authorToken.$timestamp));

$url = BASE_URL . SOFT_VERSION . '/Accounts/' . $accountSid . '/Messages/templateSMS?sig=' . $sigParameter;

$authorization = base64_encode($accountSid.':'.$timestamp);
//拼接header
$header = [
			'Accept:application/json',
			'Content-Type:application/json;charset=utf-8',
			'Authorization:'.$authorization
		];

$nums = '1234567890';
$yzm = substr(str_shuffle($nums),0,4);
$data['templateSMS'] = [
						'appId'=>'5e6cbb9fc7244006beac494b849111e5',
						'templateId'=>'153890',
						'to'=>$phone,
						'param'=>$yzm
					];

$body = json_encode($data);

//发送请求
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
curl_setopt($ch,CURLOPT_POST,1);
curl_setopt($ch,CURLOPT_POSTFIELDS,$body);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
$result = curl_exec($ch);
curl_close($ch);
//var_dump($result);
$result = json_decode($result, true);
$respCode = $result['resp']['respCode'];

if ($respCode == '000000') {
	$send = 'ok';
	//将验证码保存在session中
	session_start();
	$_SESSION['yzm'] = $yzm;
} else {
	$send = 'err';
}
echo json_encode(['send' => $send]);

















