<?php 
include_once "connectdb.php";

$id = $_POST['pidd'];
$sql = "DELETE FROM tbl_product WHERE pid = $id";

$delete = $pdo->prepare($sql);

if($delete->execute()){
    
}else{
echo "error";
}