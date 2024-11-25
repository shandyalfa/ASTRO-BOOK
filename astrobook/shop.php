<?php
include 'config.php';

session_start();

// Pastikan user sudah login
if (!isset($_SESSION['user_id'])) {
    header('location:login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

$message = ''; // Variabel pesan untuk popup
$icon = ''; // Variabel ikon untuk popup

// Logika untuk menambahkan produk ke keranjang
if (isset($_POST['add_to_cart'])) {
    $product_name = $_POST['product_name'] ?? '';
    $product_price = $_POST['product_price'] ?? 0;
    $product_image = $_POST['product_image'] ?? '';
    $product_quantity = $_POST['product_quantity'] ?? 1;

    // Validasi input
    if (empty($product_name) || empty($product_price) || empty($product_image)) {
        $message = 'Data produk tidak lengkap!';
        $icon = 'fa-exclamation-circle'; // Ikon peringatan
    } else {
        // Periksa apakah produk sudah ada di keranjang
        $check_cart_numbers = mysqli_query($conn, "SELECT * FROM `cart` WHERE name = '$product_name' AND user_id = '$user_id'") or die('Query gagal: ' . mysqli_error($conn));

        if (mysqli_num_rows($check_cart_numbers) > 0) {
            $message = 'Produk sudah ada di keranjang!';
            $icon = 'fa-times-circle'; // Ikon error
        } else {
            // Tambahkan produk ke keranjang
            $insert_query = "INSERT INTO `cart`(user_id, name, price, quantity, image) VALUES('$user_id', '$product_name', '$product_price', '$product_quantity', '$product_image')";
            $result = mysqli_query($conn, $insert_query);

            if ($result) {
                $message = 'Produk berhasil ditambahkan ke keranjang!';
                $icon = 'fa-check-circle'; // Ikon sukses
            } else {
                $message = 'Gagal menambahkan produk ke keranjang!';
                $icon = 'fa-exclamation-circle'; // Ikon peringatan
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">

    <style>
        /* Loader Style */
        .loader {
            text-align: center;
            margin: 20px 20px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .loader img {
            width: 50px;
        }

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

        .popup {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) scale(0.8);
            background: #ffffff;
            box-shadow: 0px 15px 40px rgba(0, 0, 0, 0.3);
            border-radius: 15px;
            padding: 30px;
            width: 90%;
            max-width: 400px;
            text-align: center;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease, transform 0.3s ease, visibility 0.3s ease;
            z-index: 1010;
        }

        .popup .icon {
            font-size: 50px;
            margin-bottom: 15px;
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

        /* Active State */
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
<?php include 'header.php'; ?>

<?php if ($message): ?>
    <!-- Popup -->
    <div class="popup-overlay active" id="popupOverlay"></div>
    <div class="popup active" id="popup">
        <div class="icon <?php echo $icon === 'fa-check-circle' ? 'success' : ($icon === 'fa-times-circle' ? 'error' : 'warning'); ?>">
            <i class="fas <?php echo $icon; ?>"></i>
        </div>
        <h2><?php echo $icon === 'fa-check-circle' ? 'Berhasil!' : ($icon === 'fa-times-circle' ? 'Gagal!' : 'Peringatan!'); ?></h2>
        <p><?php echo $message; ?></p>
        <button id="closePopup">Tutup</button>
    </div>
<?php endif; ?>

<div class="heading">
    <h3>Shop Produk</h3>
    <p><a href="home.php">Beranda</a> / Shop</p>
</div>

<section class="products">
    <h1 class="title">Produk Terbaru</h1>
    <div class="box-container" id="productContainer">
        <!-- Produk akan dimuat secara dinamis -->
    </div>
    <div id="loader" class="loader" style="display: none;">
        <img src="uploaded_img/loader.gif" alt="Loading...">
    </div>
</section>

<?php include 'footer.php'; ?>

<script>
document.addEventListener("DOMContentLoaded", () => {
    let page = 1; // Halaman awal
    const productContainer = document.getElementById("productContainer");
    const loader = document.getElementById("loader");
    let isLoading = false;
    let hasMore = true;

    const loadProducts = async () => {
        if (isLoading || !hasMore) return;
        isLoading = true;
        loader.style.display = "flex"; // Tampilkan animasi loading

        try {
            const response = await fetch(`load_products.php?page=${page}`);
            if (!response.ok) {
                throw new Error("Gagal memuat produk");
            }

            const productsHTML = await response.text();
            if (productsHTML.trim() === "") {
                hasMore = false; // Tidak ada produk lagi
            } else {
                productContainer.insertAdjacentHTML("beforeend", productsHTML);
                page++;
            }
        } catch (error) {
            console.error("Error:", error.message);
        }

        loader.style.display = "none"; // Sembunyikan animasi loading
        isLoading = false;
    };

    const handleScroll = () => {
        const { scrollTop, scrollHeight, clientHeight } = document.documentElement;
        if (scrollTop + clientHeight >= scrollHeight - 10) {
            loadProducts();
        }
    };

    // Muat produk pertama kali
    loadProducts();

    // Tambahkan event listener untuk scroll
    window.addEventListener("scroll", handleScroll);
});

// Close Popup
document.addEventListener("DOMContentLoaded", () => {
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
});
</script>

</body>
</html>
