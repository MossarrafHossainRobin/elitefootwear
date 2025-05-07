<?php
$products = [];
if (file_exists('products.json')) {
    $products = json_decode(file_get_contents('products.json'), true);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard - Elite Footwear</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&family=Playfair+Display:wght@700&display=swap" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    html {
      scroll-behavior: smooth;
    }

    .navbar {
      background: linear-gradient(90deg, #fc466b, #3f5efb);
    }

    .navbar .navbar-brand {
      font-weight: bold;
      font-size: 1.5rem;
      color: white;
    }

    .navbar .navbar-brand img {
      width: 45px;
      height: 45px;
      border-radius: 50%;
      padding: 4px;
      background: conic-gradient(#fc466b, #3f5efb, #fc466b);
    }

    .navbar .nav-link,
    .btn {
      color: white;
    }

    .navbar .nav-link:hover,
    .btn:hover {
      color: #ffe;
    }

    .main-content {
      padding: 40px 20px;
      background: linear-gradient(145deg, #fefefe, #f2f6ff);
      min-height: 100vh;
    }

    .product-card {
      border-radius: 12px;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      box-shadow: 0 6px 18px rgba(0, 0, 0, 0.1);
      overflow: hidden;
      background: white;
    }

    .product-card:hover {
      transform: scale(1.03);
      box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15);
    }

    .product-img {
      height: 220px;
      width: 100%;
      object-fit: cover;
    }

    .card-body h5 {
      font-weight: 600;
    }

    .price-tag {
      font-weight: bold;
      color: #e63946;
    }

    .add-product-link {
      position: fixed;
      bottom: 20px;
      right: 20px;
      z-index: 999;
    }
  </style>
</head>

<body>

  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg sticky-top">
    <div class="container">
      <a class="navbar-brand d-flex align-items-center gap-2" href="#">
        <img src="elite.png" alt="Elite Logo">
        Elite Footwear
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse justify-content-end" id="navbarMain">
        <form class="d-flex me-3" role="search">
          <input class="form-control form-control-sm" type="search" placeholder="Search stock..." aria-label="Search">
        </form>
        <div class="d-flex gap-2">
          <a href="login.html" class="btn btn-outline-light">Login</a>
          <a href="register.html" class="btn btn-light text-dark">Register</a>
        </div>
      </div>
    </div>
  </nav>

  <!-- Main Content -->
  <div class="container main-content">
    <div class="row" id="product-list">
      <?php if (!empty($products)): ?>
        <?php foreach ($products as $product): ?>
          <div class="col-lg-4 col-md-6 mb-4">
            <div class="card product-card">
              <img src="<?= htmlspecialchars($product['image']) ?>" class="product-img" alt="Product Image">
              <div class="card-body">
                <h5 class="card-title"><?= htmlspecialchars($product['name']) ?></h5>
                <p class="card-text"><?= htmlspecialchars($product['description']) ?></p>
                <p class="price-tag">Price: $<?= htmlspecialchars($product['price']) ?></p>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="col-12 text-center">
          <p class="text-muted">No products available. Add some from the employee panel.</p>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- Add Product Button -->
  <a href="add-product.php" class="btn btn-primary add-product-link">+ Add Product</a>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
