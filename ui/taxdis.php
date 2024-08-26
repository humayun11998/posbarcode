<?php
include_once "connectdb.php";
session_start();
if($_SESSION['useremail']=="" OR $_SESSION['role']=='User'){
  header("Location: ../index.php");
  exit;
}

if($_SESSION['role']=="Admin"){

  include_once "header.php";

}else{

  include_once "headeruser.php";

}
error_reporting(0);


include_once "header.php";


if($_SERVER['REQUEST_METHOD'] === "POST" && isset($_POST['btn_save'])){

 $sgst = htmlspecialchars($_POST['txt_sgst']);
 $cgst = htmlspecialchars($_POST['txt_cgst']);
 $discount = htmlspecialchars($_POST['txt_discount']);

if(empty($sgst)){

  $_SESSION['status'] = "Feild is Empty";
  $_SESSION['status_code'] = "error";

}else{

  $insert = $pdo->prepare("INSERT INTO tbl_taxdis (sgst, cgst, discount) VALUES (:sgst, :cgst, :discount)");

  $insert->bindParam(":sgst", $sgst);
  $insert->bindParam(":cgst", $cgst);
  $insert->bindParam(":discount", $discount);

  if($insert->execute()){

    $_SESSION['status'] = "Tax and Discount Added Successfully";
    $_SESSION['status_code'] = "success";

  }else{

    $_SESSION['status'] = "Failed";
    $_SESSION['status_code'] = "error";

  }

    }
}



if($_SERVER['REQUEST_METHOD'] === "POST" && isset($_POST['btnupdate'])){

  $id      = $_POST['txt_id'];
  $sgst = htmlspecialchars($_POST['txt_sgst']);
  $cgst = htmlspecialchars($_POST['txt_cgst']);
  $discount = htmlspecialchars($_POST['txt_discount']);
 if(empty($sgst)){

   $_SESSION['status'] = "Feild is Empty";
   $_SESSION['status_code'] = "error";

 }else{

   $update = $pdo->prepare("UPDATE tbl_taxdis SET sgst = :sgst, cgst = :cgst, discount = :discount WHERE taxdis_id=".$id);

   $update->bindParam(":sgst", $sgst);
   $update->bindParam(":cgst", $cgst);
   $update->bindParam(":discount", $discount);

   if($update->execute()){

     $_SESSION['status'] = "Tax and Discount Updated Successfully";
     $_SESSION['status_code'] = "success";

   }else{

     $_SESSION['status'] = "Updated Failed";
     $_SESSION['status_code'] = "error";

   }

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
            <h1 class="m-0">Tax And Discount Form</h1>
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
      <div class="card card-warning card-outline">
      <form method="post">
                <div class="row mt-2">
                  <?php
                  if($_POST['btnedit']){

                    $select = $pdo->prepare("SELECT * FROM tbl_taxdis WHERE taxdis_id =".$_POST['btnedit']);
                    $select->execute();

                    if($select){
                      $row = $select->fetch(PDO::FETCH_OBJ);
                      echo
                      '<div class="col-md-4">
                  <div class="card-body">
                    <div class="form-group">
                      <input type="hidden" class="form-control" name="txt_id" value = "'.$row->taxdis_id.'"> 
                  <div class="form-group">
                    <label for="exampleInputEmail1">SGST(%)</label>
                    <input type="text" class="form-control" name="txt_sgst" placeholder="Enter SGST" value = "'.$row->sgst.'">
                  </div>
                  <div class="form-group">
                    <label for="exampleInputEmail1">CGST(%)</label>
                    <input type="text" class="form-control" name="txt_cgst" placeholder="Enter CGST" value = "'.$row->cgst.'">
                  </div>
                  <div class="form-group">
                    <label for="exampleInputEmail1">Discount(%)</label>
                    <input type="text" class="form-control" name="txt_discount" placeholder="Enter Discount" value = "'.$row->discount.'">
                  </div>

                      
                    </div>
                  </div>
                  <!-- /.card-body -->

                  <div class="card-footer">
                    <button type="submit" name="btnupdate" class="btn btn-primary">Update</button>
                  </div>
              </div>';
                    }

                  }else{
                    echo
                    '<div class="col-md-4">
                <div class="card-body">
                  <div class="form-group">
                    <label for="exampleInputEmail1">SGST(%)</label>
                    <input type="text" class="form-control" name="txt_sgst" placeholder="Enter SGST">
                  </div>
                  <div class="form-group">
                    <label for="exampleInputEmail1">CGST(%)</label>
                    <input type="text" class="form-control" name="txt_cgst" placeholder="Enter CGST">
                  </div>
                  <div class="form-group">
                    <label for="exampleInputEmail1">Discount(%)</label>
                    <input type="text" class="form-control" name="txt_discount" placeholder="Enter Discount">
                  </div>
                </div>
                <!-- /.card-body -->

                <div class="card-footer">
                  <button type="submit" name="btn_save" class="btn btn-warning">Save</button>
                </div>
            </div>';

                  }

                  ?>

            <div class="col-md-8">
              <table id="table_tax" class="table table-striped table-hover">
                <thead>
                  <tr>
                    <td>#</td>
                    <td>SGST</td>
                    <td>CGST</td>
                    <td>Discount</td>
                    <td>Edit</td>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $select = $pdo->prepare("SELECT * FROM tbl_taxdis ORDER BY taxdis_id DESC");
                  $select->execute();

                  while($row = $select->fetch(PDO::FETCH_OBJ)){

                    echo '
                  <tr>
                    <td>'.$row->taxdis_id.'</td>
                    <td>'.$row->sgst.'</td>
                    <td>'.$row->cgst.'</td>
                    <td>'.$row->discount.'</td>
                    <td>
                    <button type="submit" value = "'.$row->taxdis_id.'" name="btnedit" class="btn btn-sm btn-primary"><i class="fa fa-edit"></i></button>
                    </td>
                    <td>
                    </td>
                  </tr>';

                   }

                  ?>
                </tbody>
                <tfoot>
                <tr>
                    <td>#</td>
                    <td>SGST</td>
                    <td>CGST</td>
                    <td>Discount</td>
                    <td>Edit</td>
                  </tr>
                </tfoot>
              </table>
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
    $('#table_tax').DataTable();
} );


</script>
