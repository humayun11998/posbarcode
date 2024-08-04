<?php
include_once "connectdb.php";
session_start();

if($_SESSION['useremail']==""){
  header("Location: ../index.php");
  exit;
}

if($_SESSION['role']=="Admin"){

  include_once "header.php";

}else{
  
  include_once "headeruser.php";

}



if($_SERVER['REQUEST_METHOD'] === "POST" && isset($_POST['submit'])){

  $oldPassword         = htmlspecialchars($_POST['old_password']);
  $newPassword         = htmlspecialchars($_POST['new_password']);
  $repeat_new_Password = htmlspecialchars($_POST['repeat_new_password']);


  $email = $_SESSION['useremail'];

  $select = $pdo->prepare("SELECT * FROM tbl_user WHERE useremail = '$email'");

  $select->execute();

  $row = $select->fetch(PDO::FETCH_ASSOC);

  $useremail_db    = $row['useremail'];
  $userpassword_db = $row['userpassword'];




  if($oldPassword == $userpassword_db){

  if($newPassword == $repeat_new_Password){

    $update = $pdo->prepare("UPDATE tbl_user SET userpassword=:pass WHERE useremail=:email");

    $update->bindParam(':pass', $repeat_new_Password);
    $update->bindParam(':email', $email);

    if($update->execute()){

      $_SESSION['status'] = "Password Updated Successfully";
      $_SESSION['status_code'] = "success";

    }else{

      $_SESSION['status'] = "Password Not Updated Successfully";
      $_SESSION['status_code'] = "error";

    }

  }else{
    $_SESSION['status'] = "New Password Does Not Matched";
    $_SESSION['status_code'] = "error";

  }

  }else{
    $_SESSION['status'] = "Password Does Not Matched";
    $_SESSION['status_code'] = "error";
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
            <h1 class="m-0">Change Password</h1>
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
          <div class="card card-info">
              <div class="card-header">
                <h3 class="card-title">Horizontal Form</h3>
              </div>
              <!-- /.card-header -->
              <!-- form start -->
              <form class="form-horizontal" method="post">
                <div class="card-body">
                  <div class="form-group row">
                    <label for="inputPassword3" class="col-sm-2 col-form-label">Old Password:</label>
                    <div class="col-sm-10">
                      <input type="password" class="form-control" id="inputPassword3" placeholder="*****" name="old_password">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="inputPassword3" class="col-sm-2 col-form-label">New Password:</label>
                    <div class="col-sm-10">
                      <input type="password" class="form-control" id="inputPassword3" placeholder="*****" name="new_password">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="inputPassword3" class="col-sm-2 col-form-label">Repeat New Password:</label>
                    <div class="col-sm-10">
                      <input type="password" class="form-control" id="inputPassword3" placeholder="*****" name="repeat_new_password">
                    </div>
                  </div>
                </div>
                <!-- /.card-body -->
                <div class="card-footer">
                  <button type="submit" name="submit" class="btn btn-info">Update Password</button>
                </div>
                <!-- /.card-footer -->
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





</body>
</html>
