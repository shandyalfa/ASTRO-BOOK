<?php

include 'config.php';
session_start();

$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
   header('location:home2.php');
   exit;
}

$message = ''; // Variabel untuk menyimpan pesan popup

if (isset($_POST['add_to_cart'])) {
   $product_name = $_POST['product_name'];
   $product_price = $_POST['product_price'];
   $product_image = $_POST['product_image'];
   $product_quantity = $_POST['product_quantity'];

   $check_cart_numbers = mysqli_query($conn, "SELECT * FROM `cart` WHERE name = '$product_name' AND user_id = '$user_id'") or die('query failed');
   if (mysqli_num_rows($check_cart_numbers) > 0) {
      $message = 'Produk sudah ada di keranjang!';
   } else {
      mysqli_query($conn, "INSERT INTO `cart`(user_id, name, price, quantity, image) VALUES('$user_id', '$product_name', '$product_price', '$product_quantity', '$product_image')") or die('query failed');
      $message = 'Produk berhasil ditambahkan ke keranjang!';
   }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Beranda</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">

   <style>
      /* Popup Overlay */
      .popup-overlay {
         position: fixed;
         top: 0;
         left: 0;
         width: 100%;
         height: 100%;
         background-color: rgba(0, 0, 0, 0.6);
         opacity: 0;
         visibility: hidden;
         transition: opacity 0.3s ease, visibility 0.3s ease;
         z-index: 1000;
      }

      /* Popup */
      .popup {
         position: fixed;
         top: 50%;
         left: 50%;
         transform: translate(-50%, -50%) scale(0.8);
         background: #ffffff;
         box-shadow: 0px 10px 30px rgba(0, 0, 0, 0.2);
         border-radius: 10px;
         padding: 20px;
         width: 90%;
         max-width: 400px;
         text-align: center;
         opacity: 0;
         visibility: hidden;
         transition: opacity 0.3s ease, transform 0.3s ease, visibility 0.3s ease;
         z-index: 1010;
      }

      .popup .icon {
         font-size: 50px;
         margin-bottom: 15px;
      }

      .popup .success {
         color: #28a745;
      }

      .popup .error {
         color: #dc3545;
      }

      .popup h2 {
         font-size: 24px;
         color: #333;
         margin-bottom: 10px;
      }

      .popup p {
         font-size: 16px;
         color: #666;
      }

      .popup button {
         background: #007bff;
         color: #fff;
         border: none;
         padding: 10px 20px;
         border-radius: 5px;
         cursor: pointer;
         font-size: 16px;
         margin-top: 15px;
      }

      /* Active state */
      .popup-overlay.active,
      .popup.active {
         opacity: 1;
         visibility: visible;
      }

      .popup.active {
         transform: translate(-50%, -50%) scale(1);
      }
      .empty {
         text-align: center;
         border:0;

         margin-top: 10px;
      }
      .empty img {
         max-width: 250px;
         height: auto;
         margin: 0 auto;
         display: block;
      }
      .empty-text {
         font-size: 1.8rem;
         color: #777;
         margin-top: 20px;
      }
   </style>
</head>
<body>
   
<?php include 'header.php'; ?>

<?php if ($message): ?>
   <!-- Popup structure -->
   <div class="popup-overlay active" id="popupOverlay"></div>
   <div class="popup active" id="popup">
      <div class="icon <?php echo $message === 'Produk berhasil ditambahkan ke keranjang!' ? 'success' : 'error'; ?>">
         <i class="fas <?php echo $message === 'Produk berhasil ditambahkan ke keranjang!' ? 'fa-check-circle' : 'fa-times-circle'; ?>"></i>
      </div>
      <h2><?php echo $message === 'Produk berhasil ditambahkan ke keranjang!' ? 'Berhasil!' : 'Gagal!'; ?></h2>
      <p><?php echo $message; ?></p>
      <button id="closePopup">Tutup</button>
   </div>
<?php endif; ?>

<section class="home">

   <div class="content">
      <h3>Buku Adalah Jendela Dunia</h3>
      <br><br><br><br>
      <a href="about.php" class="option-btn">Cari tahu lebih dalam</a>
   </div>

</section>

<section class="products">

   <h1 class="title">Produk Sebelumnya</h1>

   <div class="box-container">

      <?php  
         $select_products = mysqli_query($conn, "SELECT * FROM `products` LIMIT 3") or die('query failed');
         if(mysqli_num_rows($select_products) > 0){
            while($fetch_products = mysqli_fetch_assoc($select_products)){
      ?>
     <form action="" method="post" class="box">
      <img class="image" src="uploaded_img/<?php echo $fetch_products['image']; ?>" alt="">
      <div class="name"><?php echo $fetch_products['name']; ?></div>
      <div class="deskripsi">
         <?php 
            if (!empty($fetch_products['deskripsi'])) {
               echo $fetch_products['deskripsi'];
            } 
            else {
               echo "Deskripsi belum ada.";
            }
         ?>
      </div>
      <div class="price">Rp <?php echo number_format($fetch_products['price'], 2, ',', '.'); ?></div>
      <input type="number" min="1" name="product_quantity" value="1" class="qty">
      <input type="hidden" name="product_name" value="<?php echo $fetch_products['name']; ?>">
      <input type="hidden" name="product_deskripsi" value="<?php echo $fetch_products['deskripsi']; ?>">
      <input type="hidden" name="product_price" value="<?php echo $fetch_products['price']; ?>">
      <input type="hidden" name="product_image" value="<?php echo $fetch_products['image']; ?>">
      <input type="submit" value="Tambahkan" name="add_to_cart" class="option-btn">
     </form>
      <?php
         }
      }else{
         echo '<div class="empty">
         <img src="uploaded_img/8551181.jpg" alt="Keranjang Kosong">
         <p class="empty-text">Keranjang belanja Anda kosong!</p>
         <a href="shop.php" class="btn">Mulai Belanja</a>
      </div>';
      }
      ?>
   </div>

   <div class="load-more" style="margin-top: 2rem;text-align:center">
      <a href="shop.php" class="option-btn">Lihat Selengkapnya</a>
   </div>

</section>


<section class="about">
   <div class="flex">
      <div class="image">
         <img src="images/about-img.jpg" alt="">
      </div>
      <div class="content">
         <h3>Tentang Kami</h3>
         <p>
            Selamat datang di Astro, sebuah toko buku multidimensional yang membawa Anda ke dunia literasi tanpa batas. Kami hadir dengan satu tujuan: untuk menyatukan Anda dengan cerita dan pengetahuan dari seluruh penjuru semesta. Di Astro, kami percaya bahwa membaca bukan hanya tentang memahami kata-kata, melainkan tentang petualangan tak terbatas di dalam pikiran dan imajinasi.
         </p>
         <a href="about.php" class="btn">Lihat Selengkapnya</a>
      </div>
   </div>
</section>


<section class="home-contact">
   <div class="content">
      <h3>Punya Pertanyaan?</h3>
      <a href="contact.php" class="option-btn">Hubungi Kami</a>
   </div>
</section>



<?php include 'footer.php'; ?>

<script>
   // Close Popup
   const closePopup = document.getElementById('closePopup');
   const popupOverlay = document.getElementById('popupOverlay');
   const popup = document.getElementById('popup');

   if (closePopup) {
      closePopup.addEventListener('click', () => {
         popupOverlay.classList.remove('active');
         popup.classList.remove('active');
      });
   }

   if (popupOverlay) {
      popupOverlay.addEventListener('click', () => {
         popupOverlay.classList.remove('active');
         popup.classList.remove('active');
      });
   }
   document.addEventListener('DOMContentLoaded', () => {
   const popup = document.getElementById('popup');
   const popupOverlay = document.getElementById('popupOverlay');
   const closePopup = document.getElementById('closePopup');

   if (popup && popupOverlay) {
      // Tutup popup saat tombol "Tutup" diklik
      closePopup.addEventListener('click', () => {
         popupOverlay.classList.remove('active');
         popup.classList.remove('active');
      });

      // Tutup popup saat overlay diklik
      popupOverlay.addEventListener('click', () => {
         popupOverlay.classList.remove('active');
         popup.classList.remove('active');
      });

      // Auto-close popup setelah 5 detik
      setTimeout(() => {
         popupOverlay.classList.remove('active');
         popup.classList.remove('active');
      }, 5000);
   }
});

</script>

<script src="js/script.js"></script>

</body>
</html>
