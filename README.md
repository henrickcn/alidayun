# alidayun
阿里大鱼短信接口

配置参数：
app_key    ：所申请应用的app_key,
secret_key ：所申请应用的secret_key,
sms_free_sign_name : 阿里大鱼短信标识,
sms_template_code  : 阿里大鱼短信模块Id,
product    ：产品名称（如：淘宝）,
mobile     ：接收短信验证码的手机号码,
code       ：短信验证码（默认为6位随机数字）

使用方法：

$sms = new Alidayun($config);

$sms -> sendMsgCode();  //发送短信验证码

返回成功失败状态，err=1时，表示失败，其它则为成功，成功后会返回相应的数据，开发者根据自行业务缓存返回code值，当用户输入验证码后，直接与缓存的code进行比较。


