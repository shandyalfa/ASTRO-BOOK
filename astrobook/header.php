

<header class="header">
   <div class="header-1">
      <div class="flex">
         <div class="share">
            <a href="https://www.facebook.com/profile.php?id=61569285095755&mibextid=ZbWKwL" class="fab fa-facebook-f"></a>
            <a href="https://x.com/ABook68006?t=jIbv_ctzU1PG-WGR68KUOg&s=08" class="fab fa-twitter"></a>
            <a href="https://www.instagram.com/astrobook_reads/profilecard/?igsh=MXR5YnM4b2s4Zzduag==" class="fab fa-instagram"></a>
            <a href="https://www.facebook.com/profile.php?id=61569285095755&mibextid=ZbWKwL" class="fab fa-linkedin"></a>
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
            <a href="shop.php">Belanja</a>
            <a href="contact.php">Hubungi Kami</a>
            <a href="orders.php">Pesanan</a>
         </nav>

         <div class="icons">
            <div id="menu-btn" class="fas fa-bars"></div>
            <a href="search_page.php" class="fas fa-search"></a>
            <!-- <div id="user-btn" class="fas fa-user"></div> -->
            <?php
               $select_cart_number = mysqli_query($conn, "SELECT * FROM `cart` WHERE user_id = '$user_id'") or die('query failed');
               $cart_rows_number = mysqli_num_rows($select_cart_number); 
            ?>
            <a href="cart.php"> <i class="fas fa-shopping-cart"></i> <span>(<?php echo $cart_rows_number; ?>)</span> </a>
         </div>

         <div class="user-box">
            <p>Identitas Pengguna : <span><?php echo $_SESSION['user_name']; ?></span></p>
            <p>Email : <span><?php echo $_SESSION['user_email']; ?></span></p>
            <a href="logout.php" class="delete-btn">Keluar</a>
         </div>

      </div>
   </div>

</header>