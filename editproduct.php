<?php

include ("server.php");

session_start();

if (isset($_SESSION['productid'])) {
    $productId = $_SESSION['productid'];
    

     // Fetch product details
    $query = "SELECT * FROM product_info WHERE product_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $productId); // Bind the product ID to the query
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
    } else {
        echo "Product not found.";
        exit();
    }
} else {
    echo "No product ID provided.";
    exit();
}




// if (isset($_FILES['productImage']) && $_FILES['productImage']['error'] === UPLOAD_ERR_OK) {
//     $fileName = $_FILES['productImage']['name'];
//     // You can further validate file size or type here if needed
//     echo "/WSTFINALPROJ/imageholder/" . $fileName;
// } else {
//     echo "No file uploaded or there was an error.";



// /WSTFINALPROJ/IMAGES/no bg men/menperfume1 - png.png }

if (isset($_POST['save'])) {
    if (isset($_FILES['productImage']) && $_FILES['productImage']['error'] === UPLOAD_ERR_OK) {
        $fileName = $_FILES['productImage']['name'];
        $path = "/WSTFINALPROJ/imageholder/" . $fileName;
    } else {
        // Use session value for image if no file is uploaded
        $image = $_SESSION['image'];
        $path = $image;
        // echo "Default path is: " . $path . "<br>"; 
    }

    $productId = $_SESSION['productid'];
    $productName = $_POST['productName'];
    $productPrice = $_POST['productPrice'];
    $productDescription = $_POST['productDescription'];
    $productCategory = $_POST['productCategory'];
    $productStock = $_POST['productStock'];
    

    // Output the results
    // echo "Product ID: " . htmlspecialchars($productId) . "<br>";
    // echo "Product Name: " . htmlspecialchars($productName) . "<br>";
    // echo "Price: " . htmlspecialchars($productPrice) . "<br>";
    // echo "Description: " . htmlspecialchars($productDescription) . "<br>";
    // echo "Category: " . htmlspecialchars($productCategory) . "<br>";
    // echo "Stock: " . htmlspecialchars($productStock) . "<br>";
    // echo "Image: " . htmlspecialchars($path) . "<br>";

    // Prepare the SQL query to update the product_info table
    $sql = "UPDATE product_info 
            SET product_name = ?, price = ?, description = ?, category = ?, stocks = ?, image = ?
            WHERE product_id = ?";

    $stmt = $conn->prepare($sql);
    if ($stmt) {
        // Bind parameters to the query
        $stmt->bind_param("sissisi", $productName, $productPrice, $productDescription, $productCategory, $productStock, $path, $productId);

        // Execute the query
        if ($stmt->execute()) {
            // echo "Product information updated successfully!";
        } else {
            echo "Error updating product information: " . $stmt->error;
        }
    } else {
        echo "Error preparing statement: " . $conn->error;
    }

    echo "<script>
    document.addEventListener('DOMContentLoaded', function () {
        const notifdiv = document.getElementById('notif');
        notifdiv.style.display = 'block';
        
        // Redirect to admin.php after 3 seconds
        setTimeout(function () {
            window.location.href = 'admin.php';
        }, 1000);
    });
</script>";

    
}

if(isset($_POST['delete'])){

    $productId = $_SESSION['productid']; // Retrieve the product ID from the session

    // Prepare the SQL query to delete the product
    $sql = "DELETE FROM product_info WHERE product_id = ?";

    $stmt = $conn->prepare($sql);
    if ($stmt) {
        // Bind the product ID to the query
        $stmt->bind_param("i", $productId);

        // Execute the query
        if ($stmt->execute()) {
            // Notify the user and redirect
            echo "<script>
                alert('Product deleted successfully!');
                window.location.href = 'admin.php';
            </script>";
        } else {
            echo "Error deleting product: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Error preparing statement: " . $conn->error;
    }
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    <link rel="stylesheet" href="men.css">
    <link rel="stylesheet" href="CSS JS/Bootstrap/css/bootstrap.min.css" />
    <link rel="stylesheet" href="CSS JS/Font Awesome/css/font-awesome.min.css" />
    <style>
        /* Basic styling for layout */
        body {
            font-family: Arial, sans-serif;
            background-color: #F0E5DF;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .edit-product-form {
            background-color: rgba(243, 205, 204, 0.4); /* #F3CDCC with 40% opacity */
            padding: 30px;
            width: 625px; /* 25% wider than the previous 500px */
            border-radius: 10px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
            margin-top: 5%; /* Added margin to separate header and form */
        }

        .edit-product-form h2 {
            text-align: center;
            margin-bottom: 25px;
            font-size: 1.8em;
            color: #B06F69;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #B06F69;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            background-color: #F0E5DF;
            border: 1px solid #B06F69;
            border-radius: 5px;
            color: #333;
            font-size: 1em;
        }

        .form-actions {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
        }

        .form-actions button {
            padding: 12px 25px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            font-size: 1em;
        }

        .btn-save {
            background-color: #F3CDCC;
            color: #B06F69;
        }

        .btn-cancel {
            background-color: #B06F69;
            color: #F3CDCC;
        }

        .image-preview {
            text-align: center;
            margin-bottom: 20px;
        }

        .image-preview img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        
        .container .notif {
        font-family: rokkit;
        width: 350px;
        height: 350px;
        border-radius: 10px;
        z-index: 5;
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%); 
        justify-content: center;
        align-items: center;
        background-color: #F3CDCC;
        padding: 10px;
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.5);
        }

        .notif_text{

        text-align: center;
        font-size: 40px;
        margin-bottom: 1%;
        }


        .notif_btn {
        display: flex;
        justify-content: center; 
        align-items: center; 
        
        }

        .notif_btn button {
        width: 100px;
        height: 50px;
        align-items: center;
        justify-content: center;
        text-align: center;
        text-transform: uppercase;
        transition: 0.5s;
        background-size: 200% auto;
        color: white;
        border-radius: 10px;
        display: block;
        border: 0px;
        font-weight: 700;
        box-shadow: 0px 0px 14px -7px #f09819;
        background-image: linear-gradient(45deg, #FF512F 0%, #F09819  51%, #FF512F  100%);
        cursor: pointer;
        user-select: none;
        -webkit-user-select: none;
        touch-action: manipulation;
        }

        .btn:hover {
        background-position: right center;
        color: #fff;
        text-decoration: none;
        }

        .btn:active {
        transform: scale(0.95);
        }

    </style>
</head>
<body>

    <!-- Edit Product Form -->
    <div class="edit-product-form">
        <h2>Edit Product</h2>
        
        <form method="POST" enctype="multipart/form-data">

    <!-- Display the current product image -->
    <div class="image-preview">
        <div>Current Product Image:</div>
        <img id="currentProductImage" src="<?php echo htmlspecialchars($product['image']); ?>" alt="Current Product Image" width = "300px" height = "400px">
        
    </div>

    <div class="form-group">
        <label for="productName">Product Name:</label>
        <input type="text" id="productName" name="productName" value="<?php echo htmlspecialchars($product['product_name']); ?>" required>
    </div>
    <div class="form-group">
        <label for="productPrice">Price:</label>
        <input type="number" id="productPrice" name="productPrice" step="0.01" value="<?php echo htmlspecialchars($product['price']); ?>" required>
    </div>
    <div class="form-group">
        <label for="productDescription">Description:</label>
        <textarea id="productDescription" name="productDescription" rows="4"><?php echo htmlspecialchars($product['description']); ?></textarea>
    </div>
    <div class="form-group">
    <label for="productCategory">Category:</label>
    <select id="productCategory" name="productCategory" required>
        <option value="Female" <?php echo $product['category'] === 'Female' ? 'selected' : ''; ?>>Female</option>
        <option value="Male" <?php echo $product['category'] === 'Male' ? 'selected' : ''; ?>>Male</option>
    </select>
</div>
    <div class="form-group">
        <label for="productStock">Stock:</label>
        <input type="number" id="productStock" name="productStock" value="<?php echo htmlspecialchars($product['stocks']); ?>" required>
    </div>
    <div class="form-group">
        <label for="productImage">Upload New Image:</label>
        <input type="file" id="productImage" name="productImage" accept="image/*">
        
    </div>
    <div class="form-actions">
        <button type="submit" class="btn-save" name= "save">Save Changes</button>
        <button type="submit" class="btn-save" name= "delete">Delete item</button>
        <button type="button" id= "cancel-btn" class="btn-cancel" >Cancel</button>

    </div>
</form>
    </div>

        <div class="container">
                <div class="notif" id="notif" style = "display: none;">
                <div class="notif_text">
                    Product Updated!
                </div>
                
                <div class="notif_btn">
                    
                    <button class="btn" id="btn" name = "okay">Okay</button>
                </div>
                
                </div>

            


    <script>
    
        document.getElementById('cancel-btn').addEventListener('click', function () {
            window.history.back();
        });

        const productImageInput = document.getElementById('productImage');
        const currentProductImage = document.getElementById('currentProductImage');

productImageInput.addEventListener('change', function () {
    const file = this.files[0]; // Get the selected file
    if (file) {
        console.log("File name:", file.name); // Logs the file name
        console.log("File type:", file.type); // Logs the file type
        console.log("File size:", file.size); // Logs the file size (in bytes)

        // Create a preview of the new image
        const reader = new FileReader();
        reader.onload = function (e) {
            // Set the `src` attribute of the currentProductImage to the new image
            currentProductImage.src = e.target.result;
        };
        reader.readAsDataURL(file); // Convert the file to a data URL for preview
    }
});

            document.addEventListener('DOMContentLoaded', function () {
            const okbtn = document.getElementById('btn');
            const notifdiv = document.getElementById('notif');

            if (okbtn) {
                okbtn.addEventListener('click', function () {
                   notifdiv.style.display = 'none';
                });
            }
        });

        document.getElementById('btn').addEventListener('click', function () {
        window.location.href = 'admin.php';
    });

    </script>
</body>
</html>
