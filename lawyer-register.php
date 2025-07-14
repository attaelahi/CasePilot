<?php
// lawyer-register.php
// This file allows new lawyers to create an account.

session_start();
require 'config/db.php';

// Generate a CSRF token if one doesn't exist
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$error = '';
$success = '';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $error = "Invalid form submission. Please try again.";
    } else {
        // Sanitize and retrieve form data
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];

        // --- Validation ---
        if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
            $error = "Please fill in all fields.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Invalid email format.";
        } elseif (strlen($password) < 8) {
            $error = "Password must be at least 8 characters long.";
        } elseif ($password !== $confirm_password) {
            $error = "Passwords do not match.";
        } else {
            // Check if username or email already exists
            $sql = "SELECT id FROM lawyers WHERE username = ? OR email = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $username, $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $error = "A lawyer with this username or email already exists.";
            } else {
                // Hash the password for security
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // Insert the new lawyer into the database
                $sql_insert = "INSERT INTO lawyers (username, email, password) VALUES (?, ?, ?)";
                $stmt_insert = $conn->prepare($sql_insert);
                $stmt_insert->bind_param("sss", $username, $email, $hashed_password);

                if ($stmt_insert->execute()) {
                    $success = "Account created successfully! You can now log in.";
                } else {
                    $error = "An error occurred during registration. Please try again later.";
                }
                $stmt_insert->close();
            }
            $stmt->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CasePilot - Lawyer Registration</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }
        body {
            background: url('https://images.unsplash.com/photo-1589829545856-d10d557cf95f?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80') no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
        }
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5); /* Dark overlay for readability */
            z-index: 1;
        }
        .register-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            padding: 40px;
            width: 100%;
            max-width: 450px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
            z-index: 10;
            border: 1px solid rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(5px); /* Subtle glassmorphism effect */
        }
        .system-header { text-align: center; margin-bottom: 30px; }
        .system-logo { font-size: 48px; color: #1e40af; margin-bottom: 10px; display: block; }
        .system-title { color: #1e3a8a; font-size: 28px; font-weight: 700; }
        .system-subtitle { color: #6b7280; font-size: 14px; font-weight: 500; text-transform: uppercase; letter-spacing: 1px; }
        .error-message {
            background: #fee2e2;
            border-left: 4px solid #dc2626;
            color: #991b1b;
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            font-size: 14px;
        }
        .success-message {
            background: #d1fae5;
            border-left: 4px solid #10b981;
            color: #065f46;
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            font-size: 14px;
        }
        .input-group { position: relative; margin-bottom: 20px; }
        .input-group input {
            width: 100%;
            padding: 16px 16px 16px 55px;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            font-size: 16px;
            color: #1f2937;
            background: #f9fafb;
            transition: all 0.3s ease;
        }
        .input-group input:focus {
            border-color: #1e40af;
            box-shadow: 0 0 0 3px rgba(30, 64, 175, 0.1);
            outline: none;
        }
        .input-group i {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: #1e40af;
            font-size: 18px;
        }
        .register-btn {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .register-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(30, 64, 175, 0.4);
        }
        .additional-links { text-align: center; margin-top: 25px; }
        .additional-links a {
            color: #1e40af;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: color 0.3s ease;
        }
        .additional-links a:hover { text-decoration: underline; }
        /* Responsive adjustments */
        @media (max-width: 480px) {
            .register-container { padding: 20px; max-width: 90%; }
            .system-title { font-size: 24px; }
            .system-logo { font-size: 40px; }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="system-header">
            <i class="fas fa-balance-scale system-logo"></i>
            <h1 class="system-title">Create Lawyer Account</h1>
            <p class="system-subtitle">Join the CasePilot Network</p>
        </div>

        <?php if ($error): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-circle mr-3"></i>
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="success-message">
                <i class="fas fa-check-circle mr-3"></i>
                <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="lawyer-register.php" class="space-y-4">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
            <div class="input-group">
                <input id="username" type="text" name="username" placeholder="Choose a username" required>
                <i class="fas fa-user-tie"></i>
            </div>
            <div class="input-group">
                <input id="email" type="email" name="email" placeholder="you@example.com" required>
                <i class="fas fa-envelope"></i>
            </div>
            <div class="input-group">
                <input id="password" type="password" name="password" placeholder="Min. 8 characters" required>
                <i class="fas fa-lock"></i>
            </div>
            <div class="input-group">
                <input id="confirm_password" type="password" name="confirm_password" placeholder="Re-enter your password" required>
                <i class="fas fa-lock"></i>
            </div>
            <button type="submit" class="register-btn">
                <span><i class="fas fa-user-plus mr-2"></i>Register</span>
            </button>
            <div class="additional-links">
                <a href="index.php">Already have an account? Login</a>
            </div>
        </form>
    </div>
</body>
</html>