<?php
session_start([
    'cookie_secure' => true, // Enforce secure cookies (requires HTTPS)
    'cookie_httponly' => true, // Prevent JavaScript access to session cookie
    'use_strict_mode' => true, // Prevent session fixation
]);

require 'config/db.php';

// Generate CSRF token if not already set
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Check if already logged in
if (isset($_SESSION['admin_id'])) {
    header("Location: dashboard.php");
    exit();

}

$error = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = "Invalid form submission. Please try again.";
    } else {
        $username = trim($_POST['username']);
        $password = $_POST['password'];

        // Validate input
        if (empty($username) || empty($password)) {
            $error = "Please enter both username/CNIC and password";
        } else {
            try {
                // First try admin login
                $username_escaped = mysqli_real_escape_string($conn, $username);

                // Check if admins table has status column
                $check_admin_columns = "SHOW COLUMNS FROM admins LIKE 'status'";
                $admin_column_result = $conn->query($check_admin_columns);
                $admin_has_status = $admin_column_result->num_rows > 0;

                // Admin login query
                $sql = $admin_has_status
                    ? "SELECT id, username, password, status FROM admins WHERE username = ?"
                    : "SELECT id, username, password FROM admins WHERE username = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $username);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows == 1) {
                    $row = $result->fetch_assoc();
                    if ($admin_has_status && isset($row['status']) && $row['status'] == 'locked') {
                        $error = "Your account is locked. Please contact support.";
                    } elseif (password_verify($password, $row['password'])) {
                        session_regenerate_id(true);
                        $_SESSION['admin_id'] = $row['id'];
                        $_SESSION['admin_username'] = $row['username'];
                        header("Location: dashboard.php");
                        exit();
                    } else {
                        $error = "Incorrect username or password";
                    }
                } else {
                    // Try dealer login (fertilizer)
                    $sql = "SELECT id, name, cnic, password FROM fertilizer_dealers WHERE cnic = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("s", $username);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        $dealer = $result->fetch_assoc();
                        if (password_verify($password, $dealer['password'])) {
                            session_regenerate_id(true);
                            $_SESSION['dealer_id'] = $dealer['id'];
                            $_SESSION['dealer_type'] = 'fertilizer';
                            $_SESSION['dealer_name'] = $dealer['name'];
                            header("Location: dealer_dashboard.php");
                            exit();
                        } else {
                            $error = "Incorrect username or password";
                        }
                    } else {
                        // Try dealer login (pesticide)
                        $sql = "SELECT id, name, cnic, password FROM pesticide_dealers WHERE cnic = ?";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("s", $username);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        if ($result->num_rows > 0) {
                            $dealer = $result->fetch_assoc();
                            if (password_verify($password, $dealer['password'])) {
                                session_regenerate_id(true);
                                $_SESSION['dealer_id'] = $dealer['id'];
                                $_SESSION['dealer_type'] = 'pesticide';
                                $_SESSION['dealer_name'] = $dealer['name'];
                                header("Location: dealer_dashboard.php");
                                exit();
                            } else {
                                $error = "Incorrect username or password";
                            }
                        } else {
                            // Try staff login
                            $sql = "SELECT id, name, cnic, password, status FROM staff WHERE cnic = ?";
                            $stmt = $conn->prepare($sql);
                            $stmt->bind_param("s", $username);
                            $stmt->execute();
                            $result = $stmt->get_result();

                            if ($result->num_rows > 0) {
                                $staff = $result->fetch_assoc();
                                if ($staff['status'] == 'inactive') {
                                    $error = "Your account is inactive. Please contact support.";
                                } elseif (password_verify($password, $staff['password'])) {
                                    session_regenerate_id(true);
                                    $_SESSION['staff_id'] = $staff['id'];
                                    $_SESSION['staff_name'] = $staff['name'];
                                    header("Location: staff_dashboard.php");
                                    exit();
                                } else {
                                    $error = "Incorrect CNIC or password";
                                }
                            } else {
                                $error = "Incorrect username or password";
                            }
                        }
                    }
                }
                $stmt->close();
            } catch (Exception $e) {
                $error = "Login error. Please try again or contact support.";
                error_log("Login error: " . $e->getMessage());
            }
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

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            overflow: hidden;
            position: relative;
        }

        /* Animated background elements */
        .bg-elements {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 1;
        }

        .legal-icon {
            position: absolute;
            color: rgba(255, 255, 255, 0.1);
            animation: float 6s ease-in-out infinite;
        }

        .legal-icon:nth-child(1) {
            top: 10%;
            left: 15%;
            font-size: 60px;
            animation-delay: 0s;
        }

        .legal-icon:nth-child(2) {
            top: 20%;
            right: 10%;
            font-size: 45px;
            animation-delay: 2s;
        }

        .legal-icon:nth-child(3) {
            bottom: 20%;
            left: 10%;
            font-size: 50px;
            animation-delay: 4s;
        }

        .legal-icon:nth-child(4) {
            top: 60%;
            right: 20%;
            font-size: 40px;
            animation-delay: 1s;
        }

        .legal-icon:nth-child(5) {
            bottom: 30%;
            right: 5%;
            font-size: 35px;
            animation-delay: 3s;
        }

        .legal-icon:nth-child(6) {
            top: 40%;
            left: 5%;
            font-size: 55px;
            animation-delay: 5s;
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0px) rotate(0deg);
                opacity: 0.1;
            }
            50% {
                transform: translateY(-20px) rotate(5deg);
                opacity: 0.2;
            }
        }

        /* Justice scale animation */
        .justice-scale {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 200px;
            color: rgba(255, 255, 255, 0.03);
            z-index: 0;
            animation: scaleBalance 4s ease-in-out infinite;
        }

        @keyframes scaleBalance {
            0%, 100% {
                transform: translate(-50%, -50%) rotate(-2deg);
            }
            50% {
                transform: translate(-50%, -50%) rotate(2deg);
            }
        }

        .login-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            padding: 40px;
            width: 100%;
            max-width: 450px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(15px);
            animation: fadeIn 0.8s ease-in-out;
            position: relative;
            z-index: 10;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .system-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .system-logo {
            font-size: 48px;
            color: #1e40af;
            margin-bottom: 10px;
            display: block;
        }

        .system-title {
            color: #1e3a8a;
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 5px;
            letter-spacing: 0.5px;
        }

        .system-subtitle {
            color: #6b7280;
            font-size: 14px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .error-message {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            border-left: 4px solid #dc2626;
            color: #991b1b;
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            font-size: 14px;
            box-shadow: 0 2px 10px rgba(220, 38, 38, 0.1);
        }

        .error-message i {
            margin-right: 10px;
            color: #dc2626;
        }

        .input-group {
            position: relative;
            margin-bottom: 20px;
        }

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
            background: #ffffff;
            transform: translateY(-2px);
        }

        .input-group input::placeholder {
            color: #6b7280;
            font-weight: 400;
        }

        .input-group i {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: #1e40af;
            font-size: 18px;
            transition: color 0.3s ease;
        }

        .input-group input:focus + i {
            color: #1e40af;
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
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(30, 64, 175, 0.3);
        }

        .login-btn:hover {
            background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(30, 64, 175, 0.4);
        }

        .login-btn:active {
            transform: translateY(0);
        }

        .login-btn:disabled {
            background: #93c5fd;
            cursor: not-allowed;
            transform: none;
        }

        .login-btn .spinner {
            display: none;
            border: 2px solid #ffffff;
            border-top: 2px solid #1e40af;
            border-radius: 50%;
            width: 16px;
            height: 16px;
            animation: spin 1s linear infinite;
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
        }

        .login-btn.loading .spinner {
            display: inline-block;
        }

        .login-btn.loading span {
            visibility: hidden;
        }

        @keyframes spin {
            0% {
                transform: translate(-50%, -50%) rotate(0deg);
            }
            100% {
                transform: translate(-50%, -50%) rotate(360deg);
            }
        }

        .remember-me {
            display: flex;
            align-items: center;
            margin: 20px 0;
        }

        .remember-me input {
            width: 18px;
            height: 18px;
            margin-right: 12px;
            accent-color: #1e40af;
            cursor: pointer;
        }

        .remember-me label {
            font-size: 14px;
            color: #1f2937;
            cursor: pointer;
            user-select: none;
        }

        .additional-links {
            text-align: center;
            margin-top: 25px;
        }

        .additional-links a {
            color: #1e40af;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .additional-links a:hover {
            color: #1e3a8a;
            text-decoration: underline;
        }

        .powered-by {
            text-align: center;
            margin-top: 25px;
            font-size: 13px;
            color: #6b7280;
        }

        .powered-by a {
            color: #1e40af;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .powered-by a:hover {
            text-decoration: underline;
            color: #1e3a8a;
        }

        /* Responsive design */
        @media (max-width: 767px) {
            .login-container {
                padding: 30px;
                max-width: 90%;
            }

            .system-title {
                font-size: 24px;
            }

            .system-logo {
                font-size: 40px;
            }

            .legal-icon {
                font-size: 30px !important;
            }

            .justice-scale {
                font-size: 150px;
            }
        }

        @media (max-width: 480px) {
            .login-container {
                padding: 25px;
            }

            .system-title {
                font-size: 22px;
            }

            .system-logo {
                font-size: 36px;
            }

            .input-group input {
                padding: 14px 14px 14px 50px;
                font-size: 14px;
            }

            .login-btn {
                padding: 14px;
                font-size: 14px;
            }

            .legal-icon {
                font-size: 25px !important;
            }
        }
    </style>
</head>

<body>
    <!-- Background legal elements -->
    <div class="bg-elements">
        <i class="fas fa-balance-scale legal-icon"></i>
        <i class="fas fa-gavel legal-icon"></i>
        <i class="fas fa-file-contract legal-icon"></i>
        <i class="fas fa-university legal-icon"></i>
        <i class="fas fa-scroll legal-icon"></i>
        <i class="fas fa-handshake legal-icon"></i>
    </div>

    <!-- Central justice scale -->
    <div class="justice-scale">
        <i class="fas fa-balance-scale"></i>
    </div>

    <div class="login-container">
        <div class="system-header">
            <i class="fas fa-balance-scale system-logo"></i>
            <h1 class="system-title">CasePilot</h1>
            <p class="system-subtitle">Legal Management System</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="" style="width: 100%;" id="login-form">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
            
            <div class="input-group">
                <input type="text" id="username" name="username" placeholder="Username or Bar Council ID" required
                    value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
                <i class="fas fa-user-tie"></i>
            </div>

            <div class="input-group">
                <input type="password" id="password" name="password" placeholder="Password" required>
                <i class="fas fa-lock"></i>
            </div>

            <div class="remember-me">
                <input type="checkbox" id="remember-me" name="remember-me">
                <label for="remember-me">Keep me signed in</label>
            </div>

            <button type="submit" name="login" class="login-btn" id="login-btn">
                <span><i class="fas fa-sign-in-alt" style="margin-right: 8px;"></i>Access System</span>
                <div class="spinner"></div>
            </button>
        </form>

        <div class="additional-links">
            <a href="#" id="forgot-password">Forgot Password?</a>
        </div>

        <div class="powered-by">
            Powered by <a href="https://aevumedge.com" target="_blank">AevumEdge</a> | Legal Tech Solutions
        </div>
    </div>

    <script>
        const usernameInput = document.getElementById('username');

        // Enhanced input handling for legal IDs
        usernameInput.addEventListener('input', (e) => {
            let value = e.target.value;
            
            // Format if it looks like a Bar Council ID or CNIC
            if (/^\d+$/.test(value.replace(/-/g, ''))) {
                value = value.replace(/-/g, '');

                // Bar Council ID format (usually shorter)
                if (value.length <= 8) {
                    // Keep as is for Bar Council ID
                } else {
                    // CNIC format
                    if (value.length > 5 && value.length <= 13) {
                        value = value.replace(/^(\d{5})(\d{1,7})/, '$1-$2');
                    }
                    if (value.length > 13) {
                        value = value.replace(/^(\d{5})(\d{7})(\d{1})/, '$1-$2-$3');
                    }
                    if (value.length > 15) {
                        value = value.substring(0, 15);
                    }
                }
            }

            e.target.value = value;
        });

        // Enhanced validation for legal credentials
        usernameInput.addEventListener('blur', (e) => {
            const value = e.target.value;
            const numericValue = value.replace(/-/g, '');
            
            if (/^\d+$/.test(numericValue)) {
                if (numericValue.length !== 13 && numericValue.length < 4) {
                    alert('Please enter a valid Bar Council ID or CNIC number.');
                    e.target.focus();
                }
            }
        });

        // Enhanced form submission
        document.getElementById('login-form').addEventListener('submit', function(e) {
            const loginBtn = document.getElementById('login-btn');
            loginBtn.classList.add('loading');
            loginBtn.disabled = true;
            
            // Add a slight delay to show the loading animation
            setTimeout(() => {
                // Form will submit naturally
            }, 300);
        });

        // Forgot password functionality
        document.getElementById('forgot-password').addEventListener('click', function(e) {
            e.preventDefault();
            alert('Please contact your system administrator to reset your password.');
        });

        // Add enter key support for better UX
        document.getElementById('password').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                document.getElementById('login-form').dispatchEvent(new Event('submit'));
            }
        });
    </script>
</body>

</html>