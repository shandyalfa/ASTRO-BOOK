<?php
include 'config.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:login.php');
    exit();
}

// Jumlah data per halaman
$limit = 6;

// Ambil halaman saat ini, jika tidak ada set default ke halaman 1
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Menghitung total data dalam tabel authors
$total_users = mysqli_query($conn, "SELECT COUNT(*) AS total FROM `authors`") or die('query failed');
$total_users_count = mysqli_fetch_assoc($total_users)['total'];

// Menghitung total halaman
$total_pages = ceil($total_users_count / $limit);

// Tambah Penulis
if (isset($_POST['add_author'])) {
    $author_name = mysqli_real_escape_string($conn, $_POST['author_name']);
    $biography = mysqli_real_escape_string($conn, $_POST['biography']);
    $photo = $_FILES['photo']['name'];
    $photo_size = $_FILES['photo']['size'];
    $photo_tmp_name = $_FILES['photo']['tmp_name'];
    $photo_folder = 'uploaded_authors/' . $photo;

    $check_author = mysqli_query($conn, "SELECT * FROM `authors` WHERE name = '$author_name'") or die('Query failed');
    if (mysqli_num_rows($check_author) > 0) {
        $message[] = 'Penulis sudah ada!';
    } else {
        if ($photo_size > 2000000) {
            $message[] = 'Ukuran foto terlalu besar!';
        } else {
            move_uploaded_file($photo_tmp_name, $photo_folder);
            $insert_query = mysqli_query($conn, "INSERT INTO `authors`(name, biography, photo) VALUES('$author_name', '$biography', '$photo')") or die('Query failed');
            if ($insert_query) {
                $message[] = 'Penulis berhasil ditambahkan!';
            } else {
                $message[] = 'Gagal menambahkan penulis!';
            }
        }
    }
}

// Hapus Penulis
if (isset($_GET['delete_author'])) {
    $delete_id = $_GET['delete_author'];
    $fetch_photo = mysqli_query($conn, "SELECT photo FROM `authors` WHERE id = '$delete_id'") or die('Query failed');
    $photo_data = mysqli_fetch_assoc($fetch_photo);
    unlink('uploaded_authors/' . $photo_data['photo']);
    mysqli_query($conn, "DELETE FROM `authors` WHERE id = '$delete_id'") or die('Query failed');
    header('location:admin_setting.php');
    exit();
}

// Update Penulis
if (isset($_POST['update_author'])) {
    $update_author_id = $_POST['update_a_id'];
    $update_name = mysqli_real_escape_string($conn, $_POST['update_name']);
    $update_biography = mysqli_real_escape_string($conn, $_POST['update_biography']);

    // Update nama dan biografi penulis
    $update_query = mysqli_query($conn, "UPDATE authors SET name = '$update_name', biography = '$update_biography' WHERE id = '$update_author_id'") or die('Query gagal');

    // Periksa apakah ada gambar baru
    $update_photo = $_FILES['update_image']['name'];
    $update_photo_tmp_name = $_FILES['update_image']['tmp_name'];
    $update_photo_size = $_FILES['update_image']['size'];
    $update_folder = 'uploaded_authors/' . $update_photo;
    $update_old_photo = $_POST['update_old_image'];

    if (!empty($update_photo)) {
        if ($update_photo_size > 2000000) {
            $message[] = 'Ukuran foto terlalu besar!';
        } else {
            // Update foto di database hanya jika ada gambar baru
            mysqli_query($conn, "UPDATE authors SET photo = '$update_photo' WHERE id = '$update_author_id'") or die('Query gagal');
            move_uploaded_file($update_photo_tmp_name, $update_folder);
            unlink('uploaded_authors/' . $update_old_photo); // Hapus foto lama
        }
    }

    header('location:admin_setting.php');
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Penulis</title>
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
            background-color: #f39c12;
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

<?php if (isset($_GET['add-penulis'])) { ?>
<section class="add-penulis-form">
    <form action="" method="post" enctype="multipart/form-data">
        <h1 class="title">Tambah Penulis</h1>
        <input type="text" name="author_name" placeholder="Nama Penulis" required class="box">
        <textarea name="biography" placeholder="Biografi Penulis" rows="4" required class="box"></textarea>
        <div class="custom-file-upload">
            <label for="image" class="custom-label">Masukkan Gambar</label>
            <input id="image" type="file" name="photo" accept="image/jpg, image/jpeg, image/png" style="display: none;" required>
        </div>
        <input type="submit" value="Tambah" name="add_author" class="btn">
        <button type="reset" id="close-add-penulis" class="option-btn" onclick="window.location.href='admin_setting.php';">Batal</button>
    </form>
</section>
<?php } ?>

<section class="show-products">
    <div class="container-penulis">
        <h1 class="title">Daftar Penulis</h1>
        <a href="admin_setting.php?add-penulis=true" class="option-btn">Tambah Penulis</a>
    </div>
    <?php
        $limit = 5;
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $offset = ($page - 1) * $limit;
        $total_authors_query = mysqli_query($conn, "SELECT COUNT(*) AS total FROM authors") or die('Query failed');
        $total_authors = mysqli_fetch_assoc($total_authors_query)['total'];
        $total_pages = ceil($total_authors / $limit);

        $select_authors = mysqli_query($conn, "SELECT * FROM authors LIMIT $offset, $limit") or die('Query failed');
    ?>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Foto</th>
                <th>Nama</th>
                <th>Biografi</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
                if (mysqli_num_rows($select_authors) > 0) {
                    $no = $offset + 1;
                    while ($fetch_authors = mysqli_fetch_assoc($select_authors)) {
            ?>
            <tr>
                <td><?php echo $no++; ?></td>
                <td>
                    <img src="uploaded_authors/<?php echo $fetch_authors['photo']; ?>" alt="Foto Penulis" style="width: 100px; height: auto;">
                </td>
                <td><?php echo htmlspecialchars($fetch_authors['name']); ?></td>
                <td>
                    <?php 
                        echo !empty($fetch_authors['biography']) 
                            ? htmlspecialchars($fetch_authors['biography']) 
                            : "Biografi belum ada.";
                    ?>
                </td>
                <td>
                    <a href="admin_setting.php?update_penulis=<?php echo $fetch_authors['id']; ?>" class="option-btn">Ubah</a>
                    <a href="admin_setting.php?delete_author=<?php echo $fetch_authors['id']; ?>" class="delete-btn" onclick="return confirm('Hapus penulis ini?');">Hapus</a>
                </td>
            </tr>
            <?php
                    }
                } else {
                    echo '<tr><td colspan="5">Penulis Belum Ditambahkan!</td></tr>';
                }
            ?>
        </tbody>
    </table>

    <!-- Pagination -->
    <div class="pagination">
        <?php if ($page > 1) : ?>
            <a href="?page=<?php echo $page - 1; ?>">«</a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $total_pages; $i++) : ?>
            <a href="?page=<?php echo $i; ?>" class="<?php echo ($page == $i) ? 'active' : ''; ?>"><?php echo $i; ?></a>
        <?php endfor; ?>

        <?php if ($page < $total_pages) : ?>
            <a href="?page=<?php echo $page + 1; ?>">»</a>
        <?php endif; ?>
    </div>
</section>

<section class="edit-author-form">
    <?php
        if (isset($_GET['update_penulis'])) {
            $update_id = $_GET['update_penulis'];
            $update_query = mysqli_query($conn, "SELECT * FROM authors WHERE id = '$update_id'") or die('query failed');
            if (mysqli_num_rows($update_query) > 0) {
                while ($fetch_update = mysqli_fetch_assoc($update_query)) {
    ?>
    <form action="" method="post" enctype="multipart/form-data">
        <h1 class="title">Ubah Penulis</h1>
        <input type="hidden" name="update_a_id" value="<?php echo $fetch_update['id']; ?>">
        <input type="hidden" name="update_old_image" value="<?php echo $fetch_update['photo']; ?>">
        <img src="uploaded_authors/<?php echo $fetch_update['photo']; ?>" alt="">
        <input type="text" name="update_name" value="<?php echo $fetch_update['name']; ?>" class="box" required placeholder="Nama Penulis">
        <textarea name="update_biography" placeholder="Biografi Penulis" rows="4" required class="box"><?php echo $fetch_update['biography']; ?></textarea>
        <div class="custom-file-upload">
            <label for="image" class="custom-label">Masukkan Gambar</label>
            <input id="image" type="file" name="update_image" accept="image/jpg, image/jpeg, image/png" style="display: none;">
        </div>
        <input type="submit" value="Ubah" name="update_author" class="btn">
        <a href="admin_setting.php" class="option-btn">Batal</a>
    </form>
    <?php
                }
            }
        } else {
            echo '<script>document.querySelector(".edit-author-form").style.display = "none";</script>';
        }
    ?>
</section>

<script src="js/admin_script.js"></script>
<?php include 'admin_footer.php'; ?>

</body>
</html>
