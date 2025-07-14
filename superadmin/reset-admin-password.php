<?php
session_start();
// Check if user is logged in as superadmin
if(!isset($_SESSION['superadmin_id'])) {
    header("Location: index.php");
    exit();
}

require_once "../config/db.php";

$success_message = "";
$error_message = "";
$admin = null;

// Check if admin ID is provided
if(!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: manage-admins.php");
    exit();
}

$admin_id = intval($_GET['id']);

// Get admin details
$sql = "SELECT id, username, email FROM admins WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows == 0) {
    header("Location: manage-admins.php");
    exit();
}

$admin = $result->fetch_assoc();
$stmt->close();



if($_SERVER["REQUEST_METHOD"] == "POST") {
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validate input
    if(empty($password) || empty($confirm_password)) {
        $error_message = "All fields are required";
    } else if($password !== $confirm_password) {
        $error_message = "Passwords do not match";
    } else if(strlen($password) < 8) {
        $error_message = "Password must be at least 8 characters long";
    } else {
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Update admin password
        $update_sql = "UPDATE admins SET password = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("si", $hashed_password, $admin_id);
        
        if($update_stmt->execute()) {
            $success_message = "Admin password reset successfully";
        } else {
            $error_message = "Error resetting admin password: " . $conn->error;
        }
        
        $update_stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ClassesLog SuperAdmin - Reset Admin Password</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease-in-out;
            }
            .sidebar.open {
                transform: translateX(0);
            }
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside id="sidebar" class="sidebar bg-purple-800 text-white w-64 min-h-screen flex flex-col z-10 fixed md:relative">
            <div class="p-4 border-b border-purple-700">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-bold">ClassesLog</h2>
                    <button id="closeSidebar" class="md:hidden text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-x"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                    </button>
                </div>
                <div class="mt-2 text-sm text-purple-200">SuperAdmin Panel</div>
            </div>
            
            <nav class="flex-1 overflow-y-auto py-4">
                <ul class="space-y-1">
                    <li>
                        <a href="dashboard.php" class="flex items-center px-4 py-3 text-white hover:bg-purple-700 rounded-lg mx-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-3"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                            Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="manage-admins.php" class="flex items-center px-4 py-3 text-white bg-purple-700 hover:bg-purple-700 rounded-lg mx-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-3"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                            Manage Admins
                        </a>
                    </li>
                    <li>
                        <a href="manage-teachers.php" class="flex items-center px-4 py-3 text-white hover:bg-purple-700 rounded-lg mx-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-3"><path d="M1 22V9.76a2 2 0 0 1 .51-1.33L11.78 1a.7.7 0 0 1 .44 0l10.26 7.43a2 2 0 0 1 .52 1.33V22"/><path d="M5 8v14"/><path d="M19 8v14"/><path d="M9 22v-5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v5"/><path d="M10 7.25h4"/></svg>
                            Manage Teachers
                        </a>
                    </li>
                    <li>
                        <a href="settings.php" class="flex items-center px-4 py-3 text-white hover:bg-purple-700 rounded-lg mx-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-3"><path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/><circle cx="12" cy="12" r="3"/></svg>
                            Settings
                        </a>
                    </li>
                </ul>
            </nav>
            
            <div class="p-4 border-t border-purple-700">
                <a href="logout.php" class="flex items-center text-white hover:text-purple-200">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-3"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" x2="9" y1="12" y2="12"/></svg>
                    Logout
                </a>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Navigation -->
            <header class="bg-white shadow-sm">
                <div class="flex items-center justify-between p-4">
                    <button id="openSidebar" class="md:hidden text-gray-600">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-menu"><line x1="4" x2="20" y1="12" y2="12"/><line x1="4" x2="20" y1="6" y2="6"/><line x1="4" x2="20" y1="18" y2="18"/></svg>
                    </button>
                    <h1 class="text-xl font-semibold text-gray-800">Reset Admin Password</h1>
                    <div></div>
                </div>
            </header>

            <!-- Main Content Area -->
            <main class="flex-1 overflow-y-auto bg-gray-50 p-4 md:p-6">
                <?php if(!empty($success_message)): ?>
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                        <?php echo $success_message; ?>
                    </div>
                <?php endif; ?>
                
                <?php if(!empty($error_message)): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        <?php echo $error_message; ?>
                    </div>
                <?php endif; ?>
                
                <div class="bg-white rounded-lg shadow">
                    <div class="p-4 border-b">
                        <h2 class="text-lg font-semibold text-gray-800">Reset Password for Admin: <?php echo htmlspecialchars($admin['username']); ?></h2>
                    </div>
                    <div class="p-4">
                        <form method="POST" action="">
                            <div class="grid grid-cols-1 gap-6">
                                <div>
                                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                                    <input type="password" id="password" name="password" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-600 focus:border-transparent" required>
                                    <p class="mt-1 text-xs text-gray-500">Password must be at least 8 characters long</p>
                                </div>
                                
                                <div>
                                    <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password</label>
                                    <input type="password" id="confirm_password" name="confirm_password" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-600 focus:border-transparent" required>
                                </div>
                                
                                <div class="flex justify-end">
                                    <a href="manage-admins.php" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400 transition-colors mr-2">
                                        Cancel
                                    </a>
                                    <button type="submit" class="bg-purple-600 text-white px-4 py-2 rounded-md hover:bg-purple-700 transition-colors">
                                        Reset Password
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
        // Mobile sidebar toggle
        const sidebar = document.getElementById('sidebar');
        const openSidebarBtn = document.getElementById('openSidebar');
        const closeSidebarBtn = document.getElementById('closeSidebar');
        
        openSidebarBtn.addEventListener('click', () => {
            sidebar.classList.add('open');
        });
        
        closeSidebarBtn.addEventListener('click', () => {
            sidebar.classList.remove('open');
        });
    </script>
</body>
</html>
