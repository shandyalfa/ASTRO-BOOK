<?php

include 'config.php';

session_start();



$message = ''; // Variabel untuk menyimpan pesan popup
$icon = '';    // Variabel untuk ikon popup
$search_item = ''; // Variabel untuk kata pencarian

if (isset($_POST['add_to_cart'])) {
    $product_name = $_POST['product_name'];
    $product_price = $_POST['product_price'];
    $product_image = $_POST['product_image'];
    $product_quantity = $_POST['product_quantity'];
    $search_item = $_POST['search'] ?? ''; // Simpan kata pencarian

    $check_cart_numbers = mysqli_query($conn, "SELECT * FROM `cart` WHERE name = '$product_name' AND user_id = '$user_id'") or die('Query gagal.');

    if (mysqli_num_rows($check_cart_numbers) > 0) {
        $message = 'Produk sudah ada di keranjang!';
        $icon = 'fa-exclamation-circle'; // Ikon peringatan
    } else {
        mysqli_query($conn, "INSERT INTO `cart` (user_id, name, price, quantity, image) VALUES ('$user_id', '$product_name', '$product_price', '$product_quantity', '$product_image')") or die('Query gagal.');
        $message = 'Produk berhasil ditambahkan ke keranjang!';
        $icon = 'fa-check-circle'; // Ikon sukses
    }
}

if (isset($_POST['submit']) || isset($_POST['add_to_cart'])) {
    // Tetap gunakan kata pencarian
    $search_item = $_POST['search'] ?? '';
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Halaman Pencarian</title>

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
         background-color: rgba(0, 0, 0, 0.5);
         display: none;
         z-index: 1000;
      }

      /* Popup Container */
      .popup {
         position: fixed;
         top: 50%;
         left: 50%;
         transform: translate(-50%, -50%);
         background: white;
         padding: 30px;
         border-radius: 10px;
         box-shadow: 0 15px 40px rgba(0, 0, 0, 0.3);
         text-align: center;
         display: none;
         z-index: 1010;
      }

      .popup .icon {
         font-size: 50px;
         margin-bottom: 15px;
      }

      .popup .success {
         color: #28a745;
      }

      .popup .warning {
         color: #ffc107;
      }

      .popup h2 {
         font-size: 24px;
         margin-bottom: 10px;
      }

      .popup p {
         font-size: 16px;
         color: #333;
         margin-bottom: 15px;
      }

      .popup button {
         background: #007bff;
         color: white;
         padding: 10px 20px;
         border: none;
         border-radius: 5px;
         cursor: pointer;
         font-size: 16px;
      }

      .empty {
         text-align: center;
         margin-top: 20px;
         border: 0;
         border-radius: .5rem;
      }

      .empty img {
         max-width: 250px;
         height: auto;
         margin: 0 auto;
      }

      .empty-text {
         font-size: 1.8rem;
         color: #777;
         margin-top: 10px;
      }
   </style>
</head>
<body>
<?php include 'header2.php'; ?>

<!-- Popup -->
<?php if (!empty($message)): ?>
   <div class="popup-overlay" id="popupOverlay"></div>
   <div class="popup" id="popup">
      <div class="icon <?php echo $icon === 'fa-check-circle' ? 'success' : 'warning'; ?>">
         <i class="fas <?php echo $icon; ?>"></i>
      </div>
      <h2><?php echo $message; ?></h2>
      <button id="closePopup">Tutup</button>
   </div>
<?php endif; ?>

<div class="heading">
   <h3>Halaman Pencarian</h3>
   <p> <a href="home.php">Beranda</a> / Cari </p>
</div>

<section class="search-form">
   <form action="" method="post">
      <input type="text" name="search" placeholder="Cari Buku" class="box" value="<?php echo htmlspecialchars($search_item); ?>">
      <input type="submit" name="submit" value="Cari" class="btn">
   </form>
</section>

<section class="products" style="padding-top: 0;">
   <div class="box-container">
   <?php
      if (!empty($search_item)) {
         $select_products = mysqli_query($conn, "SELECT * FROM `products` WHERE name LIKE '%{$search_item}%'") or die('Query gagal.');
         if (mysqli_num_rows($select_products) > 0) {
            while ($fetch_product = mysqli_fetch_assoc($select_products)) {
   ?>
   <form action="" method="post" class="box">
      <img src="uploaded_img/<?php echo $fetch_product['image']; ?>" alt="" class="image">
      <div class="name"><?php echo $fetch_product['name']; ?></div>
      <div class="price">Rp <?php echo number_format($fetch_product['price'], 2, ',', '.'); ?></div>
      <input type="number" class="qty" name="product_quantity" min="1" value="1">
      <input type="hidden" name="product_name" value="<?php echo $fetch_product['name']; ?>">
      <input type="hidden" name="product_price" value="<?php echo $fetch_product['price']; ?>">
      <input type="hidden" name="product_image" value="<?php echo $fetch_product['image']; ?>">
      <input type="hidden" name="search" value="<?php echo htmlspecialchars($search_item); ?>">
      <input type="submit" class="btn" value="Tambahkan" name="add_to_cart">
   </form>
   <?php
            }
         } else {
            echo '<div class="empty">
                     <img src="uploaded_img/8551181.jpg" alt="No data found">
                     <p class="empty-text">Buku tidak ditemukan!</p>
                  </div>';
         }
      } else {
         echo '<div class="empty">
                  <img src="uploaded_img/8551181.jpg" alt="Search something">
                  <p class="empty-text">Masukkan kata pencarian!</p>
               </div>';
      }
   ?>
   </div>
</section>

<?php include 'footer.php'; ?>

<script>
   // Tampilkan popup jika ada pesan
   const popup = document.getElementById('popup');
   const popupOverlay = document.getElementById('popupOverlay');
   if (popup) {
      popupOverlay.style.display = 'block';
      popup.style.display = 'block';

      document.getElementById('closePopup').addEventListener('click', function () {
         popupOverlay.style.display = 'none';
         popup.style.display = 'none';
      });
   }
</script>

<script src="js/script.js"></script>

</body>
</html>
