<?php
error_reporting(0);
$response;
$nameAffectedRow = 0;
$idArr = $_POST["ids"];
$infoArr = $_POST["infos"];
$idCount = count($idArr);
$infoCount = count($infoArr);

//test seg
// $response = json_encode($infoArr[0]);
// echo $response;
// return;

$sqlUser = "root";
$sqlPw = "zhq000136";
$dbName = "stock_assistant";
$tableName = "stock_info";
//create mysqli object
$mysqli = new mysqli();
//connect to mysql
$mysqli->connect('127.0.0.1', $sqlUser, $sqlPw, $dbName);

//sample query
//INSERT INTO `stock_assistant`.`stock_info` (`id`, `name`) VALUES ('1', '2') on duplicate key update name='324234'
for ($i=0; $i < $idCount; $i++) {
    $jsonStr = json_encode($infoArr[$i]);
    $json = json_decode($jsonStr);
    $query = "INSERT INTO $dbName.$tableName (id,name,value,tvalue,comment) ".
    "values("."'".$json->id."',"."'".$json->name."',"."'".$json->value."',"."'".$json->tvalue."',"."'".$json->comment."'".")".
    " on duplicate key update name='$json->name', value='$json->value', tvalue='$json->tvalue', comment='$json->comment'";
    $res = $mysqli->query($query);
    $nameAffectedRow += $mysqli->affected_rows;
}

//test seg
// $f = fopen("./sql.txt", "w");
// fwrite($f, $query);
// fclose($f);
// $response = "row:$nameAffectedRow query:$query";
// echo $response;
// return;

$response = $nameAffectedRow;
echo $response;
return;

?>