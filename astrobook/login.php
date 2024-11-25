<?php

include 'config.php';
session_start();

$message = ''; // Variabel untuk menampung pesan popup

if(isset($_POST['submit'])){

   $email = mysqli_real_escape_string($conn, $_POST['email']);
   $pass = mysqli_real_escape_string($conn, md5($_POST['password']));

   $select_users = mysqli_query($conn, "SELECT * FROM `users` WHERE email = '$email' AND password = '$pass'") or die('query failed');

   if(mysqli_num_rows($select_users) > 0){

      $row = mysqli_fetch_assoc($select_users);

      if($row['user_type'] == 'admin'){

         $_SESSION['admin_name'] = $row['name'];
         $_SESSION['admin_email'] = $row['email'];
         $_SESSION['admin_id'] = $row['id'];
         header('location:admin_page.php');

      }elseif($row['user_type'] == 'user'){

         $_SESSION['user_name'] = $row['name'];
         $_SESSION['user_email'] = $row['email'];
         $_SESSION['user_id'] = $row['id'];
         header('location:home.php');

      }

   }else{
      $message = 'Email atau Kata Sandi Anda Salah!';
   }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Masuk</title>

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
   </style>
</head>
<body>

<?php if ($message): ?>
   <!-- Popup structure -->
   <div class="popup-overlay active" id="popupOverlay"></div>
   <div class="popup active" id="popup">
      <h2>Error</h2>
      <p><?php echo $message; ?></p>
      <button id="closePopup">Close</button>
   </div>
<?php endif; ?>

<div class="form-container">
   <form action="" method="post">
      <img src="images/logo_k.jpeg" alt="logo kelompok" class="logo_k">
      <br>
      <h3>Masuk Sekarang</h3>
      <input type="email" name="email" placeholder="Email" required class="box">
      <input type="password" name="password" placeholder="Kata Sandi" required class="box">
      <input type="submit" name="submit" value="Masuk" class="btn">
      <p>Belum Punya Akun? <a href="register.php">Daftar Sekarang</a></p>
   </form>
</div>

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
</script>

</body>
</html>
