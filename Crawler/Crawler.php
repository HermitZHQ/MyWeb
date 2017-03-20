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
curl_setopt($ch, CURLOPT_URL, "http://stockpage.10jqka.com.cn/600694/");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HEADER, 0);
//执行并获取HTML文档内容
$output = curl_exec($ch);
//释放curl句柄
curl_close($ch);
//打印获得的数据
print_r($output);

?>