<?php
/**
 * 阿里大鱼短信接口类
 * Created by Wepartner Team.
 * User: Henrick
 * Date: 2016/04/28
 * Time: 14:42
 */
class Alidayun{

    /**
     * 请求地址
     * @var array
     */
    private $request_url = array(
        'OnLine' => "http://gw.api.taobao.com/router/rest",
        'Dev'    => "http://gw.api.tbsandbox.com/router/rest"
    );

    /**
     * 由TOP分配
     * @var array
     */
    private $config = array(
        'app_key'    => '',
        'secret_key' => '',
        'sms_free_sign_name' => '',
        'code'       => '',
        'product'    => '众猎在线',
        'sms_template_code' => '',
        'mobile'     => ''
    );

    /**
     * API接口名称
     * @var string
     */
    private $method = "alibaba.aliqin.fc.sms.num.send";

    /**
     * 当前请求地址
     * @var string
     */
    private $url = '';

    /**
     * 初始化
     */
    public  function __construct($config=array(),$dev=false){
        foreach($config as $key => $val){
            $this->config[$key] = $val;
        }

        if($dev===true){
            $this->url = $this->request_url['Dev'];
        }else{
            $this->url = $this->request_url['OnLine'];
        }
    }

    /**
     * 发送短信验证码
     */
    public function sendMsgCode(){
        $time = date("Y-m-d H:i:s");
        $data = array(
            'method'      => $this->method,
            'app_key'     => $this->config['app_key'],
            'timestamp'   => $time,
            'format'      => 'json',
            'v'           => '2.0',
            'sign_method' => 'md5',
            'extend'      => $this->config['mobile'],
            'sms_type'    => 'normal',
            'sms_free_sign_name' => $this->config['sms_free_sign_name'],
            'sms_param'   => $this->_smsParam(),
            'rec_num'     => $this->config['mobile'],
            'sms_template_code' => $this->config['sms_template_code']

        );
        $data['sign'] = $this->_signStr($data);

        //测试添加，
        //$res = '{"alibaba_aliqin_fc_sms_num_send_response":{"result":{"err_code":"0","model":"101420518311^1101941810538","success":true},"request_id":"z2by26p00eji"}}';

        $url = $this->url;
        $res = $this->_curlPost($url,$data);
        $res = $res ? $res:false;
        if($res===false){
            Log::sysLog("短信接口：请求失败，短信平台可能网络超时");
            return array('err'=>1,'msg'=>'请求失败，短信平台可能网络超时');
        }

        $res = json_decode($res,true);
        if(isset($res['alibaba_aliqin_fc_sms_num_send_response']['result']['err_code']) && $res['alibaba_aliqin_fc_sms_num_send_response']['result']['err_code']==0){
            $data['code'] = $this->config['code'];
            return array('err'=>0,'msg'=>'短信发送成功！',"data"=>$data);
        }
        Log::sysLog("短信接口：返回错误信息".var_export($res,true));
        return array('err'=>1,'msg'=>$res['error_response']['code']."，错误信息为：".$res['error_response']['msg'].$res['error_response']['sub_code']);

    }

    /**
     *生成签名
     */
    protected function _signStr($data){
        ksort($data);
        $signStr = "";
        foreach($data as $key=>$val){
            $signStr .= $key.$val;
        }
        unset($key,$val,$data);
        $signStr = strtoupper(md5($this->config['secret_key'].$signStr.$this->config['secret_key']));
        return $signStr;
    }

    /**
     *短信内容参数
     */
    protected function _smsParam(){
        if(!$this->config['code']){
            $this->_createNumber();
        }

        $sms_str = json_encode(array(
            'code'    => (string)$this->config['code'],
            'product' => $this->config['product']
        ),JSON_UNESCAPED_UNICODE);
        return $sms_str;
    }

    /**
     * 生成随机数字验证码
     */
    protected function _createNumber(){
        $code = rand(100000,999999);
        $this->config['code'] = "{$code}";
    }
	
	//通过curl post数据
    protected function _curlPost( $url, array $post_data, $header = array() ) {

        $ch = curl_init( $url );
        curl_setopt( $ch, CURLOPT_POST, TRUE );
        curl_setopt( $ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $post_data );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, TRUE );
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array ( $header ) );//模拟的header头
        $result = curl_exec( $ch );
        curl_close( $ch );
        return $result;
    }
}