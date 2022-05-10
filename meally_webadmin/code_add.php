<?php
session_start();
include('dbcon.php');

if(isset($_POST['save']))
{
$storeaddress = $_POST['storeaddress'];
$storebin = $_POST['storebin'];
$storename = $_POST['storename'];
$storeowner = $_POST['storeowner'];

$postData = [
'storeaddress'=>$storeaddress,
'storebin' =>$storebin,
'storename' =>$storename,
'storeowner'=>$storeowner,
];
$ref_table = "stores";
$postRef_result = $database->getReference($ref_table)->push($postData);


if($postRef_result)
{
     $_SESSION['status'] = "store added succesfully";

}
else
{
    $_SESSION['status'] = "store not added succesfully";
}

}
