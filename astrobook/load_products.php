<?php
include 'config.php';

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 3; // Jumlah produk per halaman
$offset = ($page - 1) * $limit;

// Query produk berdasarkan halaman
$query = "SELECT * FROM `products` LIMIT $limit OFFSET $offset";
$result = mysqli_query($conn, $query);

if (!$result) {
    die('Query gagal: ' . mysqli_error($conn));
}

if (mysqli_num_rows($result) > 0) {
    while ($product = mysqli_fetch_assoc($result)) {
        echo '<form action="" method="post" class="box">';
        echo '<img class="image" src="uploaded_img/' . htmlspecialchars($product['image']) . '" alt="">';
        echo '<div class="name">' . htmlspecialchars($product['name']) . '</div>';
        echo '<div class="price">Rp ' . number_format($product['price'], 2, ',', '.') . '</div>';
        echo '<input type="number" min="1" name="product_quantity" value="1" class="qty">';
        echo '<input type="hidden" name="product_name" value="' . htmlspecialchars($product['name']) . '">';
        echo '<input type="hidden" name="product_price" value="' . $product['price'] . '">';
        echo '<input type="hidden" name="product_image" value="' . htmlspecialchars($product['image']) . '">';
        echo '<input type="submit" value="Tambahkan" name="add_to_cart" class="btn">';
        echo '</form>';
    }
} else {
    echo ''; // Tidak ada produk lagi
}
?>
