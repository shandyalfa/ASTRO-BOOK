<?php
include 'config.php';

$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit = 3;
$offset = ($page - 1) * $limit;

$select_products = mysqli_query($conn, "SELECT * FROM `products` LIMIT $limit OFFSET $offset") or die('query failed');

?>
