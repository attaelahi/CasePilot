<?php
// lawyer_dashboard.php
// This is the main dashboard for a logged-in lawyer.

session_start();

// Protect the page: redirect to login if lawyer is not logged in.
if (!isset($_SESSION['lawyer_id'])) {
    header("Location: index.php");
    exit();
}

require_once "config/db.php";

// Get lawyer's details from session
$lawyer_id = $_SESSION['lawyer_id'];
$lawyer_username = $_SESSION['lawyer_username'];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lawyer Dashboard - CasePilot</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="flex h-screen bg-gray-200">
        <!-- Sidebar -->
        <div class="w-64 bg-gray-800 text-white p-5 flex flex-col">
            <div class="text-center mb-10">
                <i class="fas fa-balance-scale text-3xl text-blue-400"></i>
                <h1 class="text-2xl font-bold mt-2">CasePilot</h1>
            </div>
            <nav class="flex-grow">
                <a href="lawyer_dashboard.php" class="flex items-center py-2.5 px-4 rounded transition duration-200 bg-gray-700 text-white">
                    <i class="fas fa-tachometer-alt mr-3"></i> Dashboard
                </a>
                <!-- Add more navigation links here as you build features -->
                <!-- e.g., <a href="my_cases.php" class="flex items-center py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700"> ... My Cases </a> -->
            </nav>
            <div class="pt-4 mt-4 border-t border-gray-700">
                 <a href="logout.php" class="flex items-center py-2.5 px-4 rounded transition duration-200 hover:bg-red-500">
                    <i class="fas fa-sign-out-alt mr-3"></i> Logout
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <header class="flex justify-between items-center p-4 bg-white border-b-2 border-gray-200">
                <h2 class="text-xl font-semibold text-gray-700">Dashboard</h2>
                <div class="flex items-center">
                    <span class="text-gray-600 mr-3">Welcome, <span class="font-bold"><?php echo htmlspecialchars($lawyer_username); ?></span>!</span>
                     <i class="fas fa-user-circle text-2xl text-gray-500"></i>
                </div>
            </header>
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6">
                <h3 class="text-2xl font-semibold text-gray-800 mb-4">Overview</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Example Stat Card -->
                    <div class="bg-white p-6 rounded-lg shadow-md">
                        <h4 class="text-lg font-semibold text-gray-600">Active Cases</h4>
                        <p class="text-3xl font-bold text-blue-600 mt-2">0</p>
                    </div>
                     <div class="bg-white p-6 rounded-lg shadow-md">
                        <h4 class="text-lg font-semibold text-gray-600">Upcoming Hearings</h4>
                        <p class="text-3xl font-bold text-green-600 mt-2">0</p>
                    </div>
                     <div class="bg-white p-6 rounded-lg shadow-md">
                        <h4 class="text-lg font-semibold text-gray-600">Pending Tasks</h4>
                        <p class="text-3xl font-bold text-yellow-500 mt-2">0</p>
                    </div>
                </div>
                <div class="mt-8 bg-white p-6 rounded-lg shadow-md">
                    <h4 class="text-xl font-semibold text-gray-800">Your Dashboard</h4>
                    <p class="text-gray-600 mt-2">This is your personal dashboard. All features and data you see here are exclusive to your account. As you build out the application, you can add case management, client information, scheduling, and other features here.</p>
                </div>
            </main>
        </div>
    </div>
</body>
</html>
