<?php
include_once "connectdb.php";
session_start();

include_once "header.php";

$id = $_GET['id'] ?? '';

$select = $pdo->prepare("SELECT * FROM tbl_product WHERE pid = $id");
$select->execute();
$row = $select->fetch(PDO::FETCH_ASSOC);


// Update Product 
if($_SERVER['REQUEST_METHOD'] === "POST" && isset($_POST['btneditproduct'])){
  $product_txt = htmlspecialchars($_POST['txt_pname']);
  $options_txt = $_POST['txtselect_option'];
  $descp_txt = htmlspecialchars($_POST['txt_descp']);
  $stock_txt = htmlspecialchars($_POST['txt_stock']);
  $purchasePrice_txt = htmlspecialchars($_POST['txt_purchaseprice']);
  $salePrice_txt = htmlspecialchars($_POST['txt_saleprice']);

  $f_name = $_FILES['myfile']['name'];

  if(!empty($f_name)){
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
          $f_newfile;
          $update = $pdo->prepare("UPDATE tbl_product SET     
          product       = :product,    
          category      = :category,    
          description   = :descp,    
          stock         = :stock,    
          purchaseprice = :purchaseprice,    
          saleprice     = :saleprice,    
          image         = :image
          WHERE pid     = $id");
      
          $update->bindParam(':product',$product_txt);
          $update->bindParam(':category',$options_txt);
          $update->bindParam(':descp',$descp_txt);
          $update->bindParam(':stock',$stock_txt);
          $update->bindParam(':purchaseprice',$purchasePrice_txt);
          $update->bindParam(':saleprice',$salePrice_txt);
          $update->bindParam(':image',$f_newfile);
      
          if($update->execute()){
            $_SESSION['status'] = "Product Update Successfully With New Image";
            $_SESSION['status_code'] = "success";
          }else{
            $_SESSION['status'] = "Product updated Error";
            $_SESSION['status_code'] = "error";
      
          }

        }
      }
      }

   }else{
    $update = $pdo->prepare("UPDATE tbl_product SET     
    product       = :product,    
    category      = :category,    
    description   = :descp,    
    stock         = :stock,    
    purchaseprice = :purchaseprice,    
    saleprice     = :saleprice,    
    image         = :image
    WHERE pid     = $id");

    $update->bindParam(':product',$product_txt);
    $update->bindParam(':category',$options_txt);
    $update->bindParam(':descp',$descp_txt);
    $update->bindParam(':stock',$stock_txt);
    $update->bindParam(':purchaseprice',$purchasePrice_txt);
    $update->bindParam(':saleprice',$salePrice_txt);
    $update->bindParam(':image',$row['image']);

    if($update->execute()){
      $_SESSION['status'] = "Product Update Successfully";
      $_SESSION['status_code'] = "success";
    }else{
      $_SESSION['status'] = "Product updated Error";
      $_SESSION['status_code'] = "error";

    }

  }

}


$select = $pdo->prepare("SELECT * FROM tbl_product WHERE pid = $id");
$select->execute();
$row = $select->fetch(PDO::FETCH_ASSOC);
?>
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Edit Product</h1>
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
      <div class="card card-success card-outline">
      <form method="post" name="formeditproduct" enctype="multipart/form-data">
        <div class="card-body">
                <div class="row mt-2">
            <div class="col-md-6">
            <div class="form-group">
                    <label>Barcode</label>
                    <input type="text" class="form-control" name="txt_barcode" placeholder="Enter Barcode Name" value="<?php echo $row['barcode']; ?>" disabled>
                  </div>
                  <div class="form-group">
                    <label>Product Name</label>
                    <input type="text" class="form-control" name="txt_pname" placeholder="Enter product Name" value="<?php echo $row['product']; ?>">
                  </div>
                  <div class="form-group">
                        <label>Category</label>
                        <select class="form-control" name="txtselect_option" required>
                          <option value="" disabled selected>Select Category</option>
                          <?php
                          $select = $pdo->prepare("SELECT * FROM tbl_category ORDER BY catid DESC");
                          $select->execute();
                          while($row1 = $select->fetch(PDO::FETCH_ASSOC)){
                            extract($row1);
                            ?>
                            <option <?php echo  ($row['category'] === $row1['category'])  ? 'selected' : null ; ?>><?php echo $row1['category']; ?></option>
                         <?php }

                          ?>
                        </select>
                      </div>
                      <div class="form-group">
                    <label>Description</label>
                    <textarea class="form-control" name="txt_descp" placeholder="Enter Description" rows="4"><?php echo $row['description']; ?></textarea>
                  </div>
            </div>
            <div class="col-md-6">
            <div class="form-group">
            <label>Stock Quantity</label>
            <input type="number" min="1" step="any" class="form-control" name="txt_stock" placeholder="Enter Stock" value="<?php echo $row['stock']; ?>">
            </div>
            <div class="form-group">
            <label>Purchase Price</label>
            <input type="number" min="1" step="any" class="form-control" name="txt_purchaseprice" placeholder="Enter Stock" value="<?php echo $row['purchaseprice']; ?>">
            </div>
            <div class="form-group">
            <label>Sale Price</label>
            <input type="number" min="1" step="any" class="form-control" name="txt_saleprice" placeholder="Enter Stock" value="<?php echo $row['saleprice']; ?>">
            </div>
            <div class="form-group">
                    <label>Product Image</label>
                    <input type="file" class="input-group" name="myfile"><br>
                    <img src="productimages/<?php echo $row['image']; ?>" class="img-rounded" width="150">
                  </div>
            </div>
              </div>
              </div>
              <div class="card-footer">
                <div class="text-center">
                  <button type="submit" name="btneditproduct" class="btn btn-success">Update Product</button>
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

