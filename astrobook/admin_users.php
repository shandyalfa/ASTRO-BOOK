<?php
include 'config.php';
session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:login.php');
    exit();
}

// Jumlah data per halaman
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Menghitung total data dalam tabel users
$total_users_query = mysqli_query($conn, "SELECT COUNT(*) AS total FROM users") or die('Query gagal');
$total_users = mysqli_fetch_assoc($total_users_query)['total'];
$total_pages = ceil($total_users / $limit);

// Mengubah data pengguna
if (isset($_POST['update_user'])) {
    $update_id = $_POST['update_user_id'];
    $update_name = mysqli_real_escape_string($conn, $_POST['update_name']);
    $update_email = mysqli_real_escape_string($conn, $_POST['update_email']);
    $update_user_type = mysqli_real_escape_string($conn, $_POST['update_user_type']);

    mysqli_query($conn, "UPDATE users SET name = '$update_name', email = '$update_email', user_type = '$update_user_type' WHERE id = '$update_id'") or die('Query gagal');
    header("location:admin_users.php?page=$page");
    exit();
}

// Menghapus akun pengguna
if (isset($_GET['delete_user'])) {
    $delete_id = $_GET['delete_user'];
    mysqli_query($conn, "DELETE FROM users WHERE id = '$delete_id'") or die('Query gagal');
    header("location:admin_users.php?page=$page");
    exit();
}

// Ambil data pengguna untuk "Ubah"
$edit_user_data = null;
if (isset($_GET['edit_user'])) {
    $edit_user_id = $_GET['edit_user'];
    $edit_user_query = mysqli_query($conn, "SELECT * FROM users WHERE id = '$edit_user_id'") or die('Query gagal');
    $edit_user_data = mysqli_fetch_assoc($edit_user_query);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Pengguna</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/admin_style.css">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 10px;
            text-align: center;
            border: 1px solid #ddd;
        }

        th {
            background-color: #f39c12;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        /* Popup Form Styles */
        .popup-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            display: none; /* Will be set to flex when shown */
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .popup-form {
            background: #fff;
            padding: 20px 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            width: 400px;
            text-align: center;
        }

        .popup-form h2 {
            font-size: 1.8rem;
            margin-bottom: 20px;
            color: #333;
        }

        .popup-form .box {
            width: 100%;
            padding: 10px 15px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1.2rem;
            background: #f9f9f9;
        }

        .popup-form .custom-label {
            display: block;
            text-align: left;
            margin-bottom: 5px;
            font-size: 1.2rem;
            color: #333;
        }

        .popup-form .form-actions {
            display: flex;
            justify-content: space-between;
            gap: 10px;
        }

        .popup-form .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: 1.2rem;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .popup-form .btn-update {
            background: #333;
            color: #fff;
        }

        .popup-form .btn-update:hover {
            background: #444;
        }

        .popup-form .btn-cancel {
            background: #f39c12;
            color: #fff;
        }

        .popup-form .btn-cancel:hover {
            background: #d87c05;
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
    <h1 class="title">Manajemen Pengguna</h1>

    <?php if ($edit_user_data): ?>
    <!-- Popup Form -->
    <div class="popup-overlay" id="popupOverlay" style="display: flex;">
    <div class="popup-form">
        <form action="" method="post">
            <h2>Ubah Pengguna</h2>
            <!-- Input ID pengguna -->
            <input type="hidden" name="update_user_id" value="<?php echo $edit_user_data['id']; ?>"> <!-- Ganti dengan ID pengguna -->
            <!-- Input untuk nama pengguna -->
            <input type="text" name="update_name" placeholder="Nama Pengguna" class="box" required value="<?php echo $edit_user_data['name']; ?>">
            <!-- Input untuk email pengguna -->
            <input type="email" name="update_email" placeholder="Email Pengguna" class="box" required value="<?php echo $edit_user_data['email']; ?>">
            <!-- Input untuk tipe akun -->
            <label for="user_type" class="custom-label">Tipe Akun</label>
            <select name="update_user_type" id="user_type" class="box" required>
                <option value="user" <?php if ($edit_user_data['user_type'] == 'user') echo 'selected'; ?>>User</option>
                <option value="admin" <?php if ($edit_user_data['user_type'] == 'admin') echo 'selected'; ?>>Admin</option>
            </select>
            <!-- Tombol aksi -->
            <div class="form-actions">
                <button type="submit" name="update_user" class="btn btn-update">Ubah</button>
                <button type="button" class="btn btn-cancel" id="closePopup">Batal</button>
            </div>
        </form>
    </div>
</div>
    <?php endif; ?>

    <table>
        <thead>
        <tr>
            <th>No</th>
            <th>Nama</th>
            <th>Email</th>
            <th>Tipe</th>
            <th>Aksi</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $select_users = mysqli_query($conn, "SELECT * FROM users LIMIT $limit OFFSET $offset") or die('Query gagal');
        $no = $offset + 1;
        while ($fetch_users = mysqli_fetch_assoc($select_users)) {
            ?>
            <tr>
                <td><?php echo $no++; ?></td>
                <td><?php echo htmlspecialchars($fetch_users['name']); ?></td>
                <td><?php echo htmlspecialchars($fetch_users['email']); ?></td>
                <td><?php echo htmlspecialchars($fetch_users['user_type']); ?></td>
                <td>
                    <a href="admin_users.php?edit_user=<?php echo $fetch_users['id']; ?>" class="option-btn">Ubah</a>
                    <a href="admin_users.php?delete_user=<?php echo $fetch_users['id']; ?>" class="delete-btn" onclick="return confirm('Hapus user ini?');">Hapus</a>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>

    <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="admin_users.php?page=<?php echo $page - 1; ?>">«</a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="admin_users.php?page=<?php echo $i; ?>" class="<?php echo ($page == $i) ? 'active' : ''; ?>"><?php echo $i; ?></a>
        <?php endfor; ?>

        <?php if ($page < $total_pages): ?>
            <a href="admin_users.php?page=<?php echo $page + 1; ?>">»</a>
        <?php endif; ?>
    </div>
</section>

<script>
// Menampilkan popup form ketika "Ubah" ditekan
const popupOverlay = document.getElementById('popupOverlay');
const closePopup = document.getElementById('closePopup');

if (popupOverlay) {
    popupOverlay.style.display = 'flex'; // Menampilkan popup
}

// Menutup popup jika tombol "Batal" ditekan
if (closePopup) {
    closePopup.addEventListener('click', () => {
        popupOverlay.style.display = 'none'; // Menyembunyikan popup
        window.location.href = 'admin_users.php'; // Redirect ke halaman utama
    });
}
</script>

<?php include 'admin_footer.php'; ?>
</body>
</html>
