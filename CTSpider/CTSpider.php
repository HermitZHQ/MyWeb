<?php

//remove the first left one and first right one
function RemoveStrMark(&$str, $leftC = "\"", $rightC = "\"")
{
    $pos1 = strpos($str, $leftC);
    $str = substr($str, $pos1 + 1, strrpos($str, $rightC) - $pos1 - 1);
    return $str;
}

error_reporting(0);
header("Access-Control-Allow-Origin:*");

$queryID = 600694;

//基本url
$url = "http://www.ichangtou.com/ichangtou/greet";
//公司简介分页
$post_data_pre = "7|0|6|http://www.ichangtou.com/ichangtou/|61B460404EE22A76E213EC9F66BFBFCE|com.ichangtou.webproject.client.GreetingService|getCompanyRatios|java.lang.String/2004016611|";
$post_data_suf = "|1|2|3|4|1|5|6|";
$post_data = $post_data_pre.$queryID.$post_data_suf;

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
// post数据
curl_setopt($ch, CURLOPT_POST, 1);
// post的变量
curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: text/x-gwt-rpc; charset=UTF-8',
    'Content-Length: ' . strlen($post_data),
    'X-GWT-Permutation:E518E025620D5EA148529190B19E8E17'));
$output = curl_exec($ch);
//打印获得的数据
// print_r($output);

$response;
$response = RemoveStrMark($output, "[", "]");
$response = RemoveStrMark($response, "[", "]");
$infoArr = explode(",", $response);

if (count($infoArr) < 2) {
    echo "id invalid";
    return;
}

$id = RemoveStrMark($infoArr[1]);
$value = RemoveStrMark($infoArr[2]);
$totalValue = RemoveStrMark($infoArr[3], "\"", "(");
$pb = RemoveStrMark($infoArr[6]);

//资产负债表分页
//7|0|9|http://www.ichangtou.com/ichangtou/|61B460404EE22A76E213EC9F66BFBFCE|com.ichangtou.webproject.client.GreetingService|getGridData|java.lang.String/2004016611|I|600694|B|Q4|1|2|3|4|4|5|5|6|5|7|8|10|9|
$post_data_pre = "7|0|9|http://www.ichangtou.com/ichangtou/|61B460404EE22A76E213EC9F66BFBFCE|com.ichangtou.webproject.client.GreetingService|getGridData|java.lang.String/2004016611|I|";
$post_data_suf = "|B|Q4|1|2|3|4|4|5|5|6|5|7|8|10|9|";
$post_data = $post_data_pre.$queryID.$post_data_suf;
curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: text/x-gwt-rpc; charset=UTF-8',
    'Content-Length: ' . strlen($post_data),
    'X-GWT-Permutation:E518E025620D5EA148529190B19E8E17'));
$output = curl_exec($ch);

$response = RemoveStrMark($output, "[", "]");
$response = RemoveStrMark($response, "[", "]");
$infoArr = explode(",", $response);

$iCount = 0;
$iFormDate = 0;
$iFormType = 0;
foreach ($infoArr as $var) {
    if ($var == "\"报表日期\"") {
        $iFormDate = $iCount;
    }

    if (0 == strcmp( $var, "\"报表类型\"" )) {
        $iFormType = $iCount;
    }

    if (0 != $iFormDate && 0 != $iFormType) {
        break;
    }

    ++$iCount;
}

$yearCount = $iFormType - $iFormDate - 1;
if ($yearCount < 3) {
    echo "year count(".$yearCount.") too short...";
    return;
}

$latestYear = RemoveStrMark($infoArr[$iCount - 1], "\"", "#");
$response = $latestYear;

//综合损益表分页
//7|0|9|http://www.ichangtou.com/ichangtou/|61B460404EE22A76E213EC9F66BFBFCE|com.ichangtou.webproject.client.GreetingService|getGridData|java.lang.String/2004016611|I|600694|E|Q4|1|2|3|4|4|5|5|6|5|7|8|10|9|


//财务比率表
//7|0|9|http://www.ichangtou.com/ichangtou/|61B460404EE22A76E213EC9F66BFBFCE|com.ichangtou.webproject.client.GreetingService|getGridData|java.lang.String/2004016611|I|600694|F|Q4|1|2|3|4|4|5|5|6|5|7|8|10|9|

curl_close($ch);
echo $response;
