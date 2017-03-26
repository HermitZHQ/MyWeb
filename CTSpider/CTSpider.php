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
$iFormDateCount = 0;
$iFormTypeCount = 0;
foreach ($infoArr as $var) {
    if ($var == "\"报表日期\"") {
        $iFormDateCount = $iCount;
    }

    if (0 == strcmp( $var, "\"报表类型\"" )) {
        $iFormTypeCount = $iCount;
    }

    if (0 != $iFormDateCount && 0 != $iFormTypeCount) {
        break;
    }

    ++$iCount;
}

$yearCount = $iFormTypeCount - $iFormDateCount - 1;
if ($yearCount < 3) {
    echo "year count(".$yearCount.") too short...";
    return;
}

$latestYear = RemoveStrMark($infoArr[$iCount - 1], "\"", "#");

$iCount = 0;
//货币资金及金融资产
$cash1 = 0;
$cash2 = 0;
$cash = 0;
$iCash1TitleCount = 0;
$iCash1NextTitleCount = 0;
$iCash2TitleCount = 0;
$iCash2NextTitleCount = 0;
$bCash1TitleFlag = false;
$bCash1NextTitleFlag = false;
$bCash2TitleFlag = false;
$bCash2NextTitleFlag = false;
//流动资产合计
$currentAssets = 0;
$iCurrentAssetsTitleCount = 0;
$bCurrentAssetsTitleFlag = false;
//固定资产由三部分组成（投资性房地产，固定资产，在建工程），由于这三个是列表中连续的，所以只用多一个第三部分的next
$fixedAssets = 0;
$fixedAssets1 = 0;
$fixedAssets2 = 0;
$fixedAssets3 = 0;
$iFixedAssets1TitleCount = 0;
$iFixedAssets2TitleCount = 0;
$iFixedAssets3TitleCount = 0;
$iFixedAssets3NextTitleCount = 0;
$bFixedAssets1TitleFlag = false;
$bFixedAssets2TitleFlag = false;
$bFixedAssets3TitleFlag = false;
$bFixedAssets3NextTitleFlag = false;
//短期借款
$shortLoan = 0;
$iShortLoanTitleCount = 0;
$iShortLoanNextTitleCount = 0;
$bShortLoanTitleFlag = false;
$bShortLoanNextTitleFlag = false;
//长期借款+应付债券（由于两个连一起的，应付债券就不另外起名字了）
$longLoan1 = 0;
$longLoan2 = 0;
$iLongLoan1TitleCount = 0;
$iLongLoan2TitleCount = 0;
$iLongLoan2NextTitleCount = 0;
$bLongLoan1TitleFlag = false;
$bLongLoan2TitleFlag = false;
$bLongLoan2NextTitleFlag = false;


//下面的循环整体按照长投表格从上到下进行排列，由于数据中连续的0值不记录（有些又是记录最后一个0），导致我处理起来很麻烦
foreach ($infoArr as $var) {
    if (!$bCash1TitleFlag && $var == "\"货币资金\"") {
        $iCash1TitleCount = $iCount;
        $bCash1TitleFlag = true;
    }

    if (!$bCash1NextTitleFlag && $var == "\"交易性金融资产\"") {
        $iCash1NextTitleCount = $iCount;
        $bCash1NextTitleFlag = true;

        $iTmp = $iCash1NextTitleCount - $iCash1TitleCount - 1;
        $cash1 = ( $iTmp == 0 ) ? 0 : $infoArr[$iCash1TitleCount + $iTmp];
        $cash1 = RemoveStrMark($cash1);
    }

    if (!$bCurrentAssetsTitleFlag && $var == "\"流动资产合计\"") {
        $iCurrentAssetsTitleCount = $iCount;
        $bCurrentAssetsTitleFlag = true;
    }

    if (!$bCash2TitleFlag && $var == "\"可供出售金融资产\"") {
        $iCash2TitleCount = $iCount;
        $bCash2TitleFlag = true;

        $iTmp = $iCash2TitleCount - $iCurrentAssetsTitleCount - 1;
        $currentAssets = ( $iTmp == 0 ? 0 : $infoArr[$iCurrentAssetsTitleCount+$iTmp]);
        $currentAssets = RemoveStrMark($currentAssets);
    }

    if (!$bCash2NextTitleFlag && $var == "\"持有至到期投资\"") {
        $iCash2NextTitleCount = $iCount;
        $bCash2NextTitleFlag = true;

        $iTmp = $iCash2NextTitleCount - $iCash2TitleCount - 1;
        $cash2 = ( $iTmp == 0 ) ? 0 : $infoArr[$iCash2TitleCount + $iTmp];
        $cash2 = RemoveStrMark($cash2);
    }

    if (!$bFixedAssets1TitleFlag && $var == "\"投资性房地产\"") {
        $iFixedAssets1TitleCount = $iCount;
        $bFixedAssets1TitleFlag = true;
    }

    if (!$bFixedAssets2TitleFlag && $var == "\"固定资产\"") {
        $iFixedAssets2TitleCount = $iCount;
        $bFixedAssets2TitleFlag = true;

        $iTmp = $iFixedAssets2TitleCount - $iFixedAssets1TitleCount - 1;
        $fixedAssets1 = ( $iTmp == 0 ) ? 0 : $infoArr[$iFixedAssets1TitleCount + $iTmp];
        $fixedAssets1 = RemoveStrMark($fixedAssets1);
    }

    if (!$bFixedAssets3TitleFlag && $var == "\"在建工程\"") {
        $iFixedAssets3TitleCount = $iCount;
        $bFixedAssets3TitleFlag = true;

        $iTmp = $iFixedAssets3TitleCount - $iFixedAssets2TitleCount - 1;
        $fixedAssets2 = ( $iTmp == 0 ) ? 0 : $infoArr[$iFixedAssets2TitleCount + $iTmp];
        $fixedAssets2 = RemoveStrMark($fixedAssets2);
    }

    if (!$bFixedAssets3NextTitleFlag && $var == "\"工程物资\"") {
        $iFixedAssets3NextTitleCount = $iCount;
        $bFixedAssets3NextTitleFlag = true;

        $iTmp = $iFixedAssets3NextTitleCount - $iFixedAssets3TitleCount - 1;
        $fixedAssets3 = ( $iTmp == 0 ) ? 0 : $infoArr[$iFixedAssets3TitleCount + $iTmp];
        $fixedAssets3 = RemoveStrMark($fixedAssets3);
    }

    if (!$bShortLoanTitleFlag && $var == "\"短期借款\"") {
        $iShortLoanTitleCount = $iCount;
        $bShortLoanTitleFlag = true;
    }

    if (!$bShortLoanNextTitleFlag && $var == "\"交易性金融负债\"") {
        $iShortLoanNextTitleCount = $iCount;
        $bShortLoanNextTitleFlag = true;

        $iTmp = $iShortLoanNextTitleCount - $iShortLoanTitleCount - 1;
        $shortLoan = ( $iTmp == 0 ) ? 0 : $infoArr[$iShortLoanTitleCount + $iTmp];
        $shortLoan = RemoveStrMark($shortLoan);
    }

    if (!$bLongLoan1TitleFlag && $var == "\"长期借款\"") {
        $iLongLoan1TitleCount = $iCount;
        $bLongLoan1TitleFlag = true;
    }

    if (!$bLongLoan2TitleFlag && $var == "\"应付债券\"") {
        $iLongLoan2TitleCount = $iCount;
        $bLongLoan2TitleFlag = true;

        $iTmp = $iLongLoan2TitleCount - $iLongLoan1TitleCount - 1;
        $longLoan1 = ( $iTmp == 0 ) ? 0 : $infoArr[$iLongLoan1TitleCount + $iTmp];
        $longLoan1 = RemoveStrMark($longLoan1);
    }

    if (!$bLongLoan2NextTitleFlag && $var == "\"长期应付款\"") {
        $iLongLoan2NextTitleCount = $iCount;
        $bLongLoan2NextTitleFlag = true;

        echo $infoArr[$iLongLoan2TitleCount+2];
        return;

        $iTmp = $iLongLoan2NextTitleCount - $iLongLoan2TitleCount - 1;
        $longLoan2 = ( $iTmp == 0 ) ? 0 : $infoArr[$iLongLoan2TitleCount + $iTmp];
        $longLoan2 = RemoveStrMark($longLoan2);
    }

    if ($bLongLoan2NextTitleFlag) {
        break;
    }

    ++$iCount;
}

$response = $longLoan2;

//综合损益表分页
//7|0|9|http://www.ichangtou.com/ichangtou/|61B460404EE22A76E213EC9F66BFBFCE|com.ichangtou.webproject.client.GreetingService|getGridData|java.lang.String/2004016611|I|600694|E|Q4|1|2|3|4|4|5|5|6|5|7|8|10|9|


//财务比率表
//7|0|9|http://www.ichangtou.com/ichangtou/|61B460404EE22A76E213EC9F66BFBFCE|com.ichangtou.webproject.client.GreetingService|getGridData|java.lang.String/2004016611|I|600694|F|Q4|1|2|3|4|4|5|5|6|5|7|8|10|9|

curl_close($ch);
echo $response;
