<?php
session_start();
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

include_once('includes/db_conn.php');

// Create table if not exists
$sql = "CREATE TABLE IF NOT EXISTS users (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(30) NOT NULL,
    email VARCHAR(50) NOT NULL UNIQUE,
    reg_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === FALSE) {
    die("Error creating table: " . $conn->error);
}

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verify CSRF token
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("CSRF token validation failed");
    }

    $name = htmlspecialchars($_POST['name']);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid email format.";
    } else {
        // Check if email already exists
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $message = "This email is already registered.";
            echo "<script>alert('Error: This email is already registered.');</script>";
        } else {
            // Insert new user
            $stmt = $conn->prepare("INSERT INTO users (name, email) VALUES (?, ?)");
            $stmt->bind_param("ss", $name, $email);

            if ($stmt->execute()) {
                $message = "Thank you, $name! Your information has been submitted.";
                echo "<script>alert('Thank you, $name! Your information has been submitted.');</script>";
            } else {
                $message = "Error: " . $stmt->error;
            }
        }
        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>EDU-fier</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Jost:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="css/styles.css" />
    <link rel="stylesheet" href="css/custom.css" />
</head>

<body class="bg-white">
    <header class="relative bg-white">
        <nav class="fixed top-0 left-0 right-0 bg-white shadow-lg z-20 p-1">
            <div class="container mx-auto flex items-center justify-between">
                <img src="./src/images/Rectangle 1.png" alt="Logo" class="w-20" />
                <div class="hidden md:flex items-center space-x-4">
                    <a href="#hero" class="text-gray-600 font-semibold hover:bg-gray-300 rounded-lg p-2 transition">Home</a>
                    <a href="#about" class="text-gray-600 font-semibold hover:bg-gray-300 rounded-lg p-2 transition">About</a>
                    <a href="#features" class="text-gray-600 font-semibold hover:bg-gray-300 rounded-lg p-2 transition">Features</a>
                    <a href="#contact" class="text-gray-600 font-semibold hover:bg-gray-300 rounded-lg p-2 transition">Contact</a>
                </div>
            </div>
        </nav>
        <div id="hero" class="relative flex flex-col items-center justify-center h-screen pt-20 bg-cover bg-center" style="background-image: url('./src/images/bg.png')">
            <div class="relative z-10 text-center bg-white bg-opacity-75 p-6 rounded-lg">
                <h1 class="text-4xl font-extrabold text-gray-800">
                    Transform Learning with Gamification
                </h1>
                <p class="mt-4 text-gray-600">
                    Enhance employee engagement and knowledge retention with
                    EDU-fier
                </p>
            </div>
        </div>
    </header>

    <section id="about" class="bg-primary py-12">
        <div class="container mx-auto text-center p-20" style="max-width: 500px">
            <h2 class="text-4xl font-bold mb-4 text-white">
                About EDU-fier
            </h2>
            <p class="text-white">
                EDU-fier is a gamified learning management system designed
                to transform the educational experience into an engaging
                journey.
            </p>
        </div>
    </section>
    <section id="features" class="py-20 bg-white">
        <div class="max-w-screen-xl mx-auto flex flex-wrap justify-center items-start gap-8">
            <div class="flex-1 h-full max-w-xs px-10 py-9 bg-white shadow-lg rounded-lg">
                <div class="text-center mb-4">
                    <i class="fas fa-trophy text-4xl text-primary"></i>
                </div>
                <h2 class="text-center text-gray-800 text-lg font-semibold">
                    Points and Rewards System
                </h2>
                <p class="text-center text-gray-500 text-base">
                    Employees earn points and rewards for completing tasks
                    and challenges.
                </p>
            </div>
            <div class="flex-1 h-full max-w-xs px-10 py-9 bg-white shadow-lg rounded-lg">
                <div class="text-center mb-4">
                    <i class="fas fa-user-graduate text-4xl text-primary"></i>
                </div>
                <h2 class="text-center text-gray-800 text-lg font-semibold">
                    Personalized Learning Experience
                </h2>
                <p class="text-center text-gray-500 text-base">
                    The system customizes content based on employee needs.
                </p>
            </div>
            <div class="flex-1 h-full max-w-xs px-10 py-9 bg-white shadow-lg rounded-lg">
                <div class="text-center mb-4">
                    <i class="fas fa-chart-line text-4xl text-primary"></i>
                </div>
                <h2 class="text-center text-gray-800 text-lg font-semibold">
                    Continuous Assessment
                </h2>
                <p class="text-center text-gray-500 text-base">
                    Provides ongoing assessment tools and analytics to track
                    performance.
                </p>
            </div>
        </div>
    </section>

    <section class="py-20 bg-white flex justify-center" id="contact">
        <div class="w-full max-w-screen-xl bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="p-6 text-center">
                <h2 class="text-4xl font-extrabold text-gray-800">
                    Ready to get started?
                </h2>
                <p class="mt-4 text-lg text-gray-600">
                    Get your offers now!
                </p>
                <?php if (!empty($message)) : ?>
                    <p class="mt-4 text-lg <?php echo strpos($message, 'Thank you') !== false ? 'text-green-600' : 'text-red-600'; ?>">
                        <?php echo $message; ?>
                    </p>
                <?php endif; ?>
                <form method="POST" action="">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>" />
                    <div class="flex flex-col items-center mt-4">
                        <input type="text" name="name" placeholder="Your Name" class="w-80 mb-4 px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-blue-500" required />
                        <input type="email" name="email" placeholder="Your Email" class="w-80 mb-4 px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-blue-500" required />
                        <button type="submit" class="w-80 py-3 bg-primary text-white rounded-md transition">
                            Get Early Access!
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <footer class="bg-gray-800 text-white py-6">
        <div class="container mx-auto flex justify-between items-center">
            <div class="flex space-x-4">
                <a href="#" class="hover:bg-gray-700 rounded-full p-2" title="LinkedIn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z" />
                    </svg>
                </a>
                <a href="#" class="hover:bg-gray-700 rounded-full p-2" title="Facebook">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M9 8h-3v4h3v12h5v-12h3.642l.358-4h-4v-1.667c0-.955.192-1.333 1.115-1.333h2.885v-5h-3.808c-3.596 0-5.192 1.583-5.192 4.615v3.385z" />
                    </svg>
                </a>
                <a href="#" class="hover:bg-gray-700 rounded-full p-2" title="X (formerly Twitter)">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M18.901 1.153h3.68l-8.04 9.19L24 22.846h-7.406l-5.8-7.584-6.638 7.584H.474l8.6-9.83L0 1.154h7.594l5.243 6.932ZM17.61 20.644h2.039L6.486 3.24H4.298Z" />
                    </svg>
                </a>
                <a href="#" class="hover:bg-gray-700 rounded-full p-2" title="Instagram">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z" />
                    </svg>
                </a>
            </div>
            <div class="text-sm">© 2024 EDU-fier. All rights reserved.</div>
        </div>
    </footer>
    <script src="js/custom.js"></script>
</body>

</html>