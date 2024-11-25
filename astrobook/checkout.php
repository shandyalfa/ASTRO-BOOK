<?php

include 'config.php';

session_start();

// Validasi sesi pengguna
$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    header('location:login.php');
    exit;
}

$message = ''; // Variabel untuk pesan popup
$icon = '';    // Variabel untuk ikon popup

$cart_total = 0; // Inisialisasi total keranjang

if (isset($_POST['order_btn'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $number = $_POST['number'];
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $method = mysqli_real_escape_string($conn, $_POST['method']);
    $address = mysqli_real_escape_string($conn, 'No. ' . $_POST['flat'] . ', ' . $_POST['street'] . ', ' . $_POST['city'] . ', ' . $_POST['country'] . ' - ' . $_POST['pin_code']);
    $placed_on = date('Y-m-d');
    $due_date = date('Y-m-d', strtotime('+3 days'));

    // Tentukan nomor rekening berdasarkan metode pembayaran
    $account_number = match ($method) {
        'Dana' => '085362626262',
        'Gopay' => '081243434343',
        default => '1234567890',
    };

    $proof_of_payment = null;

    // Proses unggah bukti pembayaran
    if ($method !== 'cash on delivery' && isset($_FILES['proof_of_payment']['name']) && $_FILES['proof_of_payment']['name'] !== '') {
        $allowed_extensions = ['jpg', 'jpeg', 'png'];
        $proof_name = $_FILES['proof_of_payment']['name'];
        $proof_tmp_name = $_FILES['proof_of_payment']['tmp_name'];
        $proof_extension = strtolower(pathinfo($proof_name, PATHINFO_EXTENSION));

        // Validasi ekstensi file
        if (in_array($proof_extension, $allowed_extensions)) {
            $proof_folder = 'uploaded_proof/' . uniqid() . '.' . $proof_extension;

            // Memindahkan file ke folder tujuan
            if (move_uploaded_file($proof_tmp_name, $proof_folder)) {
                $proof_of_payment = basename($proof_folder);
            } else {
                $message = 'Gagal mengunggah file. Pastikan folder memiliki izin tulis.';
                $icon = 'fa-times-circle';
            }
        } else {
            $message = 'Hanya file gambar (JPG, JPEG, PNG) yang diperbolehkan!';
            $icon = 'fa-times-circle';
        }
    }

    // Hitung total keranjang dan jumlah produk
    $cart_query = mysqli_query($conn, "SELECT * FROM `cart` WHERE user_id = '$user_id'") or die('Query gagal: ' . mysqli_error($conn));
    $total_products = 0; // Inisialisasi jumlah total produk
    if (mysqli_num_rows($cart_query) > 0) {
        while ($cart_item = mysqli_fetch_assoc($cart_query)) {
            $cart_total += $cart_item['price'] * $cart_item['quantity'];
            $total_products += $cart_item['quantity']; // Tambahkan jumlah produk
        }
    }

    if ($cart_total == 0) {
        $message = 'Keranjang Anda kosong!';
        $icon = 'fa-times-circle';
    } else {
        // Insert ke tabel orders
        $query = "INSERT INTO `orders` (user_id, name, number, email, method, address, total_price, total_products, placed_on, due_date, account_number, proof_of_payment) 
                 VALUES ('$user_id', '$name', '$number', '$email', '$method', '$address', '$cart_total', '$total_products', '$placed_on', '$due_date', '$account_number', '$proof_of_payment')";

        if (mysqli_query($conn, $query)) {
            $message = 'Pesanan berhasil dikirim!';
            $icon = 'fa-check-circle';

            // Hapus keranjang setelah pesanan dibuat
            mysqli_query($conn, "DELETE FROM `cart` WHERE user_id = '$user_id'") or die('Query gagal: ' . mysqli_error($conn));
        } else {
            die('Query gagal: ' . mysqli_error($conn));
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Periksa Kembali</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">

    <style>
        .popup-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: none;
            z-index: 1000;
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
    </style>
</head>
<body>
<?php include 'header.php'; ?>

<!-- Popup -->
<?php if (!empty($message)): ?>
    <div class="popup-overlay" id="popupOverlay"></div>
    <div class="popup" id="popup">
        <div class="icon <?php echo $icon === 'fa-check-circle' ? 'success' : 'error'; ?>">
            <i class="fas <?php echo $icon; ?>"></i>
        </div>
        <h2><?php echo $message; ?></h2>
        <button id="closePopup">Tutup</button>
    </div>
<?php endif; ?>

<div class="heading">
    <h3>Periksa Kembali</h3>
    <p><a href="home.php">Beranda</a> / Periksa Kembali</p>
</div>

<section class="checkout">
    <form action="" method="post" enctype="multipart/form-data">
        <h3>Tempatkan Pesanan Anda</h3>
        <div class="flex">
            <div class="inputBox"><span>Nama Anda :</span><input type="text" name="name" required></div>
            <div class="inputBox"><span>No. Telepon Anda :</span><input type="number" name="number" required></div>
            <div class="inputBox"><span>Email Anda :</span><input type="email" name="email" required></div>
            <div class="inputBox"><span>Metode Pembayaran :</span>
                <select name="method">
                    <option value="cash on delivery">Bayar di tempat</option>
                    <option value="Kartu Kredit">Kartu Kredit</option>
                    <option value="Gopay">Gopay</option>
                    <option value="Dana">Dana</option>
                </select>
            </div>
            <div class="inputBox"><span>Alamat :</span><input type="text" name="flat" required></div>
            <div class="inputBox"><span>Jalan :</span><input type="text" name="street" required></div>
            <div class="inputBox"><span>Kota :</span><input type="text" name="city" required></div>
            <div class="inputBox"><span>Negara :</span><input type="text" name="country" required></div>
            <div class="inputBox"><span>Kode Pos :</span><input type="number" name="pin_code" required></div>
            <div class="inputBox"><span>No. Rekening untuk Transfer:</span><input type="text" id="accountNumber" readonly></div>
            <div class="inputBox" id="proofBox" style="display: none;">
                <span>Unggah Bukti Pembayaran:</span>
                <input type="file" name="proof_of_payment" accept="image/*">
            </div>
        </div>
        <input type="submit" value="Pesan Sekarang" class="btn" name="order_btn">
    </form>
</section>

<script>
    // Tampilkan popup jika ada pesan
    if (document.getElementById('popup')) {
        document.getElementById('popupOverlay').style.display = 'block';
        document.getElementById('popup').style.display = 'block';

        document.getElementById('closePopup').addEventListener('click', function () {
            document.getElementById('popupOverlay').style.display = 'none';
            document.getElementById('popup').style.display = 'none';
        });
    }

    document.querySelector('[name="method"]').addEventListener('change', function () {
        const method = this.value;
        const accountNumber = document.getElementById('accountNumber');
        const proofBox = document.getElementById('proofBox');
        const proofInput = proofBox.querySelector('input[name="proof_of_payment"]');

        if (method === 'cash on delivery') {
            accountNumber.value = ''; // Kosongkan nomor rekening
            proofBox.style.display = 'none'; // Sembunyikan input bukti pembayaran
            proofInput.removeAttribute('required'); // Hapus atribut required
        } else if (method === 'Dana') {
            accountNumber.value = '085362626262'; // Nomor Dana
            proofBox.style.display = 'block'; // Tampilkan input bukti pembayaran
            proofInput.setAttribute('required', 'true'); // Tambahkan atribut required
        } else if (method === 'Gopay') {
            accountNumber.value = '081243434343'; // Nomor Gopay
            proofBox.style.display = 'block';
            proofInput.setAttribute('required', 'true');
        } else {
            accountNumber.value = '1234567890'; // Nomor rekening default
            proofBox.style.display = 'block';
            proofInput.setAttribute('required', 'true');
        }
    });
</script>

<?php include 'footer.php'; ?>

</body>
</html>
