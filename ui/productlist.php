<?php
include_once "connectdb.php";
session_start();


include_once "header.php";

?>
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Product List</h1>
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
        <div class="row">
          <div class="col-lg-12">
          <div class="card">
              <div class="card-header">
                <h5 class="m-0">Product List :</h5>
              </div>
              <div class="card-body">
              <table id="table_product" class="table table-striped table-hover">
                <thead>
                  <tr>
                    <td>Barcode</td>
                    <td>Product</td>
                    <td>Category</td>
                    <td>Description</td>
                    <td>Stock</td>
                    <td>PurchasePrice</td>
                    <td>SalePrice</td>
                    <td>Image</td>
                    <td>ActionIcons</td>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $select = $pdo->prepare("SELECT * FROM tbl_product ORDER BY pid DESC");
                  $select->execute();

                  while($row = $select->fetch(PDO::FETCH_OBJ)){

                    echo '
                  <tr>
                    <td>'.$row->barcode.'</td>
                    <td>'.$row->product.'</td>
                    <td>'.$row->category.'</td>
                    <td>'.$row->description.'</td>
                    <td>'.$row->stock.'</td>
                    <td>'.$row->purchaseprice.'</td>
                    <td>'.$row->saleprice.'</td>
                    <td><img src="productimages/'.$row->image.'" class="img-rounded" width="60px" height="40px" alt=""></td>
                    <td>
                    <div class="btn-group">
                    <a href="printbarcode.php?id='.$row->pid.'"class="btn btn-primary btn-xs" role="button"><span class="fa fa-barcode" style="color:#ffffff" data-toggle="tooltip" title="PrintBarcode"></span></a>
                    <div class="btn-group">
                    <a href="viewproduct.php?id='.$row->pid.'"class="btn btn-warning btn-xs" role="button"><span class="fa fa-eye" style="color:#ffffff" data-toggle="tooltip" title="View Product"></span></a>
                    <a href="editproduct.php?id='.$row->pid.'" class="btn btn-success btn-xs" role="button"><span class="fa fa-edit" style="color:#ffffff" data-toggle="tooltip" title="Edit Product"></span></a>
                    <button id='.$row->pid.' class="btn btn-danger btn-xs"><span class="fa fa-trash" style="color:#ffffff" data-toggle="tooltip" title="Delete Product"></span></button>
                    </div>
                    </td>
                  </tr>';

                   }

                  ?>
                </tbody>
              </table>
              </div>
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
    $('#table_product').DataTable();
} );


</script>

<script>


  $(document).ready( function () {
    $('[data-toggle="tooltip"]').tooltip();
} );


</script>

