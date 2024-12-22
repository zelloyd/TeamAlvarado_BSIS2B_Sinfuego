<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homepage</title>

    <!-- Link to Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }

        /* Navbar Styling */
        .navbar {
            width: 85%;
            margin: 20px auto;
            padding: 15px 35px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background-color: #ffffff;
            border-bottom: 2px solid #e0e0e0;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        /* Left-Aligned Navbar */
        .navbar .left-links ul {
            padding: 0;
            margin: 0;
            list-style: none;
            display: flex;
        }

        .navbar .left-links ul li {
            margin-right: 15px;
        }

        /* Centered Logo */
        .center-logo {
            font-size: 24px;
            font-weight: bold;
            text-transform: uppercase;
            color: #009688;
            text-decoration: none;
        }

        /* Right-Aligned Buttons */
        .navbar .right-links {
            display: flex;
            gap: 20px;
            align-items: center;
        }

        .navbar ul li a {
            text-decoration: none;
            color: #333;
            text-transform: uppercase;
            font-size: 16px;
            height: 20px;
            font-weight: bold;
            transition: color 0.3s ease;
        }

        .navbar ul li a:hover {
            color: #009688;
        }

        /* Button Styling */
        .cart-button, .profile-button, .logout-button {
            background-color: #009688;
            color: white;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 8px 12px;
            border-radius: 25px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .cart-button:hover,
        .profile-button:hover,
        .logout-button:hover {
            background-color: #00796b;
        }

        .cart-icon {
            width: 20px;
            height: 20px;
            margin-right: 5px;
        }

        .profile-icon {
            width: 30px;
            height: 30px;
            margin-right: 5px;
        }

        /* Content Styling */
        .content {
            text-align: center;
            color: black;
            margin: 50px auto;
            width: 90%;
        }

        .content h1 {
            font-size: 48px;
            color: #009688;
            margin-bottom: 20px;
        }

        .content p {
            font-size: 18px;
            line-height: 1.6;
            color: #555;
            margin-bottom: 30px;
        }

        .image-gallery {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
            margin-top: 20px;
        }

        .product {
            text-align: center;
        }

        .image-gallery img {
            width: 250px;
            height: 200px;
            object-fit: cover;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .image-gallery img:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 8px rgba(0, 0, 0, 0.2);
        }

        .price {
            font-size: 18px;
            font-weight: bold;
            color: #009688;
            margin-top: 10px;
        }

        footer {
            background-color: #f9f9f9;
            padding: 20px 50px;
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
        }

        .footer-column {
            margin: 10px;
        }

        .footer-column h4 {
            margin-bottom: 15px;
            font-size: 16px;
            font-weight: bold;
        }

        .footer-column ul {
            list-style: none;
            padding: 0;
        }

        .footer-column ul li {
            margin-bottom: 10px;
        }

        .footer-column ul li a {
            text-decoration: none;
            color: #333;
            font-size: 14px;
        }

        .footer-column ul li a:hover {
            color: #f60;
        }

        .payment-icons img, 
        .logistics-icons img {
            margin-right: 10px;
            width: 50px;
            height: auto;
            vertical-align: middle;
        }

        .social-icons a {
            margin-right: 15px;
            color: #333;
            font-size: 20px;
        }

        .social-icons a:hover {
            color: #f60;
        }

        .qr-section img {
            width: 100px;
            height: auto;
        }

        

        .footer-bottom {
            text-align: center;
            margin-top: 20px;
            font-size: 12px;
            color: #777;
        }

        /* Footer Wrapper for Flex Layout */
        .footer-sections {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            padding: 20px 50px;
        }

        /* Footer Bottom Centering */
        .footer-bottom {
            text-align: center;
            margin-top: 20px;
            padding: 10px 0;
            background-color: #f9f9f9;
            font-size: 12px;
            color: #777;
            width: 100%;
        }
    </style>
</head>
<body>
    <main>
        <!-- Navbar -->
        <div class="navbar">
            <!-- Left-Aligned: Home Button -->
            <div class="left-links">
                <ul>
                    <li><a href="homepage.php">Home</a></li>
                </ul>
            </div>

            <!-- Center: Zaikicks Logo -->
            <a href="#" class="center-logo">Zaikicks Mall</a>

            <!-- Right-Aligned: Links and Buttons -->
            <div class="right-links">

            <div class="left-links">
                <ul>
                    <li><a href="shoppingvisitor.php">Products</a></li>
                </ul>
            </div>

                <!-- Cart Button -->
                <button class="cart-button" onclick="window.location.href='registration.php';">
                    <img src="shopping-cart.png" alt="Cart" class="cart-icon">Cart
                </button>

                <!-- Logout Button -->
                <button class="profile-button" onclick="window.location.href='registration.php';">
                    <img src="icon.jpeg" alt="Icon" class="profile-icon">
                </button>

                
            </div>
        </div>

        <!-- Content Section -->
        <div class="content">
            <h1>Welcome to Zaikicks</h1>
            <p>Your go-to store for premium kicks and streetwear. Explore our services and find your perfect pair today!</p>
            
            <div class="image-gallery">
                <div class="product">
                    <img src="img1.jpg" alt="Product 1">
                    <p class="price">₱6,999</p> 
                </div>
                <div class="product">
                    <img src="img2.jpg" alt="Product 2">
                    <p class="price">₱6,999</p> 
                </div>
                <div class="product">
                    <img src="img3.jpg" alt="Product 3">
                    <p class="price">₱6,999</p>     
                </div>
                <div class="product">
                    <img src="img4.jpg" alt="Product 4">
                    <p class="price">₱6,999</p>    
                </div>
            </div>
        </div>
    </main>
    </footer
     <!-- Footer Section -->
     <footer>
    <div class="footer-sections">
        <div class="footer-column">
            <h4>About Us</h4>
            <ul>
                <p>Welcome to Zaikicks Mall, your ultimate destination for premium kicks and streetwear. At Zaikicks, we believe that style is a form of self-expression, and we are passionate about bringing you the best in trendy sneakers, clothing, and accessories.
        Founded with the goal of providing top-quality products to sneaker enthusiasts and fashion lovers, we curate a collection that combines comfort, style, and performance. Whether you're looking for the latest limited-edition sneakers or timeless classics, our carefully selected range offers something for everyone.
        Our mission is to create a shopping experience that’s as exciting and fresh as the styles we carry. We are committed to providing excellent customer service and making sure that your shopping experience with us is seamless from start to finish.
        Thank you for choosing Zaikicks Mall – where your style meets our passion.</p>
                <li><a href="#">Zaikicks Policies</a></li>
                <li><a href="#">Privacy Policy</a></li>
                <li><a href="#">Media Contact</a></li>
            </ul>
        </div>
        <div class="footer-column">
            <h4>Customer Service</h4>
            <ul>
                <li><a href="#">Help Centre</a></li>
                <li><a href="#">Payment Methods</a></li>
                <li><a href="#">Order Tracking</a></li>
                <li><a href="#">Free Shipping</a></li>
                <li><a href="#">Return & Refund</a></li>
            </ul>
        </div>
        <div class="footer-column">
            <h4>Payment</h4>
            <div class="payment-icons">
                <img src="gcash.png" alt="Gcash">
                <img src="paymaya.jpg" alt="PayMaya">
                <img src="paypal.png" alt="PayMaya">
            </div>
        </div>
        <div class="footer-column">
            <h4>Logistics</h4>
            <div class="logistics-icons">
                <img src="ninjavan.png" alt="Ninja Van">
                <img src="j&t.png" alt="J&T Express">
                <img src="2go.png" alt="2GO">
                <img src="flashexpress.png" alt="Flash Express">
            </div>
            </div>
        <div class="footer-column">
            <h4>Contact Us</h4>
            <div class="social-icons">
            <p>Zaikicks@gmail.com</p>
                <p>ZaikicksMall.com</p>
                <p>09675084271</p>
            </div>
        </div>
        <div class="footer-bottom">
            <p>Location</p>
            <p>Polangui, Albay, Philippines</p>
            <p>&copy; 2024 Zaikicks. All Rights Reserved.</p>
        </div>
</footer>

</body>
</html>
