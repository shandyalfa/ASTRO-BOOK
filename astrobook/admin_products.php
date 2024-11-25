<?php

include 'config.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
   header('location:login.php');
};

$limit = 5;

// Ambil halaman saat ini, jika tidak ada, default ke halaman 1
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Hitung total produk
$total_products_query = mysqli_query($conn, "SELECT COUNT(*) AS total FROM products") or die('Query failed');
$total_products = mysqli_fetch_assoc($total_products_query)['total'];
$total_pages = ceil($total_products / $limit);

// Ambil produk untuk halaman saat ini
$select_products = mysqli_query($conn, "SELECT * FROM products LIMIT $limit OFFSET $offset") or die('Query failed');

if(isset($_POST['add_product'])){

   $name = mysqli_real_escape_string($conn, $_POST['name']);
   $price = $_POST['price'];
   $deskripsi = $_POST['deskripsi'];
   $image = $_FILES['image']['name'];
   $image_size = $_FILES['image']['size'];
   $image_tmp_name = $_FILES['image']['tmp_name'];
   $image_folder = 'uploaded_img/'.$image;

   $select_product_name = mysqli_query($conn, "SELECT name FROM products WHERE name = '$name'") or die('query failed');

   if(mysqli_num_rows($select_product_name) > 0){
      $message[] = 'Nama Produk Sudah DItambahkn';
   }else{
      $add_product_query = mysqli_query($conn, "INSERT INTO products(name, price, deskripsi, image) VALUES('$name', '$price', '$deskripsi', '$image')") or die('query failed');

      if($add_product_query){
         if($image_size > 2000000){
            $message[] = 'Gagal Mengunggah Gambar, Ukuran Gambar Harus Kurang dari 2 MB!';
         }else{
            move_uploaded_file($image_tmp_name, $image_folder);
            $message[] = 'Produk Berhasil Ditambahkan!';
         }
      }else{
         $message[] = 'Produk Tidak Bisa Ditambahkan!';
      }
   }
}

if(isset($_GET['delete'])){
   $delete_id = $_GET['delete'];
   $delete_image_query = mysqli_query($conn, "SELECT image FROM products WHERE id = '$delete_id'") or die('query failed');
   $fetch_delete_image = mysqli_fetch_assoc($delete_image_query);
   unlink('uploaded_img/'.$fetch_delete_image['image']);
   mysqli_query($conn, "DELETE FROM products WHERE id = '$delete_id'") or die('query failed');
   header('location:admin_products.php');
}

// Bagian untuk menangani pembaruan produk
if(isset($_POST['update_product'])){
    $update_p_id = $_POST['update_p_id']; 
    $update_name = mysqli_real_escape_string($conn, $_POST['update_name']); 
    $update_price = $_POST['update_price'];
    $update_deskripsi = mysqli_real_escape_string($conn, $_POST['update_deskripsi']); // Pastikan deskripsi diambil dan di-sanitasi

    // Perbarui data produk (nama, harga, dan deskripsi)
    mysqli_query($conn, "UPDATE products SET name = '$update_name', price = '$update_price', deskripsi = '$update_deskripsi' WHERE id = '$update_p_id'") or die('Query failed');

    // Cek dan perbarui gambar produk jika ada yang baru
    $update_image = $_FILES['update_image']['name'];
    $update_image_tmp_name = $_FILES['update_image']['tmp_name'];
    $update_image_size = $_FILES['update_image']['size'];
    $update_folder = 'uploaded_img/'.$update_image;
    $update_old_image = $_POST['update_old_image'];

    if(!empty($update_image)){
        if($update_image_size > 2000000){
            $message[] = 'Ukuran gambar terlalu besar'; 
        }else{
            // Update gambar di database
            mysqli_query($conn, "UPDATE products SET image = '$update_image' WHERE id = '$update_p_id'") or die('Query failed');
            move_uploaded_file($update_image_tmp_name, $update_folder);
            unlink('uploaded_img/'.$update_old_image); // Menghapus gambar lama
        }
    }

    header('location:admin_products.php'); // Redirect ke halaman produk setelah update
    exit();
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>products</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- custom admin css file link  -->
   <link rel="stylesheet" href="css/admin_style.css">
   <style>
     
   table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
   }

   table, th, td {
      border: 1px solid #333; /* Warna garis */
   }

   th, td {
      padding: 10px;
      text-align: center;
      font-size: 1.6rem;
   }

   th {
      background-color: #f39c12; /* Warna latar header */
   }

   tr:nth-child(even) {
      background-color: #f9f9f9; /* Warna bergaris */
   }

   tr:hover {
      background-color: #f1f1f1; /* Warna saat dihover */
   }
   .pagination {
   display: flex;
   justify-content: center;
   margin-top: 20px;
   gap: 5px;
}

.page-link {
   padding: 10px 15px;
   border: 1px solid #ddd;
   text-decoration: none;
   color: black;
   background-color: #f9f9f9;
   transition: background-color 0.3s ease;
}

.page-link.active {
   background-color: #4caf50;
   color: white;
   font-weight: bold;
}

.page-link:hover {
   background-color: #ddd;
}

   </style>

</head>
<body>
   
<?php include 'admin_header.php'; ?>

<!-- product CRUD section starts  -->


<?php
if (isset($_GET['add'])) {
?>
<section class="add-product-form" style="display: flex;">
   <form action="" method="post" enctype="multipart/form-data">
      <h1 class="title">Tambahkan Produk</h1>
      <input type="text" name="name" class="box" placeholder="Nama Produk" required>
      <input type="number" name="price" min="0" class="box" placeholder="Harga Produk" required>
      <input type="text" name="deskripsi" class="box" placeholder="Deskripsi Produk" required>
       <div class="custom-file-upload">
         <label for="image" class="custom-label">Masukkan Gambar</label>
         <input id="image" type="file" name="image" accept="image/jpg, image/jpeg, image/png" style="display: none;" required>
      </div>
      <input type="submit" value="Tambah" name="add_product" class="btn">
      <button type="reset" id="close-add-product" class="option-btn" onclick="window.location.href='admin_products.php';">Batal</button>
   </form>
</section>
<?php
}
?>


<!-- product CRUD section ends -->

<!-- show products  -->
<section class="show-products">
   <div class= "container-product">
         <h1 class="title">Daftar Produk</h1>
      <a href="admin_products.php?add=true" class="option-btn">Tambah Produk</a>
   </div>
   <?php
      // Jumlah produk per halaman
      $limit = 5;

      // Ambil halaman saat ini, jika tidak ada, default ke halaman 1
      $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
      $offset = ($page - 1) * $limit;

      // Hitung total produk
      $total_products_query = mysqli_query($conn, "SELECT COUNT(*) AS total FROM products") or die('Query failed');
      $total_products = mysqli_fetch_assoc($total_products_query)['total'];
      $total_pages = ceil($total_products / $limit);

      // Ambil produk untuk halaman saat ini
      $select_products = mysqli_query($conn, "SELECT * FROM products LIMIT $limit OFFSET $offset") or die('Query failed');
   ?>
   <table border="1" cellspacing="0" cellpadding="10" style="width: 100%; text-align: center;">
      <thead>
         <tr class="judul-tabel">
            <th>No</th>
            <th>Gambar</th>
            <th>Nama Produk</th>
            <th>Deskripsi</th>
            <th>Harga</th>
            <th>Aksi</th>
         </tr>
      </thead>
      <tbody>
         <?php
            if(mysqli_num_rows($select_products) > 0){
               $no = $offset + 1; // Penomoran berdasarkan halaman
               while($fetch_products = mysqli_fetch_assoc($select_products)){
         ?>
         <tr>
            <td><?php echo $no++; ?></td>
            <td>
               <img src="uploaded_img/<?php echo $fetch_products['image']; ?>" alt="Gambar Produk" style="width: 100px; height: auto;">
            </td>
            <td><?php echo htmlspecialchars($fetch_products['name']); ?></td>
            <td>
               <?php 
                  echo !empty($fetch_products['deskripsi']) 
                     ? htmlspecialchars($fetch_products['deskripsi']) 
                     : "Deskripsi belum ada.";
               ?>
            </td>
            <td>Rp <?php echo number_format($fetch_products['price'], 2, ',', '.'); ?></td>
            <td>
               <a href="admin_products.php?update=<?php echo $fetch_products['id']; ?>" class="option-btn">Ubah</a>
               <a href="admin_products.php?delete=<?php echo $fetch_products['id']; ?>" class="delete-btn" onclick="return confirm('Hapus produk ini?');">Hapus</a>
            </td>
         </tr>
         <?php
               }
            }else{
               echo '<tr><td colspan="6">Produk Belum Ditambahkan!</td></tr>';
            }
         ?>
      </tbody>
   </table>
  <!-- Pagination -->
<div class="pagination">
   <?php if($page > 1): ?>
      <a href="admin_products.php?page=<?php echo $page - 1; ?>" class="page-link">« Sebelumnya</a>
   <?php endif; ?>

   <?php for($i = 1; $i <= $total_pages; $i++): ?>
      <a href="admin_products.php?page=<?php echo $i; ?>" class="page-link <?php if($page == $i) echo 'active'; ?>">
         <?php echo $i; ?>
      </a>
   <?php endfor; ?>

   <?php if($page < $total_pages): ?>
      <a href="admin_products.php?page=<?php echo $page + 1; ?>" class="page-link">Berikutnya »</a>
   <?php endif; ?>
</div>

</section>


<section class="edit-product-form">

   <?php
      if(isset($_GET['update'])){
         $update_id = $_GET['update'];
         $update_query = mysqli_query($conn, "SELECT * FROM products WHERE id = '$update_id'") or die('query failed');
         if(mysqli_num_rows($update_query) > 0){
            while($fetch_update = mysqli_fetch_assoc($update_query)){
   ?>
   <form action="" method="post" enctype="multipart/form-data">
      <input type="hidden" name="update_p_id" value="<?php echo $fetch_update['id']; ?>">
      <input type="hidden" name="update_old_image" value="<?php echo $fetch_update['image']; ?>">
      <img src="uploaded_img/<?php echo $fetch_update['image']; ?>" alt="">
      <input type="text" name="update_name" value="<?php echo $fetch_update['name']; ?>" class="box" required placeholder="Nama">
      <input type="text" name="update_deskripsi" value="<?php echo $fetch_update['deskripsi'];?>" class="box" required placeholder="Deskripsi"> 
      <input type="number" name="update_price" value="<?php echo $fetch_update['price']; ?>" min="0" class="box" required placeholder="Harga">
      <div class="custom-file-upload">
   <label for="update_image" class="custom-label">Masukkan Gambar</label>
   <input id="update_image" type="file" name="update_image" accept="image/jpg, image/jpeg, image/png" style="display: none;">
</div>

      <input type="submit" value="Ubah" name="update_product" class="btn">
      <input type="reset" value="Batal" id="close-update" class="option-btn">
   </form>
   <?php
         }
      }
      }else{
         echo '<script>document.querySelector(".edit-product-form").style.display = "none";</script>';
      }
   ?>

</section>

<!-- custom admin js file link  -->
<script src="js/admin_script.js"></script>
<?php include 'admin_footer.php'; ?>

</body>
</html>