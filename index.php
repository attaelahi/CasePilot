<?php
session_start([
    'cookie_secure' => true,
    'cookie_httponly' => true,
    'use_strict_mode' => true,
]);

require 'config/db.php';

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Check if already logged in and redirect
if (isset($_SESSION['admin_id'])) {
    header("Location: dashboard.php");
    exit();
} elseif (isset($_SESSION['lawyer_id'])) {
    header("Location: lawyer_dashboard.php");
    exit();
}
// Add other role checks here if needed (e.g., dealer, staff)

$error = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $error = "Invalid form submission. Please try again.";
    } else {
        $username = trim($_POST['username']);
        $password = $_POST['password'];

        if (empty($username) || empty($password)) {
            $error = "Please enter both username and password";
        } else {
            // --- NEW: Try Lawyer Login First ---
            $sql_lawyer = "SELECT id, username, password, status FROM lawyers WHERE username = ?";
            $stmt_lawyer = $conn->prepare($sql_lawyer);
            $stmt_lawyer->bind_param("s", $username);
            $stmt_lawyer->execute();
            $result_lawyer = $stmt_lawyer->get_result();

            if ($result_lawyer->num_rows == 1) {
                $lawyer = $result_lawyer->fetch_assoc();
                if ($lawyer['status'] == 'locked') {
                    $error = "Your account is locked. Please contact support.";
                } elseif (password_verify($password, $lawyer['password'])) {
                    session_regenerate_id(true);
                    $_SESSION['lawyer_id'] = $lawyer['id'];
                    $_SESSION['lawyer_username'] = $lawyer['username'];
                    header("Location: lawyer_dashboard.php");
                    exit();
                } else {
                    $error = "Incorrect username or password.";
                }
            } else {
                // --- EXISTING LOGIN LOGIC (Admin, Dealer, etc.) ---
                // Try admin login
                $sql_admin = "SELECT id, username, password, status FROM admins WHERE username = ?";
                $stmt_admin = $conn->prepare($sql_admin);
                $stmt_admin->bind_param("s", $username);
                $stmt_admin->execute();
                $result_admin = $stmt_admin->get_result();

                if ($result_admin->num_rows == 1) {
                    $admin = $result_admin->fetch_assoc();
                    if (isset($admin['status']) && $admin['status'] == 'locked') {
                         $error = "Your admin account is locked. Please contact support.";
                    } elseif (password_verify($password, $admin['password'])) {
                        session_regenerate_id(true);
                        $_SESSION['admin_id'] = $admin['id'];
                        $_SESSION['admin_username'] = $admin['username'];
                        header("Location: dashboard.php");
                        exit();
                    } else {
                         $error = "Incorrect username or password.";
                    }
                } else {
                    // Placeholder for other login types (dealer, staff) if they exist
                    // ... your existing code for other user types ...
                    
                    // If no user is found across all types
                    $error = "Incorrect username or password.";
                }
                $stmt_admin->close();
            }
            $stmt_lawyer->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CasePilot - Legal Management System</title>
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
        .login-container {
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
        .login-btn {
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
        .login-btn:hover {
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
            .login-container { padding: 20px; max-width: 90%; }
            .system-title { font-size: 24px; }
            .system-logo { font-size: 40px; }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="system-header">
            <i class="fas fa-balance-scale system-logo"></i>
            <h1 class="system-title">CasePilot</h1>
            <p class="system-subtitle">Legal Management System</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-circle mr-3"></i>
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="index.php" id="login-form">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
            
            <div class="input-group">
                <input type="text" id="username" name="username" placeholder="Enter your username" required>
                <i class="fas fa-user-tie"></i>
            </div>

            <div class="input-group">
                <input type="password" id="password" name="password" placeholder="Password" required>
                <i class="fas fa-lock"></i>
            </div>

            <button type="submit" name="login" class="login-btn">
                <span><i class="fas fa-sign-in-alt mr-2"></i>Login</span>
            </button>
        </form>

        <div class="additional-links">
            <a href="lawyer-register.php">Create a Lawyer Account</a>
        </div>
    </div>
</body>
</html>