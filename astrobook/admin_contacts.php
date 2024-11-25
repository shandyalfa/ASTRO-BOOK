<?php

include 'config.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:login.php');
    exit();
}

// Jumlah pesan per halaman
$limit = 2;

// Ambil halaman saat ini
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Hitung total pesan
$total_messages_query = mysqli_query($conn, "SELECT COUNT(*) AS total FROM `message`") or die('Query failed');
$total_messages = mysqli_fetch_assoc($total_messages_query)['total'];
$total_pages = ceil($total_messages / $limit);

// Hapus pesan jika ada parameter delete
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM `message` WHERE id = '$delete_id'") or die('Query failed');
    header("location:admin_contacts.php?page=$page");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesan</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/admin_style.css">

    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table, th, td {
            border: 1px solid #333;
        }

        th, td {
            padding: 10px;
            text-align: center;
            font-size: 1.6rem;
        }

        th {
           
            background-color: #f39c12;;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
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
   background-color: #4CAF50; /* Warna latar halaman aktif */
   color: white;
}

.pagination a:hover {
   background-color: #ddd; /* Warna latar saat di-hover */
}
    </style>
</head>
<body>

<?php include 'admin_header.php'; ?>

<section class="messages">
    <h1 class="title">Pesan</h1>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>No. Telepon</th>
                <th>Email</th>
                <th>Pesan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $select_message = mysqli_query($conn, "SELECT * FROM `message` LIMIT $limit OFFSET $offset") or die('Query failed');
            if (mysqli_num_rows($select_message) > 0) {
                $no = $offset + 1;
                while ($fetch_message = mysqli_fetch_assoc($select_message)) {
                    echo "<tr>";
                    echo "<td>" . $no++ . "</td>";
                    echo "<td>" . htmlspecialchars($fetch_message['name']) . "</td>";
                    echo "<td>" . htmlspecialchars($fetch_message['number']) . "</td>";
                    echo "<td>" . htmlspecialchars($fetch_message['email']) . "</td>";
                    echo "<td>" . htmlspecialchars($fetch_message['message']) . "</td>";
                    echo '<td><a href="admin_contacts.php?delete=' . $fetch_message['id'] . '&page=' . $page . '" onclick="return confirm(\'Hapus pesan ini?\');" class="delete-btn">Hapus</a></td>';
                    echo "</tr>";
                }
            } else {
                echo '<tr><td colspan="7">Tidak ada pesan!</td></tr>';
            }
            ?>
        </tbody>
    </table>

   <!-- Pagination -->
   <div class="pagination">
   <?php if($page > 1): ?>
      <a href="?page=<?php echo $page - 1; ?>">«</a>
   <?php endif; ?>

   <?php for($i = 1; $i <= $total_pages; $i++): ?>
      <a href="?page=<?php echo $i; ?>" class="<?php if($page == $i) echo 'active'; ?>"><?php echo $i; ?></a>
   <?php endfor; ?>

   <?php if($page < $total_pages): ?>
      <a href="?page=<?php echo $page + 1; ?>">»</a>
   <?php endif; ?>
</div>
</section>

<script src="js/admin_script.js"></script>
<?php include 'admin_footer.php'; ?>

</body>
</html>
