<?php
include 'config.php';
session_start();

$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
    header('location:login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">

    <style>
        .placed-orders .box-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            flex-wrap: wrap;
            align-items: flex-start;
            gap: 1.5rem;
        }

        .placed-orders .box-container .box {
            flex: 1 1 40rem;
            border-radius: 10px;
            padding: 2rem;
            border: none;
            background-color: #ffffff;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .placed-orders .box-container .box:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        }

        .placed-orders .box-container .box h3 {
            font-size: 2.2rem;
            color: #007bff;
            border-bottom: 2px solid #ddd;
            padding-bottom: 1rem;
            margin-bottom: 1.5rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .placed-orders .box-container .box p {
            padding: 0.5rem 0;
            font-size: 1.6rem;
            color: #333;
            line-height: 1.5;
        }

        .placed-orders .box-container .box p span {
            color: #555;
            font-weight: bold;
        }

        .placed-orders .box-container .box p span.danger {
            color: red;
            font-weight: bold;
        }

        .placed-orders .box-container .box p span.success {
            color: green;
            font-weight: bold;
        }

        .empty {
            text-align: center;
            margin-top: 20px;
        }

        .empty img {
            max-width: 250px;
            height: auto;
            margin: 0 auto;
            display: block;
        }

        .empty-text {
            font-size: 1.8rem;
            color: #777;
            margin-top: 20px;
        }

        .empty .btn {
            margin-top: 15px;
            display: inline-block;
            padding: 10px 20px;
            background: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .empty .btn:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>

<?php include 'header.php'; ?>

<div class="heading">
    <h3>Pesanan Anda</h3>
    <p><a href="home.php">Beranda</a> / Pesanan</p>
</div>

<section class="placed-orders">
    <h1 class="title">Detail Pesanan Anda</h1>

    <div class="box-container">
        <?php
        $order_query = mysqli_query($conn, "SELECT * FROM `orders` WHERE user_id = '$user_id'") or die('Query failed');
        if (mysqli_num_rows($order_query) > 0) {
            while ($fetch_orders = mysqli_fetch_assoc($order_query)) {
        ?>
        <div class="box">
            <h3>Pesanan #<?php echo $fetch_orders['id']; ?></h3>
            <p><strong>Pesanan Ditempatkan:</strong> <span><?php echo $fetch_orders['placed_on']; ?></span></p>
            <p><strong>Nama:</strong> <span><?php echo $fetch_orders['name']; ?></span></p>
            <p><strong>No. Telepon:</strong> <span><?php echo $fetch_orders['number']; ?></span></p>
            <p><strong>Email:</strong> <span><?php echo $fetch_orders['email']; ?></span></p>
            <p><strong>Alamat:</strong> <span><?php echo $fetch_orders['address']; ?></span></p>
            <p><strong>Metode Pembayaran:</strong> <span><?php echo $fetch_orders['method']; ?></span></p>
            <p><strong>Pesanan Anda:</strong> <span><?php echo $fetch_orders['total_products']; ?></span></p>
            <p><strong>Total Harga:</strong> <span>Rp <?php echo number_format($fetch_orders['total_price'], 2, ',', '.'); ?></span></p>
            <?php if ($fetch_orders['account_number']): ?>
                <p><strong>No. Rekening:</strong> <span><?php echo $fetch_orders['account_number']; ?></span></p>
                <p><strong>Tenggat Pembayaran:</strong> <span><?php echo $fetch_orders['due_date']; ?></span></p>
            <?php endif; ?>
            <?php if ($fetch_orders['proof_of_payment']): ?>
                <p><strong>Bukti Pembayaran:</strong> <span><a href="uploaded_proof/<?php echo $fetch_orders['proof_of_payment']; ?>" target="_blank">Lihat Bukti</a></span></p>
            <?php endif; ?>
            <p><strong>Status Pembayaran:</strong> <span class="<?php echo $fetch_orders['payment_status'] == 'pending' ? 'danger' : 'success'; ?>"><?php echo ucfirst($fetch_orders['payment_status']); ?></span></p>
        </div>
        <?php
            }
        } else {
            echo '<div class="empty">
                <img src="uploaded_img/8551181.jpg" alt="Pesanan Kosong">
                <p class="empty-text">Belum ada pesanan yang dibuat!</p>
                <a href="shop.php" class="btn">Mulai Belanja</a>
            </div>';
        }
        ?>
    </div>
</section>

<?php include 'footer.php'; ?>

<script src="js/script.js"></script>
</body>
</html>
