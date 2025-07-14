<?php
require_once 'config/database.php';
require_once 'config/session.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = filter_var(trim($_POST['name']), FILTER_SANITIZE_STRING);
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validation
    if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = 'All fields are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters long.';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match.';
    } else {
        try {
            $database = new Database();
            $db = $database->getConnection();
            
            // Check if email already exists
            $query = "SELECT id FROM users WHERE email = :email";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                $error = 'Email already exists. Please use a different email.';
            } else {
                // Create new user
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $query = "INSERT INTO users (name, email, password, role) VALUES (:name, :email, :password, 'lawyer')";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':name', $name);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':password', $hashed_password);
                
                if ($stmt->execute()) {
                    $_SESSION['success_message'] = 'Account created successfully. Please log in.';
                    header("Location: login.php");
                    exit();
                } else {
                    $error = 'Registration failed. Please try again.';
                }
            }
        } catch (PDOException $exception) {
            $error = 'Database error: ' . $exception->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account - CasePilot</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'cp-gray': '#F3F4F6',
                        'cp-blue': '#2563EB',
                        'cp-blue-hover': '#1E40AF',
                        'cp-red': '#DC2626',
                        'cp-green': '#059669'
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-cp-gray min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <div class="bg-white rounded-lg shadow-md p-8">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">CasePilot</h1>
                <h2 class="text-xl font-semibold text-gray-700">Create Your Account</h2>
                <p class="text-gray-600 mt-2">Join the modern case management platform for lawyers</p>
            </div>

            <?php if ($error): ?>
                <div class="bg-red-50 border border-red-200 text-cp-red px-4 py-3 rounded mb-4">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="" class="space-y-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                    <input type="text" id="name" name="name" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-cp-blue focus:border-transparent"
                           value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                    <input type="email" id="email" name="email" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-cp-blue focus:border-transparent"
                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                    <input type="password" id="password" name="password" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-cp-blue focus:border-transparent"
                           minlength="8">
                    <p class="text-sm text-gray-500 mt-1">Minimum 8 characters</p>
                </div>

                <div>
                    <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-2">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-cp-blue focus:border-transparent"
                           minlength="8">
                </div>

                <button type="submit" 
                        class="w-full bg-cp-blue text-white py-2 px-4 rounded-md hover:bg-cp-blue-hover focus:outline-none focus:ring-2 focus:ring-cp-blue focus:ring-offset-2 transition duration-200">
                    Create Account
                </button>
            </form>

            <div class="mt-6 text-center">
                <p class="text-gray-600">
                    Already have an account? 
                    <a href="login.php" class="text-cp-blue hover:underline font-medium">Sign in here</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
