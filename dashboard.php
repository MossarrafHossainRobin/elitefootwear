<?php
$file = 'products.json';
$products = file_exists($file) ? json_decode(file_get_contents($file), true) : [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST['name'] as $index => $name) {
        if (!empty($name)) {
            $id = uniqid("PROD_");
            $price = $_POST['price'][$index];
            $description = $_POST['description'][$index];
            $entry_time = $_POST['entry_time'][$index];
            $days = $_POST['days'][$index];
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
                $targetFile = $uploadDir . uniqid() . "_" . $imageName;
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
                'days' => $days,
                'quantity' => $quantity,
                'size' => $size,
                'buying_price' => $buying_price
            ];
        }
    }

    file_put_contents($file, json_encode($products, JSON_PRETTY_PRINT));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Products - Elite Footwear</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background: linear-gradient(to right, #fbd3e9, #bb377d);
            font-family: 'Poppins', sans-serif;
        }
        .logo-circle {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            background: conic-gradient(#ff416c, #ff4b2b, #ff416c);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 10px;
        }
        .logo-circle img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            border: 2px solid white;
            object-fit: cover;
        }
        .product-form-table th, .product-form-table td {
            vertical-align: middle;
        }
        .btn-rounded {
            border-radius: 30px;
        }
        .shadow-box {
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }
        .navbar {
            background: linear-gradient(90deg, #f857a6, #ff5858);
            color: white;
        }
    </style>
</head>
<body>

<div class="container py-4">
    <div class="d-flex align-items-center mb-4">
        <div class="logo-circle">
            <img src="elite.png" alt="Elite Logo">
        </div>
        <h2 class="text-white">Elite Footwear Product Management</h2>
    </div>

    <div class="mb-3">
        <input type="text" class="form-control shadow-box" placeholder="Search Products...">
    </div>

    <form method="POST" enctype="multipart/form-data">
        <table class="table table-bordered table-striped table-light shadow-box product-form-table">
            <thead class="table-dark">
                <tr>
                    <th>Product ID</th>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Image</th>
                    <th>Description</th>
                    <th>Entry Time</th>
                    <th>Days</th>
                    <th>Quantity</th>
                    <th>Size</th>
                    <th>Buying Price</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="product-rows">
                <!-- Dynamic product rows will appear here -->
                <tr>
                    <td>Auto-generated</td>
                    <td><input type="text" name="name[]" class="form-control" required></td>
                    <td><input type="number" name="price[]" class="form-control" required></td>
                    <td><input type="file" name="image[]" class="form-control" accept="image/*" required></td>
                    <td><textarea name="description[]" class="form-control" rows="1" required></textarea></td>
                    <td><input type="datetime-local" name="entry_time[]" class="form-control" required></td>
                    <td><input type="text" name="days[]" class="form-control" required></td>
                    <td><input type="number" name="quantity[]" class="form-control" required></td>
                    <td><input type="text" name="size[]" class="form-control" required></td>
                    <td><input type="number" name="buying_price[]" class="form-control" required></td>
                    <td class="text-center">
                        <button type="button" class="btn btn-danger btn-sm delete-row btn-rounded">Delete</button>
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="text-center my-3">
            <button type="button" class="btn btn-success btn-rounded shadow-sm" onclick="addRow()">Add More Row</button>
            <button type="submit" class="btn btn-primary btn-rounded shadow-sm">Save Products</button>
        </div>
    </form>
</div>

<script>
    function addRow() {
        const row = document.querySelector('#product-rows tr');
        const newRow = row.cloneNode(true);
        newRow.querySelectorAll('input, textarea').forEach(input => input.value = '');
        document.querySelector('#product-rows').appendChild(newRow);
    }

    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('delete-row')) {
            const row = e.target.closest('tr');
            row.remove();
        }
    });
</script>

</body>
</html>
