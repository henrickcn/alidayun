<?php
error_reporting(0);
require "lib/Alidayun.php";

$app_key = $_POST['app_key'];
$secret_key = $_POST['secret_key'];
$sms_free_sign_name = $_POST['sms_free_sign_name'];
$product = $_POST['product'];
$mobile  = $_POST['mobile'];
$sms_template_code = $_POST['sms_template_code'];
if(!$app_key || !$secret_key || !$sms_free_sign_name || !$product || !$mobile || !$sms_template_code){
	echo json_encode(array('err'=>1,'msg'=>'参数不能为空！'));
	exit();
}

$data = array(
	'app_key'    => $app_key,
	'secret_key' => $secret_key,
	'sms_free_sign_name' => $sms_free_sign_name,
	'product'    => $product,
	'mobile'     => $mobile,
	'sms_template_code' => $sms_template_code
);

$sms = new Alidayun($data);
$res = $sms->sendMsgCode();  //发送验证
echo json_encode($res);
