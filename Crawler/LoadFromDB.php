<?php
error_reporting(0);

$sqlUser = "root";
$sqlPw = "zhq000136";
$dbName = "stock_assistant";
$year = 2015;
$tableName = "stock_info_".$year;
//create mysqli object
$mysqli = new mysqli();
//connect to mysql
$mysqli->connect('127.0.0.1', $sqlUser, $sqlPw, $dbName);

$query = "select * from $tableName where (tvalue > value) and ((tvalue-value)/value<1)";
$res = $mysqli->query($query);
$affectedNum = $mysqli->affected_rows;

$jsonArr = array();
while ($row = $res->fetch_array()) {
    $arr = array('id'=>$row['id'],
        'name'=>$row['name'],
        'value'=>$row['value'],
        'tvalue'=>$row['tvalue'],
        'comment'=>$row['comment']
    );
    $json = json_encode($arr);
    $jsonArr[] = $json;
}

$response = json_encode($jsonArr);
echo $response;
return;

?>