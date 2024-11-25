<?php

include 'config.php';

session_start();

$admin_id = $_SESSION['admin_id'];

// Jumlah data per halaman
$limit = 7;

// Ambil halaman saat ini, jika tidak ada set default ke halaman 1
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Menghitung total data dalam tabel orders
$total_orders_query = mysqli_query($conn, "SELECT COUNT(*) AS total FROM `orders`") or die('Query failed');
$total_orders_count = mysqli_fetch_assoc($total_orders_query)['total'];

// Menghitung total halaman
$total_pages = ceil($total_orders_count / $limit);


if (!isset($admin_id)) {
    header('location:login.php');
    exit;
}

// Jumlah data per halaman
$limit = 7;
// Ambil halaman saat ini, jika tidak ada set default ke halaman 1
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Menghitung total data dalam tabel orders
$total_orders_query = mysqli_query($conn, "SELECT COUNT(*) AS total FROM `orders`") or die('Query failed');
$total_orders_count = mysqli_fetch_assoc($total_orders_query)['total'];

// Menghitung total halaman
$total_pages = ceil($total_orders_count / $limit);

// Menghapus data pesanan jika ada parameter `delete`
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM `orders` WHERE id = '$delete_id'") or die('Query failed');
    header("location:admin_orders.php?page=$page");
    exit;
}

// Memperbarui status pembayaran dan menghitung total
if (isset($_POST['update_order'])) {
    $order_update_id = mysqli_real_escape_string($conn, $_POST['order_id']);
    $update_payment = mysqli_real_escape_string($conn, $_POST['update_payment']);

    if (!$order_update_id || !$update_payment) {
        die("Data tidak diterima dengan benar. Order ID: $order_update_id, Payment Status: $update_payment");
    }

    $update_query = "UPDATE `orders` SET payment_status = '$update_payment' WHERE id = '$order_update_id'";
    $result = mysqli_query($conn, $update_query);

    if ($result) {
        // Redirect untuk menghitung ulang jumlah total di dasbor
        header("location:admin_orders.php?page=$page");
        exit;
    } else {
        die('Update gagal: ' . mysqli_error($conn));
    }
}




?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Pesanan</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/admin_style.css">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th, td {
            padding: 10px;
            text-align: center;
            border: 1px solid #ddd;
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
        }

        .pagination a {
            padding: 10px 15px;
            margin: 0 5px;
            border: 1px solid #ddd;
            color: #333;
            text-decoration: none;
        }

        .pagination a.active {
            background-color: #4CAF50;
            color: #fff;
        }

        .pagination a:hover {
            background-color: #ddd;
        }

        .action-btn {
            padding: 5px 10px;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            margin: 0 5px;
        }

        .edit-btn {
            background-color: #f39c12;
        }

        .delete-btn {
            background-color: #e74c3c;
        }

        .edit-btn:hover, .delete-btn:hover {
            background-color: #333;
        }
        .pagination {
    display: flex;
    justify-content: center;
    margin-top: 20px;
    gap: 5px;
}

.pagination a {
    padding: 10px 15px;
    margin: 0 5px;
    border: 1px solid #ddd;
    color: #333;
    text-decoration: none;
    background-color: #f9f9f9;
    transition: background-color 0.3s ease;
}

.pagination a.active {
    background-color: #4CAF50;
    color: white;
    font-weight: bold;
}

.pagination a:hover {
    background-color: #ddd;
}

        
    </style>
</head>
<body>

<?php include 'admin_header.php'; ?>

<section class="orders">

    <h1 class="title">Manajemen Pesanan</h1>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Email</th>
                <th>Alamat</th>
                <th>Total Produk</th>
                <th>Total Harga</th>
                <th>Metode</th>
                <th>No. Rekening</th>
                <th>Tanggal Pemesanan</th>
                <th>Tenggat Pembayaran</th>
                <th>Bukti Pembayaran</th>
                <th>Status Pembayaran</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Query untuk menampilkan data pesanan dengan LIMIT dan OFFSET
            $select_orders = mysqli_query($conn, "SELECT * FROM `orders` LIMIT $limit OFFSET $offset") or die('Query failed');
            $no = $offset + 1; // Nomor urut
            while ($fetch_orders = mysqli_fetch_assoc($select_orders)) {
            ?>
            <tr>
                <td><?php echo $no++; ?></td>
                <td><?php echo htmlspecialchars($fetch_orders['name']); ?></td>
                <td><?php echo htmlspecialchars($fetch_orders['email']); ?></td>
                <td><?php echo htmlspecialchars($fetch_orders['address']); ?></td>
                <td><?php echo htmlspecialchars($fetch_orders['total_products']); ?></td>
                <td>Rp <?php echo number_format($fetch_orders['total_price'], 2, ',', '.'); ?></td>
                <td><?php echo htmlspecialchars($fetch_orders['method']); ?></td>
                <td><?php echo $fetch_orders['account_number'] ?: '-'; ?></td>
                <td><?php echo $fetch_orders['placed_on'] ?: '-'; ?></td>
                <td><?php echo $fetch_orders['due_date'] ?: '-'; ?></td>
                <td>
                    <?php if ($fetch_orders['proof_of_payment']): ?>
                        <a href="uploaded_proof/<?php echo $fetch_orders['proof_of_payment']; ?>" target="_blank">Lihat Bukti</a>
                    <?php else: ?>
                        -
                    <?php endif; ?>
                </td>
                <td>
    <form action="" method="post">
        <input type="hidden" name="order_id" value="<?php echo $fetch_orders['id']; ?>">
        <select name="update_payment" onchange="this.form.submit()">
            <option value="Tunda" <?php echo $fetch_orders['payment_status'] == 'Tunda' ? 'selected' : ''; ?>>Tunda</option>
            <option value="Selesai" <?php echo $fetch_orders['payment_status'] == 'Selesai' ? 'selected' : ''; ?>>Selesai</option>
        </select>
        <input type="hidden" name="update_order" value="1">
    </form>
</td>

                <td>
                    <a href="admin_orders.php?delete=<?php echo $fetch_orders['id']; ?>&page=<?php echo $page; ?>" onclick="return confirm('Hapus pesanan ini?');" class="action-btn delete-btn">Hapus</a>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>

   <!-- Pagination -->
<div class="pagination">
    <?php if ($page > 1): ?>
        <a href="admin_orders.php?page=<?php echo $page - 1; ?>">« Sebelumnya</a>
    <?php endif; ?>

    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
        <a href="admin_orders.php?page=<?php echo $i; ?>" class="<?php if ($page == $i) echo 'active'; ?>">
            <?php echo $i; ?>
        </a>
    <?php endfor; ?>

    <?php if ($page < $total_pages): ?>
        <a href="admin_orders.php?page=<?php echo $page + 1; ?>">Berikutnya »</a>
    <?php endif; ?>
</div>


</section>

<script src="js/admin_script.js"></script>
<?php include 'admin_footer.php'; ?>

</body>
</html>
