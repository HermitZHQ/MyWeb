<?php
error_reporting(0);
header("Access-Control-Allow-Origin:*");

$response = "";
$idArr = $_POST["ids"];
$count = count($idArr);
if ($count == 0) {
    $response = "null ids...";
    echo $response;
    return;
}

$ch = curl_init();
//设置选项，包括URL
//http://qt.gtimg.cn/q=sh600694
//http://qt.gtimg.cn/q=s_sh600694,s_sh601988,s_sz000701,s_sh600012,s_sh600028,s_sh600694,s_sh601717,s_sz000002
$queryUrl = "http://qt.gtimg.cn/q=";
if ($count > 1) {
    for ($i = 0; $i < $count; ++$i) {
        $queryUrl = $queryUrl."s_sh".$idArr[$i].",";
    }
} else {
    $queryUrl = $queryUrl."s_sh".$idArr.",";
}

curl_setopt($ch, CURLOPT_URL, $queryUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HEADER, 0);

//执行并获取HTML文档内容
$output = curl_exec($ch);
//打印获得的数据
// print_r($output);

//释放curl句柄
curl_close($ch);

$jsonArr = array();

//handle the data we got
$infoArr = explode(";", $output);
$count = count($infoArr);
if ($count > 0) {
    for ($i = 0; $i < $count; ++$i) {
        $infoArr[$i] = mb_convert_encoding($infoArr[$i], "UTF-8", "GB2312");
        $dataArr = explode("~", $infoArr[$i]);
        if (count($dataArr) > 3) {
            //index:1-name 3-latest value
            // $response = $response.$dataArr[1].":".$dataArr[3]." ";

            $jsonArr['name'] = $dataArr[1];
            $jsonArr['id'] = $dataArr[2];
            $jsonArr['value'] = $dataArr[3];

            $response = json_encode($jsonArr);
            echo $response;
            return;
        } else {
            $response = $infoArr[$i];
            echo $response;
            return;
        }
    }
}

// $response = $queryUrl;
$response = "empty...";
// $response = mb_convert_encoding($response, "UTF-8", "GB2312");
echo $response;

?>