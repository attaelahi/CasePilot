<?php
session_start();
if (!isset($_SESSION['superadmin_id'])) {
    header("Location: index.php");
    exit();
}

require_once "../config/db.php";

$superadmin_username = $_SESSION['superadmin_username'];

// Get admin count
$sql_admins = "SELECT COUNT(*) as admin_count FROM admins";
$result_admins = $conn->query($sql_admins);
$admin_count = $result_admins->fetch_assoc()['admin_count'];

// --- NEW: Get lawyer count ---
$sql_lawyers = "SELECT COUNT(*) as lawyer_count FROM lawyers";
$result_lawyers = $conn->query($sql_lawyers);
$lawyer_count = $result_lawyers->fetch_assoc()['lawyer_count'];

// --- NEW: Get locked lawyer count ---
$sql_locked_lawyers = "SELECT COUNT(*) as locked_count FROM lawyers WHERE status = 'locked'";
$result_locked_lawyers = $conn->query($sql_locked_lawyers);
$locked_lawyers_count = $result_locked_lawyers->fetch_assoc()['locked_count'];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SuperAdmin - Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-gray-50">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside class="sidebar bg-purple-800 text-white w-64 min-h-screen flex-col z-10 hidden md:flex">
            <div class="p-4 border-b border-purple-700">
                <h2 class="text-xl font-bold">CasePilot SuperAdmin</h2>
            </div>
            <nav class="flex-1 py-4">
                <ul class="space-y-1">
                    <li><a href="dashboard.php" class="flex items-center px-4 py-3 text-white bg-purple-700 rounded-lg mx-2">Dashboard</a></li>
                    <li><a href="manage-admins.php" class="flex items-center px-4 py-3 text-white hover:bg-purple-700 rounded-lg mx-2">Manage Admins</a></li>
                    <li><a href="manage-lawyers.php" class="flex items-center px-4 py-3 text-white hover:bg-purple-700 rounded-lg mx-2">Manage Lawyers</a></li>
                    <li><a href="settings.php" class="flex items-center px-4 py-3 text-white hover:bg-purple-700 rounded-lg mx-2">Settings</a></li>
                </ul>
            </nav>
            <div class="p-4 border-t border-purple-700"><a href="logout.php" class="flex items-center text-white hover:text-purple-200">Logout</a></div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <header class="bg-white shadow-sm">
                <div class="flex items-center justify-between p-4">
                    <h1 class="text-xl font-semibold text-gray-800">SuperAdmin Dashboard</h1>
                    <span class="text-sm text-gray-600">Welcome, <?php echo htmlspecialchars($superadmin_username); ?></span>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto bg-gray-50 p-4 md:p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <!-- Admins Card -->
                    <div class="bg-white rounded-lg shadow p-4 flex items-center">
                        <div class="p-3 rounded-full bg-blue-100 text-blue-600"><i class="fa fa-user-shield fa-lg"></i></div>
                        <div class="ml-4">
                            <h2 class="text-gray-600 text-sm">Total Admins</h2>
                            <p class="text-2xl font-semibold"><?php echo $admin_count; ?></p>
                        </div>
                    </div>
                    <!-- Lawyers Card -->
                    <div class="bg-white rounded-lg shadow p-4 flex items-center">
                        <div class="p-3 rounded-full bg-green-100 text-green-600"><i class="fa fa-gavel fa-lg"></i></div>
                        <div class="ml-4">
                            <h2 class="text-gray-600 text-sm">Total Lawyers</h2>
                            <p class="text-2xl font-semibold"><?php echo $lawyer_count; ?></p>
                        </div>
                    </div>
                    <!-- Locked Lawyers Card -->
                     <div class="bg-white rounded-lg shadow p-4 flex items-center">
                        <div class="p-3 rounded-full bg-red-100 text-red-600"><i class="fa fa-lock fa-lg"></i></div>
                        <div class="ml-4">
                            <h2 class="text-gray-600 text-sm">Locked Lawyers</h2>
                            <p class="text-2xl font-semibold"><?php echo $locked_lawyers_count; ?></p>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>
