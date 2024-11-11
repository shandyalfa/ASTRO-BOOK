<?php

include 'config.php';

session_start();

$user_id = $_SESSION['user_id'];

if(!isset($user_id)){
   header('location:login.php');
}

if(isset($_POST['send'])){

   $name = mysqli_real_escape_string($conn, $_POST['name']);
   $email = mysqli_real_escape_string($conn, $_POST['email']);
   $number = $_POST['number'];
   $msg = mysqli_real_escape_string($conn, $_POST['message']);

   $select_message = mysqli_query($conn, "SELECT * FROM `message` WHERE name = '$name' AND email = '$email' AND number = '$number' AND message = '$msg'") or die('query failed');

   if(mysqli_num_rows($select_message) > 0){
      $message[] = 'Pesan Sudah Dikirim!';
   }else{
      mysqli_query($conn, "INSERT INTO `message`(user_id, name, email, number, message) VALUES('$user_id', '$name', '$email', '$number', '$msg')") or die('query failed');
      $message[] = 'Pesan Berhasil Dikirim!';
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

</head>
<body>
   
<?php include 'header.php'; ?>

<div class="heading">
   <h3>Hubungi Kami</h3>
   <p> <a href="home.php">Beranda</a> / Hubungi Kami </p>
</div>

<section class="contact">

   <form action="" method="post">
      <iframe
               src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d31828.599204348587!2d117.12256899899162!3d-0.4723432238550665!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2df67f1f6e9f0a4b%3A0x1a1a7c63bf85f69e!2sFaculty%20of%20Engineering%2C%20Mulawarman%20University!5e0!3m2!1sen!2sid!4v1697558520312!5m2!1sen!2sid"
               allowfullscreen="" loading="lazy"><br>
      </iframe>
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

<?php include 'footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>