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

$id = $_GET['id'];

if(isset($id)){
  $delete = $pdo->prepare("DELETE FROM tbl_user WHERE userid =" .$id);

  if($delete->execute()){
    $_SESSION['status'] = "Account Deleted Successfully";
    $_SESSION['status_code'] = "success";

  }else{
    $_SESSION['status'] = "Account is Not Deleted";
    $_SESSION['status_code'] = "warning";
  }

}

if($_SERVER['REQUEST_METHOD'] === "POST" && isset($_POST['btn_save'])){

$name = htmlspecialchars($_POST['txt_name']);
$email = htmlspecialchars($_POST['txt_email']);
$password = htmlspecialchars($_POST['txt_password']);
$role = $_POST['txtselect_option'];

if(isset($_POST['txt_email'])){

  $select = $pdo->prepare("SELECT useremail FROM tbl_user WHERE useremail = '$email'");

  $select->execute();

  if($select->rowCount()>0){
    $_SESSION['status'] = "Email Already Exists. Create Account form New Email";
    $_SESSION['status_code'] = "warning";
  }else{

$insert = $pdo->prepare("INSERT INTO tbl_user (username, useremail, userpassword, role) VALUES (:uname, :uemail, :upassword, :urole)");

$insert->bindParam(":uname", $name);
$insert->bindParam(":uemail", $email);
$insert->bindParam(":upassword", $password);
$insert->bindParam(":urole", $role);

if($insert->execute()){

  $_SESSION['status'] = "Insert Successfully the user into the Database";
  $_SESSION['status_code'] = "success";

}else{

  $_SESSION['status'] = "Error inserting the user into the Database";
  $_SESSION['status_code'] = "error";

}

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
            <h1 class="m-0">Registration</h1>
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
                <div class="row mt-2">
                  <div class="col-md-4">
                  <form method="post">
                <div class="card-body">
                  <div class="form-group">
                    <label for="exampleInputEmail1">Name</label>
                    <input type="text" class="form-control" name="txt_name" id="exampleInputEmail1" placeholder="Enter Name" required>
                  </div>
                  <div class="form-group">
                    <label for="exampleInputEmail1">Email address</label>
                    <input type="email" class="form-control" name="txt_email" id="exampleInputEmail1" placeholder="Enter email" required>
                  </div>
                  <div class="form-group">
                    <label for="exampleInputPassword1">Password</label>
                    <input type="password" class="form-control"name="txt_password"  id="exampleInputPassword1" placeholder="Password" required>
                  </div>
                  <div class="form-group">
                        <label>Role</label>
                        <select class="form-control" name="txtselect_option" required>
                          <option value="" disabled selected>Select Role</option>
                          <option>Admin</option>
                          <option>User</option>
                        </select>
                      </div>
                </div>
                <!-- /.card-body -->

                <div class="card-footer">
                  <button type="submit" name="btn_save" class="btn btn-primary">Save</button>
                </div>
              </form>
            </div>
            <div class="col-md-8">
              <table class="table table-striped table-hover">
                <thead>
                  <tr>
                    <td>#</td>
                    <td>Name</td>
                    <td>Email</td>
                    <td>Role</td>
                    <td>Delete</td>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $select = $pdo->prepare("SELECT * FROM tbl_user ORDER BY userid DESC");
                  $select->execute();

                  while($row = $select->fetch(PDO::FETCH_OBJ)){

                    echo '
                  <tr>
                    <td>'.$row->userid.'</td>
                    <td>'.$row->username.'</td>
                    <td>'.$row->useremail.'</td>
                    <td>'.$row->role.'</td>
                    <td>
                    <a href="registration.php?id='.$row->userid.'" class="btn btn-danger"><i class="fa fa-trash-alt"></i></a>
                    </td>
                  </tr>';

                   }

                  ?>
                </tbody>
              </table>
            </div>
              </div>
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
