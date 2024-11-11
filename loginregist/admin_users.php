<?php
include 'config.php';
session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
   header('location:login.php');
}

// Jumlah data per halaman
$limit = 10;

// Ambil halaman saat ini, jika tidak ada set default ke halaman 1
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Menghitung total data dalam tabel users
$total_users = mysqli_query($conn, "SELECT COUNT(*) AS total FROM `users`") or die('query failed');
$total_users_count = mysqli_fetch_assoc($total_users)['total'];

// Menghitung total halaman
$total_pages = ceil($total_users_count / $limit);

if(isset($_GET['delete'])){
   $delete_id = $_GET['delete'];
   mysqli_query($conn, "DELETE FROM `users` WHERE id = '$delete_id'") or die('query failed');
   header('location:admin_users.php?page='.$page); // Redirect ke halaman yang sama setelah menghapus
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Pengguna</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <link rel="stylesheet" href="css/admin_style.css">

   <style>
      @import url('https://fonts.googleapis.com/css2?family=Rubik:wght@300;400;500;600&display=swap');
      *{
         font-family: 'Rubik', sans-serif;
         margin:0; padding:0;
         box-sizing: border-box;
         outline: none; border:none;
         text-decoration: none;
         transition:all .2s linear;
      }
      .users{
         display:flex;
         justify-content:center;
         align-items:center;
         flex-direction:column;
      }
      table {
         width: 100rem;
         border-collapse: collapse;
         margin-bottom: 20px;
      }

      table, th, td {
         border: 1px solid black;
      }

      th, td {
         font-size:2rem;
         padding: 10px;
         text-align: center;
      }

      .action-buttons {
         display: flex;
         justify-content: center;
         gap: 10px;
      }

      .update-btn {
         background-color: orange;
         color: white;
         padding: 5px 10px;
         border: none;
         cursor: pointer;
      }

      .delete-btn {
         background-color: red;
         color: white;
         padding: 5px 10px;
         border: none;
         cursor: pointer;
      }

      .pagination {
         display: flex;
         justify-content: center;
         margin-top: 20px;
      }

      .pagination a {
         border: 1px solid #ddd;
         padding: 10px 15px;
         cursor: pointer;
         margin: 0 5px;
         text-decoration: none;
         color: black;
      }

      .pagination a.active {
         background-color: #4CAF50;
         color: white;
      }

      .pagination a:hover {
         background-color: #ddd;
      }
   </style>

</head>
<body>
   
<?php include 'admin_header.php'; ?>

<section class="users">

   <h1 class="title">Akun Pengguna</h1>

   <table>
      <thead>
         <tr>
            <th>No</th>
            <th>Identitas Pengguna</th>
            <th>Email</th>
            <th>Aksi</th>
         </tr>
      </thead>
      <tbody>
         <?php
            // Query untuk menampilkan data users dengan LIMIT dan OFFSET
            $select_users = mysqli_query($conn, "SELECT * FROM `users` LIMIT $limit OFFSET $offset") or die('query failed');
            $no = $offset + 1; // Penomoran dimulai dari $offset + 1
            while($fetch_users = mysqli_fetch_assoc($select_users)){
         ?>
         <tr>
            <td><?php echo $no++; ?></td>
            <td><?php echo $fetch_users['name']; ?></td>
            <td><?php echo $fetch_users['email']; ?></td>
            <td class="action-buttons">
               <a href="admin_users.php?edit=<?php echo $fetch_users['id']; ?>" class="option-btn">Ubah</a>
               <a href="admin_users.php?delete=<?php echo $fetch_users['id']; ?>&page=<?php echo $page; ?>" onclick="return confirm('Hapus user ini?');" class="delete-btn">Hapus</a>
            </td>
         </tr>
         <?php
            };
         ?>
      </tbody>
   </table>

   <!-- Pagination -->
   <div class="pagination">
      <?php if($page > 1): ?>
         <a href="admin_users.php?page=<?php echo $page-1; ?>">«</a>
      <?php endif; ?>

      <?php for($i = 1; $i <= $total_pages; $i++): ?>
         <a href="admin_users.php?page=<?php echo $i; ?>" class="<?php if($page == $i) echo 'active'; ?>"><?php echo $i; ?></a>
      <?php endfor; ?>

      <?php if($page < $total_pages): ?>
         <a href="admin_users.php?page=<?php echo $page+1; ?>">»</a>
      <?php endif; ?>
   </div>

</section>

<!-- custom admin js file link  -->
<script src="js/admin_script.js"></script>
<?php include 'admin_footer.php'; ?>

</body>
</html>
