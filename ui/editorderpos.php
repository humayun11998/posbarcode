<?php
ob_start();
include_once "connectdb.php";
session_start();


include_once "header.php";

function fill_product($pdo){
  $output = '';
  $select = $pdo->prepare("SELECT * FROM tbl_product ORDER BY product ASC");
  $select->execute();

  $result = $select->fetchAll();

  foreach($result as $row){
    $output .= '<option value="'.$row["pid"].'">'.$row["product"].'</option>';

  }
return $output;
}

$id = $_GET['id'];

$select = $pdo->prepare("SELECT * FROM tbl_invoice WHERE invoice_id = $id");
$select->execute();

$row = $select->fetch(PDO::FETCH_ASSOC);

$order_date = date('Y-m-d', strtotime($row['order_date']));

$sub_total     = $row['subtotal'];
$sgst          = $row['sgst'];
$cgst          = $row['cgst'];
$discount      = $row['discount'];
$total         = $row['total'];
$paid          = $row['paid'];
$due           = $row['due'];
$payment_type  = $row['payment_type'];

$select = $pdo->prepare("SELECT * FROM tbl_invoice_details WHERE invoice_id = $id");
$select->execute();
$row_invoice_details = $select->fetchAll(PDO::FETCH_ASSOC);







// Update Order into Database
if($_SERVER['REQUEST_METHOD'] === "POST" && isset($_POST['btnupdateorder'])){
  
$txt_orderDate = date('Y-m-d');
$txt_subTotal    = $_POST['txtsubtotal'];
$txt_discount    = $_POST['txtdiscount'];
$txt_sGst        = $_POST['txtsgst'];
$txt_cGst        = $_POST['txtcgst'];
$txt_total       = $_POST['txttotal'];
$txt_paymentType = $_POST['rb'];
$txt_due         = $_POST['txtdue']; 
$txt_paid        = $_POST['txtpaid']; 

///////////////////////////////////////////////////////////////////////////////



$arr_pid     = $_POST['pid_arr'];
$stock_idd = isset($_POST['stock_idd']) ? $_POST['stock_idd'] : null;
$qty = isset($_POST['qty']) ? $_POST['qty'] : null;
$price_idd = isset($_POST['price_idd']) ? $_POST['price_idd'] : null;
$saleprice_id = isset($_POST['saleprice_id']) ? $_POST['saleprice_id'] : null;
$pid = isset($_POST['pid']) ? $_POST['pid'] : null;


foreach ($row_invoice_details as $product_invoice_details) {
  $updateproduct_stock = $pdo->prepare("UPDATE tbl_product SET stock = stock + :qty WHERE pid = :product_id");
  $updateproduct_stock->bindParam(':qty', $product_invoice_details['qty'], PDO::PARAM_INT);
  $updateproduct_stock->bindParam(':product_id', $product_invoice_details['product_id'], PDO::PARAM_INT);
  $updateproduct_stock->execute();
}



// Delete Query
$delete_invoice_details = $pdo->prepare("DELETE FROM tbl_invoice_details WHERE invoice_id = $id");
$delete_invoice_details->execute();

// Update Query 
$update_tbl_invoice = $pdo->prepare("UPDATE tbl_invoice SET order_date=:order_date, subtotal=:subtotal, discount=:discount, sgst=:sgst, cgst=:cgst, total=:total, payment_type=:payment_type, due=:due, paid=:paid WHERE invoice_id = $id");

$update_tbl_invoice->bindParam(':order_date', $txt_orderDate);
$update_tbl_invoice->bindParam(':subtotal', $txt_subTotal);
$update_tbl_invoice->bindParam(':discount', $txt_discount);
$update_tbl_invoice->bindParam(':sgst', $txt_sGst);
$update_tbl_invoice->bindParam(':cgst', $txt_cGst);
$update_tbl_invoice->bindParam(':total', $txt_total);
$update_tbl_invoice->bindParam(':payment_type', $txt_paymentType);
$update_tbl_invoice->bindParam(':due', $txt_due);
$update_tbl_invoice->bindParam(':paid', $txt_paid);

$update_tbl_invoice->execute();

$invoice_id = $pdo->lastInsertId();

if($invoice_id != null){

// Write select query for tbl_product table to get out stock value.
  for($i=0; $i < count($arr_pid); $i++){

$selectpdt = $pdo->prepare("SELECT * FROM tbl_product WHERE pid = '".$arr_pid[$i]."'");
$selectpdt->execute();

while($rowpdt = $selectpdt->fetch(PDO::FETCH_OBJ)){
  
  $db_stock[$i] = $rowpdt->stock;

  $rem_qty = $db_stock[$i] - $arr_qty[$i];

if($rem_qty < 0){

  return "Order is not Completed";

}else{

  $update = $pdo->prepare("UPDATE tbl_product SET stock = '$rem_qty' WHERE pid = '".$arr_pid[$i]."'");
  $update->execute();

}

}

$insert = $pdo->prepare("INSERT INTO tbl_invoice_details (invoice_id, barcode, product_id, product_name, qty, rate, saleprice, order_date) VALUES (:inid, :barcode, :pid, :pname, :qty, :rate, :sprice, :orderdate)");
$insert->bindParam(':inid', $id);
$insert->bindParam(':barcode', $arr_barcode[$i]);
$insert->bindParam(':pid', $arr_pid[$i]);
$insert->bindParam(':pname', $arr_pid[$i]);
$insert->bindParam(':qty', $arr_qty[$i]);
$insert->bindParam(':rate', $arr_price[$i]);
$insert->bindParam(':sprice', $arr_total[$i]);
$insert->bindParam(':orderdate', $txt_orderDate);

if(!$insert->execute()){
  print_r($insert->errorInfo());

}


  } // end for loop

header("Location: orderlist.php");
  
} // 1st if end

}

$select = $pdo->prepare("SELECT * FROM tbl_taxdis WHERE taxdis_id = 1");
$select->execute();
$row = $select->fetch(PDO::FETCH_OBJ);


ob_end_flush();
?>

<style type="text/css">

.tableFixHead{
  overflow: scroll;
  height: 520px;
}  

.tableFixHead thead th{
  position: sticky;   
  top: 0;
  z-index: 1;
}


table{
  border-collapse: collapse;
  width:100px;
}

th,td{
  padding:8px 16px;
}

th{
  background:#eee;
}

.select2-container--default .select2-selection--single {
    border: 1px solid #ced4da; 
    border-radius: 4px;
    height: 38px;
    padding: 5px 10px; /* Padding */
}

.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 36px; /* Center text vertically */
}


</style>
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <!-- <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Starter Page</li> -->
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <div class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-lg-12">
            
          <div class="card card-info card-outline">
              <div class="card-header">
                <h5 class="m-0">Edit Order POS</h5>
              </div>
              <div class="card-body">
                 <div class="row">
                    <div class="col-md-8"> 
                        <div class="input-group mb-3">
                  <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fa fa-barcode"></i></span>
                  </div>
                  <input type="text" class="form-control" autocomplete="off" name="txtbarcode" id="txtbarcode_id" placeholder="Scan Barcode">
                </div>
                <form action="" method="POST" name="">
                <div class="form-group mt-4">
                  <select class="form-control select2" data-dropdown-css-class="select2-purple" style="width: 100%;">
                    <option>Select OR Search</option><?php echo fill_product($pdo); ?>
                  </select>
                </div>
                  <br>
                  <div class="tableFixHead">
                  <table id="producttable" class="table table-bordered table-hover">
                <thead>
                  <tr>
                    <th>Product</th>
                    <th>Stock</th>
                    <th>Price</th>
                    <th>QTY</th>
                    <th>Total</th>
                    <th>Delete</th>
                  </tr>
                </thead>
                <tbody class="details" id="itemtable">
                <tr data-widget="expandable-table" aria-expanded="false">

                </tr>
                </tbody>
                  </table>
                  </div>
                </div>
                <div class="col-md-4"> 
                    <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text">SUBTOTAL(Rs)</span>
                  </div>
                  <input type="text" class="form-control" name="txtsubtotal" value="<?php echo $sub_total; ?>" id="txtsubtotal_id" readonly>
                  <div class="input-group-append">
                    <span class="input-group-text">Rs</span>
                  </div>
                </div>
                    <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text">DISCOUNT(%)</span>
                  </div>
                  <input type="text" class="form-control" name="txtdiscount" id="txtdiscount_p" value="<?php echo $row->discount; ?>">
                  <div class="input-group-append">
                    <span class="input-group-text">%</span>
                  </div>
                </div>
                    <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text">DISCOUNT(Rs)</span>
                  </div>
                  <input type="text" class="form-control" id="txtdiscount_n" readonly>
                  <div class="input-group-append">
                    <span class="input-group-text">Rs</span>
                  </div>
                </div>
                    <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text">SGST(%)</span>
                  </div>
                  <input type="text" class="form-control" name="txtsgst" id="txtsgst_id_p" value="<?php echo $row->sgst; ?>" readonly>
                  <div class="input-group-append">
                    <span class="input-group-text">%</span>
                  </div>
                </div>
                    <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text">CGST(%)</span>
                  </div>
                  <input type="text" class="form-control" name="txtcgst" id="txtcgst_id_p" value="<?php echo $row->cgst; ?>" readonly>
                  <div class="input-group-append">
                    <span class="input-group-text">%</span>
                  </div>
                </div>
                    <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text">SGST(Rs)</span>
                  </div>
                  <input type="text" class="form-control" id="txtsgst_id_n" readonly>
                  <div class="input-group-append">
                    <span class="input-group-text">Rs</span>
                  </div>
                </div>
                    <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text">CGST(Rs)</span>
                  </div>
                  <input type="text" class="form-control" id="txtcgst_id_n" readonly>
                  <div class="input-group-append">
                    <span class="input-group-text">Rs</span>
                  </div>
                </div>
                <hr style="height:2px; border-width:0; color: black; background-color:black;">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text">TOTAL(Rs)</span>
                  </div>
                  <input type="text" class="form-control form-control-lg total" value="<?php echo $total; ?>" name="txttotal" id="txt_total" readonly>
                  <div class="input-group-append">
                    <span class="input-group-text">Rs</span>
                  </div>
                </div>

                <hr style="height:2px; border-width:0; color: black; background-color:black;">

                <div class="icheck-success d-inline">
                        <input type="radio" name="rb" value="Cash"<?php echo ($payment_type == 'Cash') ? 'checked' : '' ; ?> id="radioSuccess1">
                        <label for="radioSuccess1">
                          CASH
                        </label>
                      </div>
                      <div class="icheck-primary d-inline">
                        <input type="radio" name="rb" value="Card"<?php echo ($payment_type == 'Card') ? 'checked' : '' ; ?> id="radioSuccess2">
                        <label for="radioSuccess2">
                          CARD
                        </label>
                      </div>
                      <div class="icheck-danger d-inline">
                        <input type="radio" name="rb" value="Check"<?php echo ($payment_type == 'Check') ? 'checked' : '' ; ?> id="radioSuccess3">
                        <label for="radioSuccess3">
                          CHECK
                        </label>
                      </div>

                      <hr style="height:2px; border-width:0; color: black; background-color:black;">

                      <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text">DUE(Rs)</span>
                  </div>
                  <input type="text" class="form-control form-control-lg total" value="<?php echo $due; ?>" name="txtdue" id="txtdue" readonly>
                  <div class="input-group-append">
                    <span class="input-group-text">Rs</span>
                  </div>
                </div>

                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text">PAID(Rs)</span>
                  </div>
                  <input type="text" class="form-control form-control-lg total" value="<?php echo $paid;?>" name="txtpaid" id="txtpaid" required>
                  <div class="input-group-append">
                    <span class="input-group-text">Rs</span>
                  </div>
                </div>
                <hr style="height:2px; border-width:0; color: black; background-color:black;">

                <div class="card-footer" style="margin-left:80px;">
                <!-- Insert Order into Database<div class="text-center"> -->
                  <button type="submit" name="btnupdateorder" class="btn btn-info">Update Order</button>
                </div>
                 </div>

              </div>
            </div>
                    
                 </div>

                 
              </div>
            </form>
            </div>
          </div>
          <!-- /.col-md-6 -->
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
    <div class="p-3">
      <h5>Title</h5>
      <p>Sidebar content</p>
    </div>
  </aside>
  <!-- /.control-sidebar -->
<?php include_once "footer.php"; ?>

<!-- Updated HTML and JavaScript -->
<script>
$(document).ready(function() {
    // Initialize Select2 Elements
    $('.select2').select2();

    var productarr = [];

    function fetchOrderProducts() {
        $.ajax({
            url: "getorderproduct.php",
            method: "GET",
            dataType: "json",
            data: { id: <?php echo $_GET['id'] ?> },
            success: function(data) {
                console.log('Fetched order products:', data);
                if (Array.isArray(data)) {
                    $.each(data, function(key, item) {
                        console.log('Processing item:', item); // Debug each item
                        if (productarr.includes(item["product_id"])) {
                            var actualqty = parseInt($('#qty' + item["product_id"]).val()) + 1;
                            $('#qty' + item["product_id"]).val(actualqty);

                            var saleprice = actualqty * item["saleprice"];
                            $('#saleprice_id' + item["product_id"]).text(saleprice);
                            $('#saleprice_id' + item["product_id"]).val(saleprice);

                            calculate();
                        } else {
                            addRow(item["product_id"], item["product_name"], item["qty"], item["rate"], item["saleprice"], item["stock"], item["barcode"]);
                            productarr.push(item["product_id"]);
                        }
                    });
                } else {
                    console.error('Expected an array of products but received:', data);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error);
            }
        });
    }

    function addRow(product_id, product_name, qty, rate, saleprice, stock, barcode) {
        var tr = '<tr>' +
            '<input type="hidden" class="form-control barcode" name="barcode_arr[]" id="barcode_id' + barcode + '" value="' + barcode + '">' +
            '<td><span class="form-control product_c" name="product_arr[]"><span class="badge badge-dark">' + product_name + '</span><input type="hidden" class="form-control pid" name="pid_arr[]" value="' + product_id + '"></td>' +
            '<td><span class="badge badge-primary stocklbl" id="stock_id' + product_id + '">' + stock + '</span><input type="hidden" class="form-control stock_c" id="stock_idd' + product_id + '" value="' + stock + '"></td>' +
            '<td><span class="badge badge-warning price" id="price_id' + product_id + '">' + saleprice + '</span><input type="hidden" class="form-control price_c" id="price_idd' + product_id + '" value="' + saleprice + '"></td>' +
            '<td><input type="text" class="form-control qty" id="qty' + product_id + '" value="' + qty + '" size="1"></td>' +
            '<td><span class="badge badge-success totalmt" id="saleprice_id' + product_id + '">' + saleprice + '</span><input type="hidden" class="form-control saleprice" id="saleprice_idd' + product_id + '" value="' + (rate * qty) + '"></td>' +
            '<td><center><button type="button" class="btn btn-danger btn-sm btnremove" data-id="' + product_id + '"><span class="fas fa-trash"></span></button></center></td>' +
            '</tr>';

        $('.details').append(tr);
        calculate();
    }

    // Handle Select2 change event
    $('.select2').on('change', function() {
        var productid = $(this).val();
        if (!productid) return;

        $.ajax({
            url: "getproduct.php",
            method: "GET",
            dataType: "json",
            data: { id: productid },
            success: function(data) {
                console.log('Fetched product data:', data);
                if (data && data.pid) {
                    if (productarr.includes(data["pid"])) {
                        var actualqty = parseInt($('#qty' + data["pid"]).val()) + 1;
                        $('#qty' + data["pid"]).val(actualqty);

                        var saleprice = actualqty * data["saleprice"];
                        $('#saleprice_id' + data["pid"]).text(saleprice);
                        $('#saleprice_id' + data["pid"]).val(saleprice);

                        calculate();
                    } else {
                        addRow(data["pid"], data["product"], 1, data["saleprice"], data["saleprice"], data["stock"], data["barcode"]);
                        productarr.push(data["pid"]);
                    }
                } else {
                    console.error('Invalid product data:', data);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error);
            }
        });
    });

    // Handle quantity change event
    $("#itemtable").on("keyup change", ".qty", function() {
        var quantity = $(this);
        var tr = $(this).closest('tr');

        if ((quantity.val() - 0) > (tr.find(".stock_c").val() - 0)) {
            Swal.fire("WARNING!", "SORRY! This Much Of Quantity is Not Available", "warning");
            quantity.val(1);
            calculate();
        }

        var total = quantity.val() * tr.find(".price").text();
        tr.find(".totalmt").text(total);
        tr.find(".saleprice").val(total);
        calculate();
    });

    function calculate() {
        var subtotal = 0;
        var discount = parseFloat($("#txtdiscount_p").val()) || 0;
        var sgst = parseFloat($("#txtsgst_id_p").val()) || 0;
        var cgst = parseFloat($("#txtcgst_id_p").val()) || 0;
        var paid_amount = parseFloat($("#txtpaid").val()) || 0;

        $(".saleprice").each(function() {
            subtotal += parseFloat($(this).val()) || 0;
        });

        $("#txtsubtotal_id").val(subtotal.toFixed(2));

        sgst = sgst / 100 * subtotal;
        cgst = cgst / 100 * subtotal;
        discount = discount / 100 * subtotal;

        $("#txtsgst_id_n").val(sgst.toFixed(2));
        $("#txtcgst_id_n").val(cgst.toFixed(2));
        $("#txtdiscount_n").val(discount.toFixed(2));

        var total = sgst + cgst + subtotal - discount;
        var due = total - paid_amount;

        $("#txt_total").val(total.toFixed(2));
        $("#txtdue").val(due.toFixed(2));
    }

    // Recalculate when discount percentage or paid amount changes
    $("#txtdiscount_p, #txtpaid").on('keyup change', function() {
        calculate();
    });

    // Handle remove button click event
    $(document).on('click', '.btnremove', function() {
        var removedId = $(this).data("id");

        productarr = jQuery.grep(productarr, function(value) {
            return value != removedId;
        });

        $(this).closest('tr').remove();
        calculate();
    });

    // Initial fetch of order products
    fetchOrderProducts();
});
</script>
