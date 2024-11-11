<?php
if(isset($message)){
   foreach($message as $message){
      echo '
      <div class="message">
         <span>'.$message.'</span>
         <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
      </div>
      ';
   }
}
?>

<header class="header">
   <div class="header-1">
      <div class="flex">
         <div class="share">
            <a href="#" class="fab fa-facebook-f"></a>
            <a href="#" class="fab fa-twitter"></a>
            <a href="#" class="fab fa-instagram"></a>
            <a href="#" class="fab fa-linkedin"></a>
         </div>
         <div class="icons">
            <p>Selamat Datang 
            <span><?php echo $_SESSION['user_name']; ?></span>
            </p>
            <div id="user-btn" class="fas fa-user"></div>
         </div>
         
      </div>
   </div>

   <div class="header-2">
      <div class="flex">
         <img src="images/logo_k.jpeg" alt="logo kelompok" class="logo_k">
         <!-- <a href="home.php" class="logo">Bookly.</a> -->

         <nav class="navbar">
            <a href="home.php">Beranda</a>
            <a href="about.php">Tentang Kami</a>
            <a href="#">Belanja</a>
            <a href="contact.php">Hubungi Kami</a>
            <a href="#">Pesanan</a>
         </nav>

         <div class="icons">
            <div id="menu-btn" class="fas fa-bars"></div>
            <a href="#" class="fas fa-search"></a>
            <!-- <div id="user-btn" class="fas fa-user"></div> -->
            <?php
               $select_cart_number = mysqli_query($conn, "SELECT * FROM `cart` WHERE user_id = '$user_id'") or die('query failed');
               $cart_rows_number = mysqli_num_rows($select_cart_number); 
            ?>
            <a href="#"> <i class="fas fa-shopping-cart"></i> <span>(<?php echo $cart_rows_number; ?>)</span> </a>
         </div>

         <div class="user-box">
            <p>Identitas Pengguna : <span><?php echo $_SESSION['user_name']; ?></span></p>
            <p>Email : <span><?php echo $_SESSION['user_email']; ?></span></p>
            <a href="logout.php" class="delete-btn">Keluar</a>
         </div>
      </div>
   </div>

</header>