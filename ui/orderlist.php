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
            <h1 class="m-0">OrderList</h1>
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
          <div class="card card-primary card-outline">
              <div class="card-header">
                <h5 class="m-0">Orders</h5>
              </div>
              <div class="card-body">

              <table id="table_orderlist" class="table table-striped table-hover">
                <thead>
                  <tr>
                    <td>Invoice ID</td>
                    <td>Order Date</td>
                    <td>Total</td>
                    <td>Paid</td>
                    <td>Due</td>
                    <td>Payment Type</td>
                    <td>ActionIcons</td>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $select = $pdo->prepare("SELECT * FROM tbl_invoice ORDER BY invoice_id ASC");
                  $select->execute();

                  while($row = $select->fetch(PDO::FETCH_OBJ)){

                    echo '
                  <tr>
                    <td>'.$row->invoice_id.'</td>
                    <td>'.$row->order_date.'</td>
                    <td>'.$row->total.'</td>
                    <td>'.$row->paid.'</td>
                    <td>'.$row->due.'</td>';

                    if($row->payment_type == "Cash"){
                      echo '<td><span class="badge badge-warning">'.$row->payment_type.'</span></td>';
                    }elseif($row->payment_type == "Card"){
                        echo '<td><span class="badge badge-success">'.$row->payment_type.'</span></td>';  
                    }else{
                      echo '<td><span class="badge badge-danger">'.$row->payment_type.'</span></td>';
                    }


                  echo '
                    <td>
                    <div class="btn-group">
                    <a href="printbill.php?id='.$row->invoice_id.'"class="btn btn-warning" role="button"><span class="fa fa-print" style="color:#ffffff" data-toggle="tooltip" title="PrintBill"></span></a>
                    <div class="btn-group">
                    <a href="editorderpos.php?id='.$row->invoice_id.'" class="btn btn-primary" role="button"><span class="fa fa-edit" style="color:#ffffff" data-toggle="tooltip" title="EditOrder"></span></a>
                    <button id='.$row->invoice_id.' class="btn btn-danger btndelete"><span class="fa fa-trash" style="color:#ffffff" data-toggle="tooltip" title="DeleteOrder"></span></button>
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


<script>
  $(document).ready( function () {
    $('[data-toggle="tooltip"]').tooltip();
} );

$(document).ready( function(){
  $('.btndelete').click(function(){
    var tdh = $(this);
    var id = $(this).attr("id");

    Swal.fire({
      title: 'Do you want to delete?',
      text: "You won't be able to revert this!",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
      if(result.isConfirmed){

        $.ajax({
          url: 'oderdelete.php',
          type: 'post',
          data: {
            pidd: id
          },
          success: function(data){
            tdh.parents('tr').hide();
          }


});
        Swal.fire(
          'Deleted!',
          'Your Product has been deleted',
          'success'
        )

      }
  })

  });



});



$(document).ready( function () {
    $('#table_orderlist').DataTable({
        "order":[[0,"desc"]]
    });
} );

</script>
