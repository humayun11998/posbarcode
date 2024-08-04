<?php
include_once "ui/connectdb.php";
session_start();

if(isset($_SESSION['useremail']) && $_SESSION['role']=="Admin"){
  header("Location: ../ui/dashboard.php");
  exit;
}elseif(isset($_SESSION['useremail']) && $_SESSION['role']=="User") {
  header("Location: ../ui/user.php");
  exit;
}

if(isset($_POST['btn_login'])){

$useremail = $_POST['txt_email'];
$userpassword = $_POST['txt_password'];
// $hash_password = password_hash($userpassword, PASSWORD_DEFAULT);

$select = $pdo->prepare("SELECT * FROM tbl_user WHERE useremail = '$useremail' AND userpassword = '$userpassword'");
$select->execute();

$row = $select->fetch(PDO::FETCH_ASSOC);

if(is_array($row)){
  if($row['useremail'] == $useremail AND $row['userpassword'] == $userpassword AND $row['role'] == "Admin"){

    // echo $success = "Login success By Admin";

    header("refresh: 1;ui/dashboard.php");

    $_SESSION['userid']    = $row['userid'];
    $_SESSION['username']  = $row['username'];
    $_SESSION['useremail'] = $row['useremail'];
    $_SESSION['role']      = $row['role'];

    $_SESSION['status'] = "Login success By Admin";
    $_SESSION['status_code'] = "success";

}elseif($row['useremail'] == $useremail AND $row['userpassword'] == $userpassword AND $row['role'] == "User"){

  // echo $success = "Login success By User";

  header("refresh: 1;ui/user.php");


  $_SESSION['userid']    = $row['userid'];
  $_SESSION['username']  = $row['username'];
  $_SESSION['useremail'] = $row['useremail'];
  $_SESSION['role']      = $row['role'];

  $_SESSION['status'] = "Login success By User";
  $_SESSION['status_code'] = "success";
}

}else{

  // echo $success = "Wrong Email or Password";

  $_SESSION['status'] = "Wrong Email or Password";
  $_SESSION['status_code'] = "error";



}
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>POS BARCODE | Log in </title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
  <!-- SweetAlert2 -->
   <link rel="stylesheet" href="plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
  <!-- Toastr -->
  <link rel="stylesheet" href="plugins/toastr/toastr.min.css">
</head>
<body class="hold-transition login-page">
<div class="login-box">
  <!-- /.login-logo -->
  <div class="card card-outline card-primary">
    <div class="card-header text-center">
      <a href="index2.html" class="h1"><b>POS</b>BARCODE</a>
    </div>
    <div class="card-body">
      <p class="login-box-msg">Sign in to start your session</p>

      <form method="post">
        <div class="input-group mb-3">
          <input type="email" class="form-control" placeholder="Email" name="txt_email" required>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-envelope"></span>
            </div>
          </div>
        </div>
        <div class="input-group mb-3">
          <input type="password" class="form-control" placeholder="Password" name="txt_password" required>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-8">
          <a href="forgot">I forgot my password</a>
          </div>
          <!-- /.col -->
          <div class="col-4">
            <button type="submit" name="btn_login" class="btn btn-primary btn-block">Login</button>
          </div>
          <!-- /.col -->
        </div>
      </form>
    </div>
    <!-- /.card-body -->
  </div>
  <!-- /.card -->
</div>
<!-- /.login-box -->

<!-- jQuery -->
<script src="plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>
<!-- SweetAlert2 -->
<script src="plugins/sweetalert2/sweetalert2.min.js"></script>
<!-- Toastr -->
<script src="plugins/toastr/toastr.min.js"></script>

<?php
if(isset($_SESSION['status']) && $_SESSION['status'] != ''){

?>
<script>
  $(function() {
    var Toast = Swal.mixin({
      toast: true,
      position: 'top',
      showConfirmButton: false,
      timer: 5000
    });

     Toast.fire({
        icon: '<?= $_SESSION['status_code']; ?>',
        title: '<?= $_SESSION['status']; ?>'
      })
    })
</script>
<?php

unset($_SESSION['status']);
}

?>





</body>
</html>
