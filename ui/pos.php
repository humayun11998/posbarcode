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


// Insert Order into Database
if($_SERVER['REQUEST_METHOD'] === "POST" && isset($_POST['btnsaveorder'])){
  
$orderDate = date('Y-m-d');
$subTotal    = $_POST['txtsubtotal'];
$discount    = $_POST['txtdiscount'];
$sGst        = $_POST['txtsgst'];
$cGst        = $_POST['txtcgst'];
$total       = $_POST['txttotal'];
$paymentType = $_POST['rb'];
$due         = $_POST['txtdue']; 
$paid        = $_POST['txtpaid']; 

///////////

$arr_pid     = $_POST['pid_arr'];
$arr_barcode = $_POST['barcode_arr'];
$arr_name    = $_POST['pid_arr'];
$arr_stock   = $_POST['stock_c_arr'];
$arr_qty     = $_POST['quantity_arr'];
$arr_price   = $_POST['price_c_arr'];
$arr_total   = $_POST['saleprice_arr'];

// Insert Query 
$insert = $pdo->prepare("INSERT INTO tbl_invoice (order_date, subtotal, discount, sgst, cgst, total, payment_type, due, paid) VALUES (:order_date, :subtotal, :discount, :sgst, :cgst, :total, :payment_type, :due, :paid)");

$insert->bindParam(':order_date', $orderDate);
$insert->bindParam(':subtotal', $subTotal);
$insert->bindParam(':discount', $discount);
$insert->bindParam(':sgst', $sGst);
$insert->bindParam(':cgst', $cGst);
$insert->bindParam(':total', $total);
$insert->bindParam(':payment_type', $paymentType);
$insert->bindParam(':due', $due);
$insert->bindParam(':paid', $paid);

$insert->execute();

$invoice_id = $pdo->lastInsertId();

if($invoice_id != null){

  for($i=0; $i < count($arr_pid); $i++){

$rem_qty = $arr_stock[$i] - $arr_qty[$i];

if($rem_qty < 0){

  return "Order is not Completed";

}else{

  $update = $pdo->prepare("UPDATE tbl_product SET stock = '$rem_qty' WHERE pid = '".$arr_pid[$i]."'");
  $update->execute();

}


$insert = $pdo->prepare("INSERT INTO tbl_invoice_details (invoice_id, barcode, product_id, product_name, qty, rate, saleprice, order_date) VALUES (:inid, :barcode, :pid, :pname, :qty, :rate, :sprice, :orderdate)");
$insert->bindParam(':inid', $invoice_id);
$insert->bindParam(':barcode', $arr_barcode[$i]);
$insert->bindParam(':pid', $arr_pid[$i]);
$insert->bindParam(':pname', $arr_pid[$i]);
$insert->bindParam(':qty', $arr_qty[$i]);
$insert->bindParam(':rate', $arr_price[$i]);
$insert->bindParam(':sprice', $arr_total[$i]);
$insert->bindParam(':orderdate', $orderDate);

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
            
          <div class="card card-success card-outline">
              <div class="card-header">
                <h5 class="m-0">POS</h5>
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
                  <input type="text" class="form-control" name="txtsubtotal" id="txtsubtotal_id" readonly>
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
                  <input type="text" class="form-control form-control-lg total" name="txttotal" id="txt_total" readonly>
                  <div class="input-group-append">
                    <span class="input-group-text">Rs</span>
                  </div>
                </div>

                <hr style="height:2px; border-width:0; color: black; background-color:black;">

                <div class="icheck-success d-inline">
                        <input type="radio" name="rb" value="Cash" checked id="radioSuccess1">
                        <label for="radioSuccess1">
                          CASH
                        </label>
                      </div>
                      <div class="icheck-primary d-inline">
                        <input type="radio" name="rb" value="Card" id="radioSuccess2">
                        <label for="radioSuccess2">
                          CARD
                        </label>
                      </div>
                      <div class="icheck-danger d-inline">
                        <input type="radio" name="rb" value="Check" id="radioSuccess3">
                        <label for="radioSuccess3">
                          CHECK
                        </label>
                      </div>

                      <hr style="height:2px; border-width:0; color: black; background-color:black;">

                      <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text">DUE(Rs)</span>
                  </div>
                  <input type="text" class="form-control form-control-lg total" name="txtdue" id="txtdue" readonly>
                  <div class="input-group-append">
                    <span class="input-group-text">Rs</span>
                  </div>
                </div>

                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text">PAID(Rs)</span>
                  </div>
                  <input type="text" class="form-control form-control-lg total" name="txtpaid" id="txtpaid">
                  <div class="input-group-append">
                    <span class="input-group-text">Rs</span>
                  </div>
                </div>
                <hr style="height:2px; border-width:0; color: black; background-color:black;">

                <div class="card-footer">
                
                
                <!-- Insert Order into Database<div class="text-center"> -->
                  <button type="submit" name="btnsaveorder" class="btn btn-success">Save Order</button>
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
<?php
// Your PHP code remains the same
?>

<!-- Updated HTML and JavaScript -->

<script>
// Initialize Select2 Elements
$('.select2').select2();

var productarr = [];

// Handle select2 change event
$('.select2').on('change', function() {
    var productid = $(this).val();

    $.ajax({
        url: "getproduct.php",
        method: "GET",
        dataType: "json",
        data: { id: productid },
        success: function(data) {
            if (jQuery.inArray(data["pid"], productarr) !== -1) {
                var actualqty = parseInt($('#qty_id' + data["pid"]).val()) + 1;
                $('#qty_id' + data["pid"]).val(actualqty);

                var saleprice = parseInt(actualqty) * data["saleprice"];

                $('#saleprice_id' + data["pid"]).html(saleprice);
                $('#saleprice_id' + data["pid"]).val(saleprice);

                $(".select2").val(null).trigger('change'); // Clear the select2 selection

                calculate(); // Recalculate after quantity update

            } else {
                addrow(data["pid"], data["product"], data["saleprice"], data["stock"], data["barcode"]);

                productarr.push(data["pid"]);
                $(".select2").val(null).trigger('change'); // Clear the select2 selection
            }

            function addrow(pid, product, saleprice, stock, barcode) {
                var tr = '<tr>' +

                    '<input type="hidden" class="form-control barcode" name="barcode_arr[]" id="barcode_id'+barcode+'" value="'+barcode+'">' +
                    '<td style="text-align:left; vertical-align:middle; font-size:17px;"><class="form-control product_c" name="product_arr[]"<span class="badge badge-dark">' + product + '</span><input type="hidden" class="form-control pid" name="pid_arr[]" value="' + pid + '"></td>' +
                    '<td style="text-align:left; vertical-align:middle; font-size:17px;"><span class="badge badge-primary stocklbl" name="stock_arr[]" id="stock_id' + pid + '">' + stock + '</span><input type="hidden" class="form-control stock_c" name="stock_c_arr[]" id="stock_idd' + pid + '" value="' + stock + '"></td>' +
                    '<td style="text-align:left; vertical-align:middle; font-size:17px;"><span class="badge badge-warning price" name="price_arr[]" id="price_id' + pid + '">' + saleprice + '</span><input type="hidden" class="form-control price_c" name="price_c_arr[]" id="price_idd' + pid + '" value="' + saleprice + '"></td>' +
                    '<td><input type="text" class="form-control qty" name="quantity_arr[]" id="qty' + pid + '" value="1" size="1"></td>' +
                    '<td style="text-align:left; vertical-align:middle; font-size:17px;"><span class="badge badge-success totalmt" name="netamt_arr[]" id="saleprice_id' + pid + '">' + saleprice + '</span><input type="hidden" class="form-control saleprice" name="saleprice_arr[]" id="saleprice_idd' + pid + '" value="' + saleprice + '"></td>' +

                    // Remove Button Code Start Here 

                    '<td><center><button type="button" name="remove" class="btn btn-danger btn-sm btnremove" data-id="'+pid+'"><span class="fas fa-trash"></span></button></center></td>'+

                    '</tr>';
                $('.details').append(tr);
                calculate(); // Recalculate after adding new row

            }
            $("#txtbarcode_id").val("");
        }
    });
});

// Handle quantity change event in table
$("#itemtable").delegate(".qty", "keyup change", function() { 
    var quantity = $(this);
    var tr = $(this).closest('tr'); 

    if ((quantity.val() - 0) > (tr.find(".stock_c").val() - 0)) {
        Swal.fire("WARNING!", "SORRY! This Much Of Quantity is Not Available", "warning");
        quantity.val(1);
        calculate(); // Recalculate if quantity is reset
    }

    var total = quantity.val() * tr.find(".price").text();
    tr.find(".totalmt").text(total);
    tr.find(".saleprice").val(total);
    calculate(); // Recalculate after quantity change
});

function calculate() {
    var subtotal = 0;
    var discount = parseFloat($("#txtdiscount_p").val()) || 0; // Get discount percentage
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

$("#txtdiscount_p").keyup(function() {
    calculate(); // Recalculate when discount percentage is changed
}); 

$("#txtpaid").keyup(function() {
    calculate(); // Recalculate when paid amount is changed
});


$(document).on('click', '.btnremove', function() {
    var removedId = $(this).attr("data-id");

    // Remove the product ID from the productarr array
    productarr = jQuery.grep(productarr, function(value) {
        return value != removedId;
    });

    // Remove the corresponding row
    $(this).closest('tr').remove();

    // Recalculate totals after removal
    calculate();
});



</script>

