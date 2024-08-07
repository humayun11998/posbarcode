<?php
include_once "connectdb.php";
session_start();



include_once "header.php";

if($_SERVER['REQUEST_METHOD'] === "POST" && isset($_POST['btn_save'])){
  $barcode = htmlspecialchars($_POST['txt_barcode']);
  $product = htmlspecialchars($_POST['txt_pname']);
  $options = $_POST['txtselect_option'];
  $descp = htmlspecialchars($_POST['txt_descp']);
  $stock = htmlspecialchars($_POST['txt_stock']);
  $purchasePrice = htmlspecialchars($_POST['txt_purchaseprice']);
  $salePrice = htmlspecialchars($_POST['txt_saleprice']);

  $f_name = $_FILES['myfile']['name'];
  $f_tmp  =  $_FILES['myfile']['tmp_name'];
  $f_size  =  $_FILES['myfile']['size'];
  $f_extension = explode('.',$f_name);
  $f_extension = strtolower(end($f_extension));
  $f_newfile = uniqid() . '.' . $f_extension;
  $store = "productimages/".$f_newfile;

  if($f_extension=='jpg' || $f_extension=='png' || $f_extension=='gif' || $f_extension=='jpeg'){

    if($f_size>=1000000){
      $_SESSION['status'] = "Max file Should be 1MB";
      $_SESSION['status_code'] = "warning";

    }else{
      if(move_uploaded_file($f_tmp,$store)){
        $productImage = $f_newfile;

        if(empty($barcode)){

          $insert = $pdo->prepare("INSERT INTO tbl_product (product, category, description, stock, purchaseprice, saleprice, image) VALUES (:product, :category, :description, :stock, :purchaseprice, :saleprice, :image)");

          $insert->bindParam(':product', $product);
          $insert->bindParam(':category', $options);
          $insert->bindParam(':description', $descp);
          $insert->bindParam(':stock', $stock);
          $insert->bindParam(':purchaseprice', $purchasePrice);
          $insert->bindParam(':saleprice', $salePrice);
          $insert->bindParam(':image', $productImage);

        $insert->execute();

        $pid = $pdo->lastInsertId();

        date_default_timezone_set("Asia/karachi");
        $newbarcode = $pid.date('his');
        $update = $pdo->prepare("UPDATE tbl_product SET barcode='$newbarcode' WHERE pid = '$pid'");

        if($update->execute()){
          $_SESSION['status'] = "Product Insert Successfully";
          $_SESSION['status_code'] = "success";
        }else{
          $_SESSION['status'] = "Product Insert Failed";
            $_SESSION['status_code'] = "error";

        }
        }else{
          $insert = $pdo->prepare("INSERT INTO tbl_product (barcode, product, category, description, stock, purchaseprice, saleprice, image) VALUES (:barcode, :product, :category, :description, :stock, :purchaseprice, :saleprice, :image)");

          $insert->bindParam(':barcode', $barcode);
          $insert->bindParam(':product', $product);
          $insert->bindParam(':category', $options);
          $insert->bindParam(':description', $descp);
          $insert->bindParam(':stock', $stock);
          $insert->bindParam(':purchaseprice', $purchasePrice);
          $insert->bindParam(':saleprice', $salePrice);
          $insert->bindParam(':image', $productImage);

          if($insert->execute()){
            $_SESSION['status'] = "Product Insert Successfully";
            $_SESSION['status_code'] = "success";

          }else{
            $_SESSION['status'] = "Product Insert Failed";
            $_SESSION['status_code'] = "error";

          }

        }


      }

    }

  }else{
    $_SESSION['status'] = "only jpg, png, jpeg and gif can be uploaded";
    $_SESSION['status_code'] = "warning";
  }

}

?>
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Add Product</h1>
          </div><!-- /.col -->
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
      <div class="card card-primary card-outline">
      <form method="post" enctype="multipart/form-data">
        <div class="card-body">
                <div class="row mt-2">
            <div class="col-md-6">
            <div class="form-group">
                    <label>Barcode</label>
                    <input type="text" class="form-control" name="txt_barcode" placeholder="Enter Barcode Name">
                  </div>
                  <div class="form-group">
                    <label>Product Name</label>
                    <input type="text" class="form-control" name="txt_pname" placeholder="Enter product Name" required>
                  </div>
                  <div class="form-group">
                        <label>Category</label>
                        <select class="form-control" name="txtselect_option" required>
                          <option value="" disabled selected>Select Category</option>
                          <?php
                          $select = $pdo->prepare("SELECT * FROM tbl_category ORDER BY catid DESC");
                          $select->execute();
                          while($row = $select->fetch(PDO::FETCH_ASSOC)){
                            extract($row);
                            ?>
                            <option><?php echo $row['category']; ?></option>
                         <?php }

                          ?>
                        </select>
                      </div>
                      <div class="form-group">
                    <label>Description</label>
                    <textarea class="form-control" name="txt_descp" placeholder="Enter Description" rows="4"></textarea>
                  </div>
            </div>
            <div class="col-md-6">
            <div class="form-group">
            <label>Stock Quantity</label>
            <input type="number" min="1" step="any" class="form-control" name="txt_stock" placeholder="Enter Stock">
            </div>
            <div class="form-group">
            <label>Purchase Price</label>
            <input type="number" min="1" step="any" class="form-control" name="txt_purchaseprice" placeholder="Enter Stock">
            </div>
            <div class="form-group">
            <label>Sale Price</label>
            <input type="number" min="1" step="any" class="form-control" name="txt_saleprice" placeholder="Enter Stock">
            </div>
            <div class="form-group">
                    <label>Product Image</label>
                    <input type="file" class="input-group" name="myfile">
                  </div>
            </div>
              </div>
              </div>
              <div class="card-footer">
                <div class="text-center">
                  <button type="submit" name="btn_save" class="btn btn-primary">Save Product</button>
                </div>
                </div>
              </form>
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
if(isset($_SESSION['status']) && $_SESSION['status'] != ''){

?>
<script>




     Swal.fire({
        icon: '<?= $_SESSION['status_code']; ?>',
        title: '<?= $_SESSION['status']; ?>'
      })
</script>
<?php

unset($_SESSION['status']);
}

?>

<script>


  $(document).ready( function () {
    $('#table_category').DataTable();
} );


</script>

