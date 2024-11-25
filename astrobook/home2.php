<?php

include 'config.php';

if(isset($_POST['add_to_cart'])){
   $product_name = $_POST['product_name'];
   $product_price = $_POST['product_price'];
   $product_image = $_POST['product_image'];
   $product_quantity = $_POST['product_quantity'];
  
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

</head>
<body>
   
<?php include 'header2.php'; ?>

<section class="home">

   <div class="content">
      <h3>Buku Adalah Jendela Dunia</h3>
      <br><br><br><br>
      <!-- <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Excepturi, quod? Reiciendis ut porro iste totam.</p> -->
      <a href="about2.php" class="option-btn">Cari tahu lebih dalam</a>
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
            } else {
               echo "Deskripsi belum ada.";
            }
         ?>
      </div>
      <div class="price">Rp <?php echo number_format($fetch_products['price'], 2, ',', '.'); ?></div>
      <input type="number" min="1" name="product_quantity" value="1" class="qty">
      <input type="hidden" name="product_name" value="<?php echo $fetch_products['name']; ?>">
      <input type="hidden" name="product_deskripsi" value="<?php echo $fetch_products['deskripsi'];?>">
      <input type="hidden" name="product_price" value="<?php echo $fetch_products['price']; ?>">
      <input type="hidden" name="product_image" value="<?php echo $fetch_products['image']; ?>">
      <input type="submit" value="Tambahkan" name="add_to_cart" class="option-btn" onclick="return confirmLogin()">

     </form>
      <?php
         }
      }else{
         echo '<p class="empty">Belum Ada Produk yang ditambahkan!</p>';
      }
      ?>
   </div>

   <div class="load-more" style="margin-top: 2rem;text-align:center">
      <a href="shop2.php" class="option-btn">Lihat Selengkapnya</a>
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
         <a href="about2.php" class="btn">Lihat Selengkapnya</a>
      </div>

   </div>

</section>

<section class="home-contact">

   <div class="content">
      <h3>Punya Pertanyaan?</h3>
      <br><br>
      <!-- <p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Atque cumque exercitationem repellendus, amet ullam voluptatibus?</p> -->
      <a href="javascript:void(0);" class="option-btn" onclick="redirectToLogin()">Hubungi Kami</a>
   </div>

</section>

<script>
   function confirmLogin() {
      const userConfirm = confirm("Anda perlu login untuk menambahkan produk ke keranjang. Apakah Anda ingin login?");
      if (!userConfirm) {
         // Mencegah formulir dikirim jika pengguna membatalkan
         return false;
      }
      // Melanjutkan pengiriman formulir jika pengguna setuju
      return true;
   }
</script>


<?php include 'footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>