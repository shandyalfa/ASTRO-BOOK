<?php

include 'config.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
   header('location:login.php');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Admin Dashboard</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <link rel="stylesheet" href="css/admin_style.css">

</head>
<body>
   
<?php include 'admin_header.php'; ?>

<!-- admin dashboard section starts  -->

<section class="dashboard">

   <h1 class="title">Dasbor</h1>

   <div class="box-container">

   <div class="box1">
    <?php
    $total_pendings = 0;
    $select_pending = mysqli_query($conn, "SELECT total_price FROM `orders` WHERE payment_status = 'Tunda'") or die('Query gagal');
    if (mysqli_num_rows($select_pending) > 0) {
        while ($fetch_pendings = mysqli_fetch_assoc($select_pending)) {
            $total_price = $fetch_pendings['total_price'];
            $total_pendings += $total_price;
        }
    }
    ?>
    <h3><div class="price">Rp <?php echo number_format($total_pendings, 2, ',', '.'); ?></div></h3>
    <p>Total Penundaan</p>
</div>

<div class="box2">
    <?php
    $total_completed = 0;
    $select_completed = mysqli_query($conn, "SELECT total_price FROM `orders` WHERE payment_status = 'Selesai'") or die('Query gagal');
    if (mysqli_num_rows($select_completed) > 0) {
        while ($fetch_completed = mysqli_fetch_assoc($select_completed)) {
            $total_price = $fetch_completed['total_price'];
            $total_completed += $total_price;
        }
    }
    ?>
    <h3><div class="price">Rp <?php echo number_format($total_completed, 2, ',', '.'); ?></div></h3>
    <p>Pembayaran Selesai</p>
</div>


      <div class="box3">
         <?php 
            $select_orders = mysqli_query($conn, "SELECT * FROM `orders`") or die('query failed');
            $number_of_orders = mysqli_num_rows($select_orders);
         ?>
         <h3><?php echo $number_of_orders; ?></h3>
         <p>Pesanan diorder</p>
      </div>

      <div class="box4">
         <?php 
            $select_products = mysqli_query($conn, "SELECT * FROM `products`") or die('query failed');
            $number_of_products = mysqli_num_rows($select_products);
         ?>
         <h3><?php echo $number_of_products; ?></h3>
         <p>Produk ditambahkan</p>
      </div>

      <div class="box5">
         <?php 
            $select_users = mysqli_query($conn, "SELECT * FROM `users` WHERE user_type = 'user'") or die('query failed');
            $number_of_users = mysqli_num_rows($select_users);
         ?>
         <h3><?php echo $number_of_users; ?></h3>
         <p>Akun Pengguna</p>
      </div>

      <div class="box6">
         <?php 
            $select_admins = mysqli_query($conn, "SELECT * FROM `users` WHERE user_type = 'admin'") or die('query failed');
            $number_of_admins = mysqli_num_rows($select_admins);
         ?>
         <h3><?php echo $number_of_admins; ?></h3>
         <p>Akun Admin</p>
      </div>

      <div class="box7">
         <?php 
            $select_account = mysqli_query($conn, "SELECT * FROM `users`") or die('query failed');
            $number_of_account = mysqli_num_rows($select_account);
         ?>
         <h3><?php echo $number_of_account; ?></h3>
         <p>Total Akun</p>
      </div>

      <div class="box8">
         <?php 
            $select_messages = mysqli_query($conn, "SELECT * FROM `message`") or die('query failed');
            $number_of_messages = mysqli_num_rows($select_messages);
         ?>
         <h3><?php echo $number_of_messages; ?></h3>
         <p>Pesan Baru</p>
      </div>

   </div>

</section>
<script src="js/admin_script.js"></script>
<?php include 'admin_footer.php'; ?>

</body>
</html>