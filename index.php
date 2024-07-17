<?php
session_start();
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "eduifer";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create table if not exists
$sql = "CREATE TABLE IF NOT EXISTS users (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(30) NOT NULL,
    email VARCHAR(50) NOT NULL,
    reg_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === FALSE) {
    die("Error creating table: " . $conn->error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verify CSRF token
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("CSRF token validation failed");
    }

    $name = htmlspecialchars($_POST['name']);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email format.");
    }

    // Use prepared statement to prevent SQL injection
    $stmt = $conn->prepare("INSERT INTO users (name, email) VALUES (?, ?)");
    $stmt->bind_param("ss", $name, $email);

    if ($stmt->execute()) {
        echo "<script>alert('Thank you, $name! Your information has been submitted.');</script>";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EDU-fier</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>

<body class="bg-white">
    <header class="relative bg-white">
        <nav class="fixed top-0 left-0 right-0 bg-white shadow-lg z-10 p-4">
            <div class="container mx-auto flex items-center justify-between">
                <img src="./src/images/Rectangle 1.png" alt="Logo" class="w-32 h-20">
                <div class="hidden md:flex items-center space-x-4">
                    <a href="#" class="text-gray-600 font-semibold hover:bg-gray-300 rounded-lg p-2 transition">Home</a>
                    <a href="#about" class="text-gray-600 font-semibold hover:bg-gray-300 rounded-lg p-2 transition">About</a>
                    <a href="#features" class="text-gray-600 font-semibold hover:bg-gray-300 rounded-lg p-2 transition">Features</a>
                    <a href="#contact" class="text-gray-600 font-semibold hover:bg-gray-300 rounded-lg p-2 transition">Contact</a>
                </div>
            </div>
        </nav>
        <div class="relative flex flex-col items-center justify-center h-screen pt-20 bg-cover bg-center" style="background-image: url('../switch/src/images/bg.png');">
            <div class="relative z-10 text-center bg-white bg-opacity-75 p-6 rounded-lg">
                <h1 class="text-4xl font-extrabold text-gray-800">Transform Learning with Gamification</h1>
                <p class="mt-4 text-gray-600">Enhance employee engagement and knowledge retention with EDU-fier</p>
            </div>
        </div>
    </header>

    <section id="about" class="bg-[#8E6FB2] py-12">
        <div class="container mx-auto text-center p-20">
            <h2 class="text-4xl font-bold mb-4">About EDU-fier</h2>
            <p class="text-gray-700 mb-6">
                EDU-fier is a gamified learning management system designed to transform the educational experience into an engaging journey.
            </p>
        </div>
    </section>

    <section id="features" class="py-20 bg-white">
        <div class="max-w-screen-xl mx-auto flex flex-wrap justify-center items-start gap-8">
            <div class="flex-1 h-full max-w-xs px-10 py-9 bg-white shadow-lg rounded-lg">
                <h2 class="text-center text-gray-800 text-lg font-semibold">Points and Rewards System</h2>
                <p class="text-center text-gray-500 text-base">Employees earn points and rewards for completing tasks and challenges.</p>
            </div>
            <div class="flex-1 h-full max-w-xs px-10 py-9 bg-white shadow-lg rounded-lg">
                <h2 class="text-center text-gray-800 text-lg font-semibold">Personalized Learning Experience</h2>
                <p class="text-center text-gray-500 text-base">The system customizes content based on employee needs.</p>
            </div>
            <div class="flex-1 h-full max-w-xs px-10 py-9 bg-white shadow-lg rounded-lg">
                <h2 class="text-center text-gray-800 text-lg font-semibold">Continuous Assessment</h2>
                <p class="text-center text-gray-500 text-base">Provides ongoing assessment tools and analytics to track performance.</p>
            </div>
        </div>
    </section>

    <section class="py-20 bg-white flex justify-center" id="contact">
        <div class="w-full max-w-screen-xl bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="p-6 text-center">
                <h2 class="text-4xl font-extrabold text-gray-800">Ready to get started?</h2>
                <p class="mt-4 text-lg text-gray-600">The world beckons; seize its grand offerings now!</p>
                <form method="POST" action="">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <div class="flex flex-col items-center mt-4">
                        <input type="text" name="name" placeholder="Your Name" class="w-80 mb-4 px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-blue-500" required />
                        <input type="email" name="email" placeholder="Your Email" class="w-80 mb-4 px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-blue-500" required />
                        <button type="submit" class="w-80 py-3 bg-[#8E6FB2] text-white rounded-md transition">Get Early Access!</button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <footer class="bg-gray-800 text-white py-6">
        <div class="container mx-auto flex justify-between items-center">
            <div class="flex space-x-4">
                <a href="#" class="hover:bg-gray-700 rounded-full p-2" title="Facebook">
                    <i class="fab fa-facebook-f"></i>
                </a>
                <a href="#" class="hover:bg-gray-700 rounded-full p-2" title="Twitter">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20px" height="20px">
                        <path fill="#ffffff" d="M12 2C6.48 2 2 6.48 2 12c0 5.03 3.66 9.16 8.44 9.87v-6.98H8.74v-2.89h1.7V9.54c0-1.68.6-2.83 2.02-2.83 1.1 0 1.75.08 1.75.08v1.93h-1.05c-1.03 0-1.35.64-1.35 1.29v1.53h2.29l-.37 2.89h-1.92v6.98C18.34 21.16 22 17.03 22 12c0-5.52-4.48-10-10-10z" />
                    </svg>
                </a>
                <a href="#" class="hover:bg-gray-700 rounded-full p-2" title="Instagram">
                    <i class="fab fa-instagram"></i>
                </a>
                <a href="#" class="hover:bg-gray-700 rounded-full p-2" title="LinkedIn">
                    <i class="fab fa-linkedin-in"></i>
                </a>
            </div>
            <div class="text-sm">
                © 2024 EDU-fier. All rights reserved.
            </div>
        </div>
    </footer>
</body>

</html>