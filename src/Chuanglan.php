<?php
namespace Farmer\Sms;

/* *
 * 类名：ChuanglanSmsApi
 * 功能：创蓝接口请求类
 * 详细：构造创蓝短信接口请求，获取远程HTTP数据
 */
class Chuanglan{

	var $api_account;  
    var $api_password;  
    var $api_send_url ;  //送短信接口URL
    var $API_VARIABLE_URL; //变量短信接口URL
    var $api_balance_query_url; //短信余额查询接口URL
    function __construct($api_account,$api_password,$api_send_url = 'http://vsms.253.com/msg/send/json',$API_VARIABLE_URL = 'http://vsms.253.com/msg/variable/json',$api_balance_query_url = 'http://vsms.253.com/msg/balance/json'){  
        $this->api_account = $api_account;  
        $this->api_password = $api_password;  
        $this->api_send_url = $api_send_url;  
        $this->API_VARIABLE_URL = $API_VARIABLE_URL;  
        $this->api_balance_query_url = $api_balance_query_url;
     }
	/**
	 * 发送短信
	 *
	 * @param string $mobile 		手机号码
	 * @param string $msg 			短信内容
	 * @param string $needstatus 	是否需要状态报告
	 */
	public function sendSMS( $mobile, $msg, $needstatus = 'true') {
		//创蓝接口参数
		$postArr = array (
			'account'  =>  $this->api_account,
			'password' => $this->api_password,
			'msg' => urlencode($msg),
			'phone' => $mobile,
			'report' => $needstatus
        );
		
		$result = $this->curlPost( $this->api_send_url , $postArr);
		return $result;
	}
	
	/**
	 * 发送变量短信
	 *
	 * @param string $msg 			短信内容
	 * @param string $params 	最多不能超过1000个参数组
	 */
	public function sendVariableSMS( $msg, $params) {
		global $chuanglan_config;
		
		//创蓝接口参数
		$postArr = array (
			'account' => $this->api_account,
			'password' =>$this->api_password,
			'msg' => $msg,
			'params' => $params,
			'report' => 'true'
        );
		
		$result = $this->curlPost( $this->API_VARIABLE_URL, $postArr);
		return $result;
	}
	
	
	/**
	 * 查询额度
	 *
	 *  查询地址
	 */
	public function queryBalance() {

		//查询参数
		$postArr = array ( 
		    'account' => $this->api_account,
			'password' =>$this->api_password,
		);
		$result = $this->curlPost($this->api_balance_query_url, $postArr);
		return $result;
	}

	/**
	 * 通过CURL发送HTTP请求
	 * @param string $url  //请求URL
	 * @param array $postFields //请求参数 
	 * @return mixed
	 */
	private function curlPost($url,$postFields){
		$postFields = json_encode($postFields);
		$ch = curl_init ();
		curl_setopt( $ch, CURLOPT_URL, $url ); 
		curl_setopt( $ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json; charset=utf-8'
			)
		);
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt( $ch, CURLOPT_POST, 1 );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $postFields);
        curl_setopt( $ch, CURLOPT_TIMEOUT,1); 
        curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, 0);
		$ret = curl_exec ( $ch );
        if (false == $ret) {
            $result = curl_error(  $ch);
        } else {
            $rsp = curl_getinfo( $ch, CURLINFO_HTTP_CODE);
            if (200 != $rsp) {
                $result = "请求状态 ". $rsp . " " . curl_error($ch);
            } else {
                $result = $ret;
            }
        }
		curl_close ( $ch );
		return $result;
	}
	
}