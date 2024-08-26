<?php 
include_once "connectdb.php";

$id = $_POST['pidd'];


$select = $pdo->prepare("SELECT * FROM tbl_invoice_details WHERE invoice_id = $id");
$select->execute();
$row_invoice_details = $select->fetchAll(PDO::FETCH_ASSOC);


foreach ($row_invoice_details as $product_invoice_details) {
    $updateproduct_stock = $pdo->prepare("UPDATE tbl_product SET stock = stock + :qty WHERE pid = :product_id");
    $updateproduct_stock->bindParam(':qty', $product_invoice_details['qty'], PDO::PARAM_INT);
    $updateproduct_stock->bindParam(':product_id', $product_invoice_details['product_id'], PDO::PARAM_INT);
    $updateproduct_stock->execute();
  }

$sql = "DELETE tbl_invoice,tbl_invoice_details FROM tbl_invoice INNER JOIN tbl_invoice_details ON tbl_invoice.invoice_id = tbl_invoice_details.invoice_id WHERE tbl_invoice.invoice_id = $id";


$delete = $pdo->prepare($sql);

if($delete->execute()){

    

}else{

echo "error: Failed to delete";



}