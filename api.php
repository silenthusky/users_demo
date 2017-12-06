<?php
$user = array('phone'=>'13000000000', 'password'=>'123456');
$mod = isset($_GET['action'])?trim($_GET['action']):'';
if($mod != ''){
	$mod = $mod.'UCModule';
}
if(!function_exists($mod)){
	resp(0, '未知请求');
}

$input = getInput();
if($input == ''){
	resp(0, '请求格式错误');
}

$mod();

function loginUCModule(){
	global $input, $user;
	$phone = isset($input['phone'])?trim($input['phone']):'';
	$password = isset($input['password'])?trim($input['password']):'';
	if($phone === $user['phone'] && $password === $user['password']){
		$sessionid = md5(get_salt());
		session_id($sessionid);
		session_start();
		$_SESSION['phone'] = $phone;
		resp(1, '登录成功', $sessionid);
	}else{
		resp(0, '手机号或密码错误');
	}
}

function pingUCModule(){
	global $input;
	$sessionid = isset($input['sessionid'])?$input['sessionid']:'';
	if($sessionid == '' || !is_alphanum_str($sessionid)){
		resp(-2, '登录凭证无效');
	}
	session_id($sessionid);
	session_start();
	if(isset($_SESSION['phone'])){
		resp(1, '登录凭证有效', $sessionid);
	}else{
		resp(-2, '登录凭证无效');
	}
}

function logoutUCModule(){
	global $input;
	$sessionid = isset($input['sessionid'])?$input['sessionid']:'';
	if($sessionid == '' || !is_alphanum_str($sessionid)){
		resp(-2, '登录凭证无效');
	}
	session_id($sessionid);
	session_start();
	if(isset($_SESSION['phone'])){
		unset($_SESSION['phone']);
		resp(1, '注销成功', $sessionid);
	}else{
		resp(-2, '登录凭证无效');
	}
}


function getInput(){
	$data = file_get_contents("php://input");
	if(is_json($data)){
		return json_decode($data, 1);
	}else{
		return false;
	}
}

function is_alphanum_str($str) {
    if (preg_match('/^[0-9a-zA-Z]+$/i', $str)) {
        return true;
    } else {
        return false;
    }
}

function is_json($string) {
	json_decode($string, true);
	return (json_last_error() == JSON_ERROR_NONE);
}


function resp($status, $msg, $sessionid='', $data=''){
	header('Content-type: text/json');
	echo json_encode(array(
		'status' => $status,
		'msg'	=> $msg,
		'sessionid' => $sessionid,
		'data'	=> $data
	));
	exit;
}

function get_salt($length = 8) {
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $salt = '';
    for ($i = 0; $i < $length; $i ++) {
        $salt .= $chars [mt_rand(0, strlen($chars) - 1)];
    }
    return $salt;
}
