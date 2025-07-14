<?php
session_start();
// Check if user is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

require_once "config/db.php";

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/lucide-icons@0.309.0/dist/umd/lucide.min.js">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        @media (max-width: 768px) {

            #adminFeedbackDropdown {
                right: 50%;
                transform: translateX(50%);
            }

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

        <?php
        include './includes/sidebar.php';
        ?>


        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Navigation -->
            <?php
            include './includes/header.php';
            ?>

            <!-- Main Content Area -->

            <!-- Footer -->
            <footer class="mt-6 text-center text-sm text-gray-500 py-4">
                Powered by <a href="https://aevumedge.com" target="_blank" class="text-teal-600 hover:text-teal-800 font-medium">AevumEdge</a>
            </footer>
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