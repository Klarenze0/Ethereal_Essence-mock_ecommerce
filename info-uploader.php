<?php

include ("server.php");

   // Query the database
$result = $conn->query("SELECT * FROM product_info");


// Fetch and display the data
while ($row = $result->fetch_assoc()) {
    echo "<h3>" . $row['product_id'] . "</h3>";
    echo "<h3>" . $row['product_name'] . "</h3>";
    echo "<h3>" . $row['category'] . "</h3>";
    echo "<p>" . $row['description'] . "</p>";
    echo "<p>" . $row['stocks'] . "</p>";
    echo "<p>Price: P " . $row['price'] . "</p>";

    echo "<img src='" . $row['image'] . "' alt='Product Image' style='width:200px;height:auto;'><br>";
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    
    <form action="your_action_page.php" method="POST" enctype="multipart/form-data">
        <label for="product_name">Product Name:</label>
        <input type="text" name="product_name" id="product_name">
        <br>
    
        <label for="category">Category:</label>
        <input type="text" name="category" id="category">
        <br>
    
        <label for="description">Description:</label>
        <input type="text" name="description" id="description">
        <br>
    
        <label for="stocks">Stocks:</label>
        <input type="number" name="stocks" id="stocks">
        <br>
    
        <label for="price">Price:</label>
        <input type="number" name="price" id="price">
        <br>
    
        <label for="image">Image:</label>
        <input type="file" name="image" id="image">
        <br>
    
        <button type="submit">Submit</button>
    </form>
    
    
</body>
</html>