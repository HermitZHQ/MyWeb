<?php

//PHP会出现不规范提示，故此设置
error_reporting(0); 
//记得将Pear目录加入环境，才能这样引用，否则要引用绝对路径  
require_once "Mail.php";

$from = "cdzhanghongquan@163.com";
$to = "53009792@qq.com";
$subject = "Hi!";
$body = "Hi,\n\nHow are you?";

//SMTP服务器，不同的邮箱服务器和端口不同，网上可以查到
$host = "smtp.163.com";
$port = "25";
$username = "cdzhanghongquan@163.com";
$password = "Zhq&Xi000136";

//邮件头
$headers = array ('From' => $from,  
    'To' => $to,  
    'Subject' => $subject);

//服务设置
$smtp = Mail::factory('smtp',  
    array ('host' => $host,  
    'port' => $port,  
    'auth' => true,  
    'username' => $username,  
    'password' => $password));
        
//发送邮件  
$mail = $smtp->send($to, $headers, $body);  
    
//错误处理  
if (PEAR::isError($mail))   
{  
    echo("<p>". $mail->getMessage() ."</p>");  
}   
else   
{  
    echo("<p>Message successfully sent!</p>");  
} 

?>