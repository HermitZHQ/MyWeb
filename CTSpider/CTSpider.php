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

$bEnableDebugInfo = true;
$queryID = "000948";

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

if ($bEnableDebugInfo) {
    echo "id:$id, value:$value, totalValue:$totalValue, pb:$pb\n";
}
// return;

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
$dataArr = explode(",", $response);
$response = RemoveStrMark($response, "[", "]");
$infoArr = explode(",", $response);

$iCount = 0;
$iFormDateCount = 0;
$iFormTypeCount = 0;
$iTradeAssetsCount = 0;
$iFirstZeroCount = 0;
foreach ($infoArr as $var) {
    if ($var == "\"报表日期\"") {
        $iFormDateCount = $iCount;
    }

    if (0 == strcmp( $var, "\"报表类型\"" )) {
        $iFormTypeCount = $iCount;
    }

    if (0 == strcmp( $var, "\"交易性金融资产\"" )) {
        $iTradeAssetsCount = $iCount;
    }

    if (0 == strcmp( $var, "\"0\"" )) {
        $iFirstZeroCount = $iCount + 1;
    }

    if (0 != $iFirstZeroCount) {
        break;
    }

    ++$iCount;
}

if ($bEnableDebugInfo) {
    echo "first zero count:$iFirstZeroCount\n";
}

if (0 == $iTradeAssetsCount) {
    echo "couldn't find trade assets, type error, maybe it's a bank stock...";
    return;
}

$yearCount = $iFormTypeCount - $iFormDateCount - 1;
if ($yearCount < 3) {
    echo "year count(".$yearCount.") too short...";
    return;
}

$startDataIndex = count($dataArr) - count($infoArr) - 5;
$checkYearCount = $dataArr[$startDataIndex - 1] - 1;
if ($checkYearCount != $yearCount) {
    echo "verify year count failed, dataArr-yearCount:$checkYearCount, yearCount:$yearCount";
    return;
}

$latestYear = RemoveStrMark($infoArr[$iFormTypeCount - 1], "\"", "#");

if ($bEnableDebugInfo) {
    echo "latestYear:$latestYear, yearCount:$yearCount\n";
}
// return;

$iCount = 0;
//货币资金及金融资产
$cash1 = 0;
$cash2 = 0;
$cash = 0;
$Cash1Row = 3;
$Cash2Row = 18;
$iCash1NextTitleCount = 0;
$iCash2NextTitleCount = 0;
$bCash1NextTitleFlag = false;
$bCash2NextTitleFlag = false;
//流动资产合计
$currentAssets = 0;
$currentAssetsRow = 17;
$iCurrentAssetsNextTitleCount = 0;
$bCurrentAssetsNextTitleFlag = false;
//固定资产由三部分组成（投资性房地产，固定资产，在建工程），由于这三个是列表中连续的，所以只用多一个第三部分的next
$fixedAssets = 0;
$fixedAssets1 = 0;
$fixedAssets2 = 0;
$fixedAssets3 = 0;
$fixedAssets1Row = 22;
$fixedAssets2Row = 23;
$fixedAssets3Row = 24;
$iFixedAssets1NextTitleCount = 0;
$iFixedAssets2NextTitleCount = 0;
$iFixedAssets3NextTitleCount = 0;
$bFixedAssets1NextTitleFlag = false;
$bFixedAssets2NextTitleFlag = false;
$bFixedAssets3NextTitleFlag = false;
//短期借款
$shortLoan = 0;
$shortLoanRow = 38;
$iShortLoanNextTitleCount = 0;
$bShortLoanNextTitleFlag = false;
//长期借款
$longLoan = 0;
$longLoanRow = 55;
$iLongLoanNextTitleCount = 0;
$bLongLoanNextTitleFlag = false;
//应付债券
$bondsPayable = 0;
$bondsPayableRow = 56;
$ibondsPayableNextTitleCount = 0;
$bbondsPayableNextTitleFlag = false;


//下面的循环整体按照长投表格从上到下进行排列，由于数据中连续的0值不记录（有些又是记录最后一个0），导致我处理起来很麻烦
//0的问题经过数据逆向分析后已经解决了，所以改变策略，不需要读取下个标题，然后减去和本标题的差，来算有几个数据了，这种算也算不正确
//但是我们需要使用固定的标题行数，这个模式是固定的，除了银行股的标题排列不一样，多数的一般股的标题行都是一样的（这就是我要找的固定的模式）
//使用标题的行数，是为了从外部的数据数组（dataArr）中取得该行的正确的数据排列，这样我们就能准确的拿到最后一年的数据，而不是像我开始想的这种的办法，去算n年的平均值
//由于数据数组中并没有实际的数据，只是记录数据的下标（可判断是否重复），如果最后一年数据为0，我们就可以直接使用0就好
//但如果不为0的话，我们需要使用该标题的下一个标题的位置-1，来获取最后一年的数据
foreach ($infoArr as $var) {
    if (!$bCash1NextTitleFlag && $var == "\"交易性金融资产\"") {
        $iCash1NextTitleCount = $iCount;
        $bCash1NextTitleFlag = true;
        
        //由于数据是逆向排列的，所以这里是减回去
        $dataIndex = $startDataIndex - (2+$yearCount+1)*($Cash1Row-1);
        //注意这里也是减，开始写成+号出bug了，很容易忘记是逆向的
        if ($dataArr[$dataIndex-$yearCount-2] == $iFirstZeroCount) {
            $cash1 = 0;
        } else {
            $tmp = $infoArr[$iCash1NextTitleCount-1];
            $cash1 = floatval(RemoveStrMark($tmp));
            // echo "cash1:$cash1";
        }
    }

    if (!$bCash2NextTitleFlag && $var == "\"持有至到期投资\"") {
        $iCash2NextTitleCount = $iCount;
        $bCash2NextTitleFlag = true;

        $dataIndex = $startDataIndex - (2+$yearCount+1)*($Cash2Row-1);
        if ($dataArr[$dataIndex-$yearCount-2] == $iFirstZeroCount) {
            $cash2 = 0;
        } else {
            $tmp = $infoArr[$iCash2NextTitleCount-1];
            $cash2 = floatval(RemoveStrMark($tmp));
            // echo "cash2:$cash2";
        }
    }

    if (!$bCurrentAssetsNextTitleFlag && $var == "\"可供出售金融资产\"") {
        $iCurrentAssetsNextTitleCount = $iCount;
        $bCurrentAssetsNextTitleFlag = true;

        $dataIndex = $startDataIndex - (2+$yearCount+1)*($currentAssetsRow-1);
        if ($dataArr[$dataIndex-$yearCount-2] == $iFirstZeroCount) {
            $currentAssets = 0;
        } else {
            $tmp = $infoArr[$iCurrentAssetsNextTitleCount-1];
            $currentAssets = floatval(RemoveStrMark($tmp));
            // echo "currentAssets:$currentAssets";
        }
    }

    if (!$bFixedAssets1NextTitleFlag && $var == "\"固定资产\"") {
        $iFixedAssets1NextTitleCount = $iCount;
        $bFixedAssets1NextTitleFlag = true;

        $dataIndex = $startDataIndex - (2+$yearCount+1)*($fixedAssets1Row-1);
        if ($dataArr[$dataIndex-$yearCount-2] == $iFirstZeroCount) {
            $fixedAssets1 = 0;
        } else {
            $tmp = $infoArr[$iFixedAssets1NextTitleCount-1];
            $fixedAssets1 = floatval(RemoveStrMark($tmp));
            // echo "fixedAssets1:$fixedAssets1";
        }
    }

    if (!$bFixedAssets2NextTitleFlag && $var == "\"在建工程\"") {
        $iFixedAssets2NextTitleCount = $iCount;
        $bFixedAssets2NextTitleFlag = true;

        $dataIndex = $startDataIndex - (2+$yearCount+1)*($fixedAssets2Row-1);
        if ($dataArr[$dataIndex-$yearCount-2] == $iFirstZeroCount) {
            $fixedAssets2 = 0;
        } else {
            $tmp = $infoArr[$iFixedAssets2NextTitleCount-1];
            $fixedAssets2 = floatval(RemoveStrMark($tmp));
            // echo "fixedAssets2:$fixedAssets2";
        }
    }

    if (!$bFixedAssets3NextTitleFlag && $var == "\"工程物资\"") {
        $iFixedAssets3NextTitleCount = $iCount;
        $bFixedAssets3NextTitleFlag = true;

        $dataIndex = $startDataIndex - (2+$yearCount+1)*($fixedAssets3Row-1);
        if ($dataArr[$dataIndex-$yearCount-2] == $iFirstZeroCount) {
            $fixedAssets3 = 0;
        } else {
            $tmp = $infoArr[$iFixedAssets3NextTitleCount-1];
            $fixedAssets3 = floatval(RemoveStrMark($tmp));
            // echo "fixedAssets3:$fixedAssets3";
        }
    }

    if (!$bShortLoanNextTitleFlag && $var == "\"交易性金融负债\"") {
        $iShortLoanNextTitleCount = $iCount;
        $bShortLoanNextTitleFlag = true;

        $dataIndex = $startDataIndex - (2+$yearCount+1)*($shortLoanRow-1);
        if ($dataArr[$dataIndex-$yearCount-2] == $iFirstZeroCount) {
            $shortLoan = 0;
        } else {
            $tmp = $infoArr[$iShortLoanNextTitleCount-1];
            $shortLoan = floatval(RemoveStrMark($tmp));
            // echo "shortLoan:$shortLoan";
        }
    }

    if (!$bLongLoanNextTitleFlag && $var == "\"应付债券\"") {
        $iLongLoanNextTitleCount = $iCount;
        $bLongLoanNextTitleFlag = true;

        $dataIndex = $startDataIndex - (2+$yearCount+1)*($longLoanRow-1);
        if ($dataArr[$dataIndex-$yearCount-2] == $iFirstZeroCount) {
            $longLoan = 0;
        } else {
            $tmp = $infoArr[$iLongLoanNextTitleCount-1];
            $longLoan = floatval(RemoveStrMark($tmp));
            // echo "longLoan:$longLoan";
        }
    }

    if (!$bbondsPayableNextTitleFlag && $var == "\"长期应付款\"") {
        $ibondsPayableNextTitleCount = $iCount;
        $bbondsPayableNextTitleFlag = true;

        $dataIndex = $startDataIndex - (2+$yearCount+1)*($bondsPayableRow-1);
        if ($dataArr[$dataIndex-$yearCount-2] == $iFirstZeroCount) {
            $bondsPayable = 0;
        } else {
            $tmp = $infoArr[$ibondsPayableNextTitleCount-1];
            $bondsPayable = floatval(RemoveStrMark($tmp));
            // echo "bondsPayable:$bondsPayable";
        }
    }

    if ($bbondsPayableNextTitleFlag) {
        break;
    }

    ++$iCount;
}

$fixedAssets = $fixedAssets1+$fixedAssets2+$fixedAssets3;
$cash = $cash1+$cash2;
if ($bEnableDebugInfo) {
    echo "Cash:".($cash)." CurrentAssets:".$currentAssets." FixedAssets:".($fixedAssets).
    " ShortLoan:".$shortLoan." LongLoan:".$longLoan." BondsPayable:".$bondsPayable."\n";
}
// $response = $longLoan2;

//综合损益表分页
//7|0|9|http://www.ichangtou.com/ichangtou/|61B460404EE22A76E213EC9F66BFBFCE|com.ichangtou.webproject.client.GreetingService|getGridData|java.lang.String/2004016611|I|600694|E|Q4|1|2|3|4|4|5|5|6|5|7|8|10|9|
$post_data_suf = "|E|Q4|1|2|3|4|4|5|5|6|5|7|8|10|9|";
$post_data = $post_data_pre.$queryID.$post_data_suf;
curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: text/x-gwt-rpc; charset=UTF-8',
    'Content-Length: ' . strlen($post_data),
    'X-GWT-Permutation:E518E025620D5EA148529190B19E8E17'));
$output = curl_exec($ch);

$response = RemoveStrMark($output, "[", "]");
$dataArr = explode(",", $response);
$response = RemoveStrMark($response, "[", "]");
$infoArr = explode(",", $response);

$iCount = 0;
$iFirstZeroCount = 0;
foreach ($infoArr as $var) {
    if (0 == strcmp( $var, "\"0\"" )) {
        $iFirstZeroCount = $iCount + 1;
    }

    if (0 != $iFirstZeroCount) {
        break;
    }

    ++$iCount;
}

//那么这里说一个特殊情况，其实处理的复杂度又比我预料的高出了一节，还好综合损益表中，我需要的几个数据都不存在这种情况
//但是我这里还是得记录一下，免得以后出现了不知道怎么回事
//情况是这样，我发现，表中会存在两行数据完全相同的情况，而且基本是肯定出现，出现的两行分别是 总营业收入和营业收入
//为什么出现这个情况，是因为总营业收入=营业收入+其他营业收入，但是很多公司都没有其他营业收入，所以这两行完全是一样的
//那么完全一样的话，如果我要找的正好是第二行的数据，它在infoArr里就完全是空白的，因为和0一样，重复数据不再记录在infoArr里
//但是后来我又想了下，首先我要的数据没有这种情况，其次是真有这种情况出现的话，我也可以读第一行的（就是记录了的那一行，也可以说是上面那行）
//总体对我的处理影响不大

//再说这里的处理，因为年份上面的表已经有了，这里不可能变化，所以直接找到标题按照年份的个数读取就ok了
$financialCostArr = array();
$financialCostFlag = false;
$totalProfitArr = array();
$totalProfitFlag = false;
$retainedProfitArr = array();
$retainedProfitFlag = false;
$ebitArr = array();

$iCount = 0;
foreach ($infoArr as $var) {
    if (!$financialCostFlag && $var == "\"财务费用\"") {
        $financialCostFlag = true;
        for ($i=0; $i < $yearCount; $i++) {
            $tmp = $infoArr[$iCount+1+$i];
            $tmp = floatval(RemoveStrMark($tmp));
            $financialCostArr[] = $tmp;
        }
    }

    if (!$totalProfitFlag && $var == "\"利润总额\"") {
        $totalProfitFlag = true;
        for ($i=0; $i < $yearCount; $i++) {
            $tmp = $infoArr[$iCount+1+$i];
            $tmp = floatval(RemoveStrMark($tmp));
            $totalProfitArr[] = $tmp;
        }
    }

    if (!$retainedProfitFlag && $var == "\"净利润\"") {
        $retainedProfitFlag = true;
        for ($i=0; $i < $yearCount; $i++) {
            $tmp = $infoArr[$iCount+1+$i];
            $tmp = floatval(RemoveStrMark($tmp));
            $retainedProfitArr[] = $tmp;
        }
    }

    ++$iCount;
}

for ($i=0; $i < $yearCount; $i++) { 
    $ebitArr[$i] = $totalProfitArr[$i] + $financialCostArr[$i];
    // echo "ebitArr($i):".$ebitArr[$i]."\n";
}

$totalEbitIncrease = 0;
$AverageEbitIncrease = 0;
for ($i=$yearCount-1; $i >= 1 ; $i--) { 
    $totalEbitIncrease += (($ebitArr[$i] - $ebitArr[$i-1])/abs($ebitArr[$i-1]));
}

$AverageEbitIncrease = $totalEbitIncrease / ($yearCount-1);
$ebitAdjustedYear = 0;
if ( $AverageEbitIncrease > 0.2 ){
    $ebitAdjustedYear = 2;
}
else if ($AverageEbitIncrease >= 0.05){
    $ebitAdjustedYear = 1;
}
else if ($AverageEbitIncrease >= 0){
    $ebitAdjustedYear = 0;
}
else{
    $ebitAdjustedYear = -1;
}

if ($bEnableDebugInfo) {
    echo "AverageEbitIncrease:".$AverageEbitIncrease." ebitAdjustedYear:".$ebitAdjustedYear."\n";
    echo "first zero count:$iFirstZeroCount\n";
    echo "first financial cost:".$financialCostArr[0]."\n";
}

//财务比率表
//7|0|9|http://www.ichangtou.com/ichangtou/|61B460404EE22A76E213EC9F66BFBFCE|com.ichangtou.webproject.client.GreetingService|getGridData|java.lang.String/2004016611|I|600694|F|Q4|1|2|3|4|4|5|5|6|5|7|8|10|9|
$post_data_suf = "|F|Q4|1|2|3|4|4|5|5|6|5|7|8|10|9|";
$post_data = $post_data_pre.$queryID.$post_data_suf;
curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: text/x-gwt-rpc; charset=UTF-8',
    'Content-Length: ' . strlen($post_data),
    'X-GWT-Permutation:E518E025620D5EA148529190B19E8E17'));
$output = curl_exec($ch);

$response = RemoveStrMark($output, "[", "]");
$dataArr = explode(",", $response);
$response = RemoveStrMark($response, "[", "]");
$infoArr = explode(",", $response);

$iCount = 0;
$roicArr = array();
$roicFlag = false;
$capitalCostArr = array();
$capitalCostFlag = false;
$retainedCashFlowArr = array();
$retainedCashFlowFlag = false;

//同理，由于前面计算过总列数，所以无需重复计算，直接从标题开始取值
foreach ($infoArr as $var) {
    if (!$roicFlag && $var == "\"ROIC 投资回报率\"") {
        $roicFlag = true;
        for ($i=0; $i < $yearCount; $i++) {
            $tmp = $infoArr[$iCount+1+$i];
            $roicArr[] = floatval(RemoveStrMark($tmp));
        }
    }

    if (!$capitalCostFlag && $var == "\"资本开支\"") {
        $capitalCostFlag = true;
        for ($i=0; $i < $yearCount; $i++) {
            $tmp = $infoArr[$iCount+1+$i];
            $capitalCostArr[] = floatval(RemoveStrMark($tmp));
        }
    }

    if (!$retainedCashFlowFlag && $var == "\"净现金流\"") {
        $retainedCashFlowFlag = true;
        for ($i=0; $i < $yearCount; $i++) {
            $tmp = $infoArr[$iCount+1+$i];
            $retainedCashFlowArr[] = floatval(RemoveStrMark($tmp));
        }
    }

    ++$iCount;
}

//----bear base value
$totalRetainedCashFlow = 0;
$totalCapitalCost = 0;
for ($i=0; $i < $yearCount; $i++) { 
    $totalRetainedCashFlow += $retainedCashFlowArr[$i];
    $totalCapitalCost += $capitalCostArr[$i];
}
$bearBaseValue = $totalRetainedCashFlow / $totalCapitalCost;
$bearAdjustedYear = 0;
if ($bearBaseValue < 0.1){
    $bearAdjustedYear = -1;
}
else{
    $bearAdjustedYear = 0;
}

//----safe bound adjust
$safeBoundAdjusted = 0;
$minusRoicCount = 0;
for ($i=0; $i < $yearCount; $i++) { 
    if ( $roicArr[$i] < 0 ){
        ++$minusRoicCount;
    }
}

if ($minusRoicCount >= 2){
    $safeBoundAdjusted -= 0.2;
}
else if($minusRoicCount > 0){
    $safeBoundAdjusted -= 0.1;
}

if ($bEnableDebugInfo) {
    echo "bearBaseValue:".$bearBaseValue." bearAdjustedYear:".$bearAdjustedYear."\n";
    echo "safeBoundAdjusted".$safeBoundAdjusted." first ROIC:".$roicArr[0]." first capitalCost:".$capitalCostArr[0]."\n";
}

$expectPaybackYear = 10;
$finalPaybackYear = $expectPaybackYear + $ebitAdjustedYear + $bearAdjustedYear;
$safeBound = 0.8;
$finalSafeBound = $safeBound + $safeBoundAdjusted;
$totalRoic = 0;
for ($i=0; $i < $yearCount; $i++) {
    $totalRoic += $roicArr[$i];
}
$averageRoic = $totalRoic / (float)$yearCount;

$precisionAssessmentTotalValue = (($currentAssets + $fixedAssets)*$finalPaybackYear*$averageRoic+$cash-$shortLoan-$longLoan-$bondsPayable)*$finalSafeBound;
$precisionAssessmentValue = $precisionAssessmentTotalValue / $totalValue * $value;
if ($bEnableDebugInfo) {
    echo "precisionAssessmentTotalValue:".$precisionAssessmentTotalValue." totalRoic:".$totalRoic." averageRoic:".$averageRoic."\n";
}

echo "precisionAssessmentValue:$precisionAssessmentValue";

curl_close($ch);
