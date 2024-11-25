<?php

include 'config.php';

session_start();

$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
    header('location:login.php');
    exit;
}

$message = ''; // Variabel untuk menyimpan pesan popup
$icon = '';    // Variabel untuk ikon popup

if (isset($_POST['update_cart'])) {
    $cart_id = $_POST['cart_id'];
    $cart_quantity = $_POST['cart_quantity'];

    // Update jumlah item di keranjang
    mysqli_query($conn, "UPDATE `cart` SET quantity = '$cart_quantity' WHERE id = '$cart_id'") or die('Query failed');
    $message = 'Keranjang sudah diupdate!';
    $icon = 'fa-check-circle'; // Ikon sukses
}

if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];

    // Hapus item dari keranjang
    mysqli_query($conn, "DELETE FROM `cart` WHERE id = '$delete_id'") or die('Query failed');
    $message = 'Item berhasil dihapus!';
    $icon = 'fa-times-circle'; // Ikon error
}

if (isset($_GET['delete_all'])) {
    // Hapus semua item dari keranjang
    mysqli_query($conn, "DELETE FROM `cart` WHERE user_id = '$user_id'") or die('Query failed');
    $message = 'Semua item berhasil dihapus!';
    $icon = 'fa-trash-alt'; // Ikon peringatan
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Keranjang</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">

   <style>
      .popup-overlay {
         position: fixed;
         top: 0;
         left: 0;
         width: 100%;
         height: 100%;
         background-color: rgba(0, 0, 0, 0.6);
         opacity: 0;
         visibility: hidden;
         z-index: 1000;
         transition: all 0.3s ease;
      }
      .popup {
         position: fixed;
         top: 50%;
         left: 50%;
         transform: translate(-50%, -50%);
         background: white;
         padding: 20px;
         border-radius: 10px;
         box-shadow: 0 15px 40px rgba(0, 0, 0, 0.3);
         width: 90%;
         max-width: 400px;
         text-align: center;
         opacity: 0;
         visibility: hidden;
         z-index: 1010;
         transition: all 0.3s ease;
      }
      .popup.active,
      .popup-overlay.active {
         opacity: 1;
         visibility: visible;
      }
      .popup .icon {
         font-size: 50px;
         margin-bottom: 10px;
      }
      .popup .success {
         color: #28a745;
      }
      .popup .error {
         color: #dc3545;
      }
      .popup h2 {
         font-size: 20px;
         margin-bottom: 10px;
      }
      .popup p {
         margin-bottom: 20px;
      }
      .popup button {
         background: #007bff;
         color: white;
         padding: 10px 20px;
         border: none;
         border-radius: 5px;
         cursor: pointer;
      }
      .empty {
         text-align: center;
         margin-top: 10px;
         border:0;
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
      .shopping-cart .cart-total {
         text-align: center;
         margin-top: 30px;
      }
   </style>
</head>
<body>

<?php include 'header.php'; ?>

<!-- Popup -->
<?php if (!empty($message)): ?>
   <div class="popup-overlay active" id="popupOverlay"></div>
   <div class="popup active" id="popup">
      <div class="icon <?php echo $icon === 'fa-check-circle' ? 'success' : ($icon === 'fa-times-circle' ? 'error' : 'warning'); ?>">
         <i class="fas <?php echo $icon; ?>"></i>
      </div>
      <h2><?php echo $icon === 'fa-check-circle' ? 'Berhasil!' : 'Peringatan!'; ?></h2>
      <p><?php echo $message; ?></p>
      <button id="closePopup">Tutup</button>
   </div>
<?php endif; ?>

<div class="heading">
   <h3>Keranjang Belanja</h3>
   <p> <a href="home.php">Beranda</a> / Keranjang </p>
</div>

<section class="shopping-cart">

   <h1 class="title">Produk ditambahkan</h1>

   <div class="box-container">
      <?php
         $grand_total = 0;
         $select_cart = mysqli_query($conn, "SELECT * FROM `cart` WHERE user_id = '$user_id'") or die('Query failed');
         if (mysqli_num_rows($select_cart) > 0) {
            while ($fetch_cart = mysqli_fetch_assoc($select_cart)) {   
      ?>
      <div class="box">
         <a href="cart.php?delete=<?php echo $fetch_cart['id']; ?>" class="fas fa-times" onclick="return confirm('Hapus dari keranjang?');"></a>
         <img src="uploaded_img/<?php echo $fetch_cart['image']; ?>" alt="Produk">
         <div class="name"><?php echo $fetch_cart['name']; ?></div>
         <div class="price" data-price="<?= htmlspecialchars($fetch_cart['price']); ?>">
            Rp <?= number_format($fetch_cart['price'], 2, ',', '.'); ?>
         </div>
         <form action="" method="post">
            <input type="hidden" name="cart_id" value="<?php echo $fetch_cart['id']; ?>">
            <input type="number" min="1" name="cart_quantity" value="<?php echo $fetch_cart['quantity']; ?>">
            <input type="submit" name="update_cart" value="Ubah" class="option-btn">
         </form>
         <div class="sub-total"> 
            Total : 
            <span>Rp <?php echo number_format($sub_total = ($fetch_cart['quantity'] * $fetch_cart['price']), 2, ',', '.'); ?></span> 
         </div> 
      </div>
      <?php
      $grand_total += $sub_total;
         }
      } else {
         echo '<div class="empty">
                  <img src="uploaded_img/8551181.jpg" alt="Keranjang Kosong">
                  <p class="empty-text">Keranjang belanja Anda kosong!</p>
                  <a href="shop.php" class="btn">Mulai Belanja</a>
               </div>';
      }
      ?>
   </div>

   <?php if ($grand_total > 0): ?>
      <div class="cart-total">
    <p>Total Keseluruhan : <span>Rp <?php echo number_format($grand_total, 2, ',', '.'); ?></span></p>

    <div class="flex">
        <a href="shop.php" class="option-btn">Lanjut Belanja</a>
        <a href="checkout.php" class="btn">Proses Belanja</a>
        <a href="cart.php?delete_all" class="delete-btn" onclick="return confirm('Anda yakin ingin menghapus semua item di keranjang?');">Hapus Semua</a>
    </div>
</div>

   <?php endif; ?>

</section>

<script>
   // Tutup Popup
   const closePopup = document.getElementById('closePopup');
   const popupOverlay = document.getElementById('popupOverlay');

   if (closePopup) {
      closePopup.addEventListener('click', () => {
         popupOverlay.classList.remove('active');
         document.querySelector('.popup').classList.remove('active');
      });
   }
</script>
<?php include 'footer.php'; ?>

</body>
</html>

