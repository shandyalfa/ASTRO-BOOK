<?php

include 'config.php';

session_start();

// Validasi sesi pengguna
$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    header('location:login.php');
    exit;
}

$message = ''; // Variabel untuk menyimpan pesan popup
$icon = '';    // Variabel untuk ikon popup

if (isset($_POST['send'])) {
    // Ambil data dari form
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $number = mysqli_real_escape_string($conn, $_POST['number']);
    $msg = mysqli_real_escape_string($conn, $_POST['message']);

    // Periksa apakah pesan sudah ada
    $select_message = mysqli_query($conn, "SELECT * FROM `message` WHERE name = '$name' AND email = '$email' AND number = '$number' AND message = '$msg'") or die('Query gagal: ' . mysqli_error($conn));

    if (mysqli_num_rows($select_message) > 0) {
        $message = 'Pesan sudah dikirim sebelumnya!';
        $icon = 'fa-times-circle'; // Ikon error
    } else {
        // Masukkan pesan baru ke database
        $insert_query = "INSERT INTO `message`(user_id, name, email, number, message) VALUES('$user_id', '$name', '$email', '$number', '$msg')";
        $insert_result = mysqli_query($conn, $insert_query);

        if ($insert_result) {
            $message = 'Pesan berhasil dikirim!';
            $icon = 'fa-check-circle'; // Ikon sukses
        } else {
            $message = 'Gagal mengirim pesan!';
            $icon = 'fa-exclamation-circle'; // Ikon peringatan
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Kontak</title>

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
         background: #fff;
         box-shadow: 0 15px 40px rgba(0, 0, 0, 0.3);
         border-radius: 10px;
         padding: 20px;
         width: 90%;
         max-width: 400px;
         text-align: center;
         display: none;
         z-index: 1010;
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

      .popup .warning {
         color: #ffc107;
      }

      .popup h2 {
         font-size: 22px;
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
   </style>
</head>
<body>
<?php include 'header.php'; ?>

<!-- Popup -->
<?php if (!empty($message)): ?>
   <div class="popup-overlay" id="popupOverlay"></div>
   <div class="popup" id="popup">
      <div class="icon <?php echo $icon === 'fa-check-circle' ? 'success' : ($icon === 'fa-times-circle' ? 'error' : 'warning'); ?>">
         <i class="fas <?php echo $icon; ?>"></i>
      </div>
      <h2><?php echo $icon === 'fa-check-circle' ? 'Berhasil!' : ($icon === 'fa-times-circle' ? 'Gagal!' : 'Peringatan!'); ?></h2>
      <p><?php echo $message; ?></p>
      <button id="closePopup">Tutup</button>
   </div>
<?php endif; ?>

<div class="heading">
   <h3>Hubungi Kami</h3>
   <p> <a href="home.php">Beranda</a> / Hubungi Kami </p>
</div>

<section class="contact">
   <form action="" method="post">
      <iframe
         src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d31828.599204348587!2d117.12256899899162!3d-0.4723432238550665!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2df67f1f6e9f0a4b%3A0x1a1a7c63bf85f69e!2sFaculty%20of%20Engineering%2C%20Mulawarman%20University!5e0!3m2!1sen!2sid!4v1697558520312!5m2!1sen!2sid"
         allowfullscreen="" loading="lazy"></iframe><br>
      <div class="isi">
         <h3>Berikan Pesan dan Ulasan Anda!</h3><br>
         <input type="text" name="name" required placeholder="Nama" class="box"><br>
         <input type="email" name="email" required placeholder="Email" class="box"><br>
         <input type="number" name="number" required placeholder="No. Telepon" class="box"><br>
         <textarea name="message" class="box" placeholder="Pesan" id="" cols="30" rows="10"></textarea><br>
         <input type="submit" value="Kirim Pesan" name="send" class="option-btn">
      </div>
   </form>
</section>

<script>
   const closePopup = document.getElementById('closePopup');
   const popupOverlay = document.getElementById('popupOverlay');
   const popup = document.getElementById('popup');

   if (closePopup) {
      closePopup.addEventListener('click', () => {
         popupOverlay.style.display = 'none';
         popup.style.display = 'none';
      });

      popupOverlay.addEventListener('click', () => {
         popupOverlay.style.display = 'none';
         popup.style.display = 'none';
      });
   }

   if (popup) {
      popupOverlay.style.display = 'block';
      popup.style.display = 'block';
   }
</script>
<?php include 'footer.php'; ?>

</body>
</html>
