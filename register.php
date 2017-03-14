<?php
/**
 * Created by PhpStorm.
 * User: z
 * Date: 2017/3/6
 * Time: 16:36
 */

$sqlUser = "root";
$sqlPw = "zhq000136";
$dbName = "my_store";
$tableName = "account";
//create mysqli object
$mysqli = new mysqli();
//connect to mysql
$mysqli->connect('127.0.0.1', $sqlUser, $sqlPw, $dbName);
//printf("MySQL error number:%d<br/>", $mysqli->errno);

//get the post value
$name=$_POST["name"];
$pw=$_POST["pw"];
$pwConfirm=$_POST["pwConfirm"];
$email=$_POST["email"];
$phone=$_POST["phone"];

//set query string
$query = "select name from $tableName where name = '$name'";
$mysqli->query($query);
$nameAffectedRow = $mysqli->affected_rows;

$query = "select email from $tableName where email = '$email'";
$mysqli->query($query);
$emailAffectedrows = $mysqli->affected_rows;

//list the query result
// while (list($CName) = $result->fetch_row())
// {
//     ++$nameAffectedRow;
// }

//no repeat account here, so we should insert the new user info to db
if ( $nameAffectedRow == 0 )
{
    $createTime = date("Y/m/d/H/i/s");
    $query = "insert into $dbName.$tableName (name, pw, level, email, phone, create_time, money) 
    VALUES ('$name', '$pw', '0', '$email', '$phone', '$createTime', '0')";
    $mysqli->query($query);
    $newResult = $mysqli->affected_rows;
}

//close the mysql
$mysqli->close();

$json = array('nameRepeat'=>$nameAffectedRow,
'emailRepeat'=>$emailAffectedrows,
'newResult'=>$newResult);
$response = json_encode($json);
echo $response;
?>