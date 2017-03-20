<?php
error_reporting(0); 
header("Access-Control-Allow-Origin:*");

// //http://gu.qq.com/sz000568
// $url='http://gu.qq.com/sz000568';//此处写抓取的网页的网址，我随便写的 
// $html=file_get_contents($url);
// // echo "<!--'$html'-->";

// $fileName = "testHtml.txt";
// $f = fopen($fileName, 'w');
// fwrite( $f, $html );
// fclose($f);

// $dom=new DOMDocument();
// $dom->loadHTML($html);

$ch = curl_init();
//设置选项，包括URL
//http://gu.qq.com/i/
//http://stockapp.finance.qq.com/mstats/
//http://stockpage.10jqka.com.cn/600694/
//https://www.baidu.com/s?wd=600694&rsv_spt=1&rsv_iqid=0xa06820e3000666cf&issp=1&f=8&rsv_bp=1&rsv_idx=2&ie=utf-8&rqlang=cn&tn=baiduhome_pg&rsv_enter=1&oq=%25E5%25A4%25A7%25E6%2599%25BA%25E6%2585%25A7&rsv_t=c4d61%2Fc1IBoTni49E7BlT2sI7zGHc5jyMEJ4CCCNdBX1b5TdPVPXhghJ2jW7WqqSw8%2BB&inputT=1353&rsv_pq=a5db52a8000006bf&rsv_sug3=51&rsv_sug1=35&rsv_sug7=100&rsv_sug2=0&rsv_sug4=1353
curl_setopt($ch, CURLOPT_URL, "http://www.baidu.com/s?wd=600694");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HEADER, 0);
//执行并获取HTML文档内容
$output = curl_exec($ch);
//释放curl句柄
curl_close($ch);
//打印获得的数据
print_r($output);

?>