<?php
session_start();
// Check if user is logged in as superadmin
if (!isset($_SESSION['superadmin_id'])) {
    header("Location: index.php");
    exit();
}

require_once "../config/db.php";

// Get superadmin info
$superadmin_id = $_SESSION['superadmin_id'];
$superadmin_username = $_SESSION['superadmin_username'];

// Get admin count
$sql_admins = "SELECT COUNT(*) as admin_count FROM admins";
$result_admins = $conn->query($sql_admins);
$admin_count = $result_admins->fetch_assoc()['admin_count'];


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ClassesLog SuperAdmin - Dashboard</title>
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
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-x">
                            <path d="M18 6 6 18" />
                            <path d="m6 6 12 12" />
                        </svg>
                    </button>
                </div>
                <div class="mt-2 text-sm text-purple-200">SuperAdmin Panel</div>
            </div>

            <nav class="flex-1 overflow-y-auto py-4">
                <ul class="space-y-1">
                    <li>
                        <a href="dashboard.php" class="flex items-center px-4 py-3 text-white bg-purple-700 hover:bg-purple-700 rounded-lg mx-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-3">
                                <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                                <polyline points="9 22 9 12 15 12 15 22" />
                            </svg>
                            Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="manage-admins.php" class="flex items-center px-4 py-3 text-white hover:bg-purple-700 rounded-lg mx-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-3">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                                <circle cx="9" cy="7" r="4" />
                                <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
                                <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                            </svg>
                            Manage Admins
                        </a>
                    </li>
                   
                    <li>
                        <a href="settings.php" class="flex items-center px-4 py-3 text-white hover:bg-purple-700 rounded-lg mx-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-3">
                                <path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z" />
                                <circle cx="12" cy="12" r="3" />
                            </svg>
                            Settings
                        </a>
                    </li>
                </ul>
            </nav>

            <div class="p-4 border-t border-purple-700">
                <a href="logout.php" class="flex items-center text-white hover:text-purple-200">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-3">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                        <polyline points="16 17 21 12 16 7" />
                        <line x1="21" x2="9" y1="12" y2="12" />
                    </svg>
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
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-menu">
                            <line x1="4" x2="20" y1="12" y2="12" />
                            <line x1="4" x2="20" y1="6" y2="6" />
                            <line x1="4" x2="20" y1="18" y2="18" />
                        </svg>
                    </button>
                    <h1 class="text-xl font-semibold text-gray-800">SuperAdmin Dashboard</h1>
                    <div class="flex items-center">
                        <span class="text-sm text-gray-600 mr-2">Welcome, <?php echo htmlspecialchars($superadmin_username); ?></span>
                        <div class="w-8 h-8 rounded-full bg-purple-600 flex items-center justify-center text-white">
                            <?php echo strtoupper(substr($superadmin_username, 0, 1)); ?>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Content Area -->
            <main class="flex-1 overflow-y-auto bg-gray-50 p-4 md:p-6">
                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div class="bg-white rounded-lg shadow p-4">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-users">
                                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                                    <circle cx="9" cy="7" r="4" />
                                    <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h2 class="text-gray-600 text-sm">Total Admins</h2>
                                <p class="text-2xl font-semibold"><?php echo $admin_count; ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow p-4">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-user-cog">
                                    <circle cx="9" cy="7" r="4" />
                                    <path d="M3 21v-2a4 4 0 0 1 4-4h4a4 4 0 0 1 4 4v2" />
                                    <path d="M21.7 16.4c.2-.5.3-1 .3-1.4 0-2.2-2-4-4.5-4s-4.5 1.8-4.5 4 2 4 4.5 4c.5 0 1-.1 1.4-.2" />
                                    <path d="M21.1 15.5c0 .2.1.5.1.7 0 .8-.7 1.5-1.5 1.5s-1.5-.7-1.5-1.5.7-1.5 1.5-1.5c.2 0 .5 0 .7.1" />
                                    <path d="m19.3 18.2.2.2" />
                                    <path d="m18.7 14.1.2-.2" />
                                    <path d="m21.5 16.5.2-.2" />
                                    <path d="m16.8 16.5.2.2" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h2 class="text-gray-600 text-sm">Total Teachers</h2>
                                <p class="text-2xl font-semibold"><?php echo $teacher_count; ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow p-4">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-red-100 text-red-600">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-lock">
                                    <rect width="18" height="11" x="3" y="11" rx="2" ry="2" />
                                    <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h2 class="text-gray-600 text-sm">Locked Accounts</h2>
                                <p class="text-2xl font-semibold"><?php echo $locked_count; ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white rounded-lg shadow mb-6">
                    <div class="p-4 border-b">
                        <h2 class="text-lg font-semibold text-gray-800">Quick Actions</h2>
                    </div>
                    <div class="p-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <a href="manage-admins.php" class="flex items-center p-3 bg-purple-50 hover:bg-purple-100 rounded-lg transition-colors">
                            <div class="p-2 rounded-full bg-purple-200 text-purple-700">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-user-cog">
                                    <circle cx="9" cy="7" r="4" />
                                    <path d="M3 21v-2a4 4 0 0 1 4-4h4a4 4 0 0 1 4 4v2" />
                                    <path d="M21.7 16.4c.2-.5.3-1 .3-1.4 0-2.2-2-4-4.5-4s-4.5 1.8-4.5 4 2 4 4.5 4c.5 0 1-.1 1.4-.2" />
                                    <path d="M21.1 15.5c0 .2.1.5.1.7 0 .8-.7 1.5-1.5 1.5s-1.5-.7-1.5-1.5.7-1.5 1.5-1.5c.2 0 .5 0 .7.1" />
                                    <path d="m19.3 18.2.2.2" />
                                    <path d="m18.7 14.1.2-.2" />
                                    <path d="m21.5 16.5.2-.2" />
                                    <path d="m16.8 16.5.2.2" />
                                </svg>
                            </div>
                            <span class="ml-3 font-medium text-purple-700">Manage Admin Accounts</span>
                        </a>

                        <a href="manage-teachers.php" class="flex items-center p-3 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors">
                            <div class="p-2 rounded-full bg-blue-200 text-blue-700">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-user-cog">
                                    <circle cx="9" cy="7" r="4" />
                                    <path d="M3 21v-2a4 4 0 0 1 4-4h4a4 4 0 0 1 4 4v2" />
                                    <path d="M21.7 16.4c.2-.5.3-1 .3-1.4 0-2.2-2-4-4.5-4s-4.5 1.8-4.5 4 2 4 4.5 4c.5 0 1-.1 1.4-.2" />
                                    <path d="M21.1 15.5c0 .2.1.5.1.7 0 .8-.7 1.5-1.5 1.5s-1.5-.7-1.5-1.5.7-1.5 1.5-1.5c.2 0 .5 0 .7.1" />
                                    <path d="m19.3 18.2.2.2" />
                                    <path d="m18.7 14.1.2-.2" />
                                    <path d="m21.5 16.5.2-.2" />
                                    <path d="m16.8 16.5.2.2" />
                                </svg>
                            </div>
                            <span class="ml-3 font-medium text-blue-700">Manage Teacher Accounts</span>
                        </a>
                    </div>
                </div>

                <!-- System Status -->
                <div class="bg-white rounded-lg shadow">
                    <div class="p-4 border-b">
                        <h2 class="text-lg font-semibold text-gray-800">System Status</h2>
                    </div>
                    <div class="p-4">
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="w-3 h-3 rounded-full bg-green-500 mr-2"></div>
                                    <span class="text-sm font-medium text-gray-700">Admin Login System</span>
                                </div>
                                <span class="text-sm text-green-600">Active</span>
                            </div>

                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="w-3 h-3 rounded-full bg-green-500 mr-2"></div>
                                    <span class="text-sm font-medium text-gray-700">Teacher Login System</span>
                                </div>
                                <span class="text-sm text-green-600">Active</span>
                            </div>

                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="w-3 h-3 rounded-full bg-green-500 mr-2"></div>
                                    <span class="text-sm font-medium text-gray-700">Student Login System</span>
                                </div>
                                <span class="text-sm text-green-600">Active</span>
                            </div>

                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="w-3 h-3 rounded-full bg-green-500 mr-2"></div>
                                    <span class="text-sm font-medium text-gray-700">Database Connection</span>
                                </div>
                                <span class="text-sm text-green-600">Connected</span>
                            </div>
                        </div>
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