<?php
// File: manage_products.php
$file = 'products.json';
$products = file_exists($file) ? json_decode(file_get_contents($file), true) : [];

// Auto-incrementing product ID starting from 100
$lastId = 100;
if (!empty($products)) {
    $ids = array_column($products, 'id');
    $numericIds = array_filter($ids, fn($id) => is_numeric($id));
    if (!empty($numericIds)) {
        $lastId = max($numericIds) + 1;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update'])) {
        $id = $_POST['id'];
        foreach ($products as &$product) {
            if ($product['id'] == $id) {
                $product['name'] = $_POST['name'];
                $product['price'] = $_POST['price'];
                $product['description'] = $_POST['description'];
                $product['entry_time'] = $_POST['entry_time'];
                $product['days'] = date('l', strtotime($_POST['entry_time']));
                $product['quantity'] = $_POST['quantity'];
                $product['size'] = $_POST['size'];
                $product['buying_price'] = $_POST['buying_price'];
                break;
            }
        }
        unset($product);
    } elseif (isset($_POST['delete'])) {
        $id = $_POST['id'];
        $products = array_filter($products, fn($p) => $p['id'] != $id);
    } else {
        foreach ($_POST['name'] as $index => $name) {
            if (!empty($name)) {
                $id = $lastId++;
                $price = $_POST['price'][$index];
                $description = $_POST['description'][$index];
                $entry_time = $_POST['entry_time'][$index];
                $day_name = date('l', strtotime($entry_time));
                $quantity = $_POST['quantity'][$index];
                $size = $_POST['size'][$index];
                $buying_price = $_POST['buying_price'][$index];
                $imagePath = '';

                if (isset($_FILES['image']['name'][$index]) && $_FILES['image']['error'][$index] === UPLOAD_ERR_OK) {
                    $uploadDir = 'uploads/';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }
                    $imageName = basename($_FILES['image']['name'][$index]);
                    $targetFile = $uploadDir . uniqid() . '_' . $imageName;
                    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
                    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];

                    if (in_array($imageFileType, $allowedTypes)) {
                        if (move_uploaded_file($_FILES['image']['tmp_name'][$index], $targetFile)) {
                            $imagePath = $targetFile;
                        }
                    }
                }

                $products[] = [
                    'id' => $id,
                    'name' => $name,
                    'price' => $price,
                    'description' => $description,
                    'image' => $imagePath,
                    'entry_time' => $entry_time,
                    'days' => $day_name,
                    'quantity' => $quantity,
                    'size' => $size,
                    'buying_price' => $buying_price
                ];
            }
        }
    }
    file_put_contents($file, json_encode($products, JSON_PRETTY_PRINT));
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

$colorMap = [];
$availableColors = ['bg-light', 'bg-secondary', 'bg-white', 'bg-light', 'bg-light-subtle'];
$colorIndex = 0;
foreach ($products as $product) {
    $name = $product['name'];
    if (!isset($colorMap[$name])) {
        $colorMap[$name] = $availableColors[$colorIndex % count($availableColors)];
        $colorIndex++;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Elite Footwear - Product Manager</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #f4f7fc;
            font-family: 'Roboto', sans-serif;
        }
        .logo {
            width: 60px;
            height: 60px;
            border-radius: 50%;
        }
        .logo-link {
            text-decoration: none;
        }
        th, td {
            resize: horizontal;
            overflow: auto;
        }
        .shadow-box {
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }
        table.table-bordered th {
            background-color: #343a40;
            color: white;
        }
        table.table-bordered td {
            vertical-align: middle;
        }
        textarea.form-control {
            resize: vertical;
        }
    </style>
</head>
<body>
<div class="container py-4">
    <div class="d-flex align-items-center mb-4">
        <a href="#" class="logo-link">
            <img src="elite.png" class="logo me-3">
        </a>
        <h2 class="text-dark">Elite Footwear - Product Management</h2>
    </div>

    <input type="text" id="searchBar" class="form-control shadow-box mb-3" placeholder="Search by Product ID or Name">

    <form method="POST" enctype="multipart/form-data">
        <table class="table table-bordered shadow-box">
            <thead>
            <tr>
                <th>ID</th><th>Name</th><th>Price</th><th>Image</th><th>Description</th>
                <th>Entry Time</th><th>Day</th><th>Qty</th><th>Size</th><th>Buy Price</th><th>Action</th>
            </tr>
            </thead>
            <tbody id="product-rows">
                <tr>
                    <td>Auto</td>
                    <td><input type="text" name="name[]" class="form-control" required></td>
                    <td><input type="number" name="price[]" class="form-control" required></td>
                    <td><input type="file" name="image[]" class="form-control" accept="image/*" required></td>
                    <td><textarea name="description[]" class="form-control" rows="1" required></textarea></td>
                    <td><input type="datetime-local" name="entry_time[]" class="form-control" required></td>
                    <td><input type="text" class="form-control" disabled placeholder="Auto"></td>
                    <td><input type="number" name="quantity[]" class="form-control" required></td>
                    <td><input type="text" name="size[]" class="form-control" required></td>
                    <td><input type="number" name="buying_price[]" class="form-control" required></td>
                    <td class="text-center"></td>
                </tr>
            </tbody>
        </table>

        <div class="text-center">
            <button type="button" class="btn btn-outline-success me-2" onclick="addRow()">Add Row</button>
            <button type="submit" class="btn btn-outline-primary">Save Products</button>
        </div>
    </form>

    <hr class="my-4">
    <h4 class="text-dark">Product List</h4>
    <table class="table table-bordered table-hover bg-white shadow-box">
        <thead>
        <tr>
            <th>ID</th><th>Name</th><th>Price</th><th>Image</th><th>Description</th>
            <th>Entry</th><th>Day</th><th>Qty</th><th>Size</th><th>Buy Price</th><th>Action</th>
        </tr>
        </thead>
        <tbody id="display-table">
        <?php foreach ($products as $p): ?>
            <tr class="<?= $colorMap[$p['name']] ?>">
                <form method="POST">
                    <input type="hidden" name="id" value="<?= $p['id'] ?>">
                    <td><?= $p['id'] ?></td>
                    <td><input type="text" name="name" class="form-control" value="<?= htmlspecialchars($p['name']) ?>"></td>
                    <td><input type="number" name="price" class="form-control" value="<?= htmlspecialchars($p['price']) ?>"></td>
                    <td><img src="<?= $p['image'] ?>" width="50" height="50" class="rounded"></td>
                    <td><textarea name="description" class="form-control"><?= htmlspecialchars($p['description']) ?></textarea></td>
                    <td><input type="datetime-local" name="entry_time" class="form-control" value="<?= date('Y-m-d\TH:i', strtotime($p['entry_time'])) ?>"></td>
                    <td><?= htmlspecialchars($p['days']) ?></td>
                    <td><input type="number" name="quantity" class="form-control" value="<?= htmlspecialchars($p['quantity']) ?>"></td>
                    <td><input type="text" name="size" class="form-control" value="<?= htmlspecialchars($p['size']) ?>"></td>
                    <td><input type="number" name="buying_price" class="form-control" value="<?= htmlspecialchars($p['buying_price']) ?>"></td>
                    <td class="text-center">
                        <button type="submit" name="update" class="btn btn-outline-success btn-sm">Save</button>
                        <button type="submit" name="delete" class="btn btn-outline-danger btn-sm">Delete</button>
                    </td>
                </form>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
    function addRow() {
        const row = document.querySelector('#product-rows tr');
        const newRow = row.cloneNode(true);
        newRow.querySelectorAll('input, textarea').forEach(el => el.value = '');
        document.querySelector('#product-rows').appendChild(newRow);
    }

    document.getElementById('searchBar').addEventListener('keyup', function () {
        let val = this.value.toLowerCase();
        document.querySelectorAll('#display-table tr').forEach(row => {
            row.style.display = row.textContent.toLowerCase().includes(val) ? '' : 'none';
        });
    });
</script>
</body>
</html>
