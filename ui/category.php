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

 $category = htmlspecialchars($_POST['txt_category']);

if(empty($category)){

  $_SESSION['status'] = "Category Feild is Empty";
  $_SESSION['status_code'] = "error";

}else{

  $insert = $pdo->prepare("INSERT INTO tbl_category (category) VALUES (:cat)");

  $insert->bindParam(":cat", $category);

  if($insert->execute()){

    $_SESSION['status'] = "Category Added Successfully";
    $_SESSION['status_code'] = "success";

  }else{

    $_SESSION['status'] = "Category Added Failed";
    $_SESSION['status_code'] = "error";

  }

    }
}



if($_SERVER['REQUEST_METHOD'] === "POST" && isset($_POST['btnupdate'])){

  $id      = $_POST['txt_catid'];
  $category = htmlspecialchars($_POST['txt_category']);

 if(empty($category)){

   $_SESSION['status'] = "Category Feild is Empty";
   $_SESSION['status_code'] = "error";

 }else{

   $update = $pdo->prepare("UPDATE tbl_category SET category = :cat WHERE catid=".$id);

   $update->bindParam(":cat", $category);

   if($update->execute()){

     $_SESSION['status'] = "Category Updated Successfully";
     $_SESSION['status_code'] = "success";

   }else{

     $_SESSION['status'] = "Category Updated Failed";
     $_SESSION['status_code'] = "error";

   }

     }
 }

 if($_SERVER['REQUEST_METHOD'] === "POST" && isset($_POST['btndelete'])){
  $delete = $pdo->prepare("DELETE FROM tbl_category WHERE catid =" .$_POST['btndelete']);

  if($delete->execute()){
    $_SESSION['status'] = "Category Deleted Successfully";
    $_SESSION['status_code'] = "success";

  }else{
    $_SESSION['status'] = "Category is Not Deleted";
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
            <h1 class="m-0">Category Form</h1>
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

                    $select = $pdo->prepare("SELECT * FROM tbl_category WHERE catid =".$_POST['btnedit']);
                    $select->execute();

                    if($select){
                      $row = $select->fetch(PDO::FETCH_OBJ);
                      echo
                      '<div class="col-md-4">
                  <div class="card-body">
                    <div class="form-group">
                      <label for="exampleInputEmail1">Category</label>
                      <input type="hidden" class="form-control" name="txt_catid" value = "'.$row->catid.'" placeholder="Enter Category">
                      <input type="text" class="form-control" name="txt_category" value = "'.$row->category.'"id="exampleInputEmail1" placeholder="Enter Category">
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
                    <label for="exampleInputEmail1">Category</label>
                    <input type="text" class="form-control" name="txt_category" id="exampleInputEmail1" placeholder="Enter Category">
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
              <table id="table_category" class="table table-striped table-hover">
                <thead>
                  <tr>
                    <td>#</td>
                    <td>Category</td>
                    <td>Edit</td>
                    <td>Delete</td>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $select = $pdo->prepare("SELECT * FROM tbl_category ORDER BY catid DESC");
                  $select->execute();

                  while($row = $select->fetch(PDO::FETCH_OBJ)){

                    echo '
                  <tr>
                    <td>'.$row->catid.'</td>
                    <td>'.$row->category.'</td>
                    <td>
                    <button type="submit" value = "'.$row->catid.'" name="btnedit" class="btn btn-sm btn-primary"><i class="fa fa-edit"></i></button>
                    </td>
                    <td>
                    <button type="submit" value = "'.$row->catid.'" name="btndelete" class="btn btn-sm btn-danger"><i class="fa fa-trash-alt"></i></button>
                    </td>
                  </tr>';

                   }

                  ?>
                </tbody>
                <tfoot>
                <tr>
                    <td>#</td>
                    <td>Category</td>
                    <td>Edit</td>
                    <td>Delete</td>
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
    $('#table_category').DataTable();
} );


</script>
