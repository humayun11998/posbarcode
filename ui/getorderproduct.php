<?php 
include_once "connectdb.php";

// Get the ID from the query string and validate it
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id > 0) {
    try {
        // Prepare the SQL query with placeholders
        $select = $pdo->prepare("SELECT * FROM tbl_invoice_details a 
                                  INNER JOIN tbl_product b ON a.product_id = b.pid 
                                  WHERE a.invoice_id = :id");

        // Bind the ID parameter to the query
        $select->bindParam(':id', $id, PDO::PARAM_INT);

        // Execute the query
        $select->execute();

        // Fetch all results as an associative array
        $row_invoice_details = $select->fetchAll(PDO::FETCH_ASSOC);

        // Send JSON response
        header("Content-Type: application/json");
        echo json_encode($row_invoice_details);

    } catch (PDOException $e) {
        // Handle SQL errors
        header("Content-Type: application/json");
        echo json_encode(["error" => $e->getMessage()]);
    }
} else {
    // Handle cases where ID is missing or invalid
    header("Content-Type: application/json");
    echo json_encode(["error" => "Invalid ID"]);
}
