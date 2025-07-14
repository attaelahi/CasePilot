<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CasePilot - Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

        body {
            font-family: 'Inter', sans-serif;
        }

        .header-shadow {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        .dropdown-menu {
            transition: all 0.3s ease;
            transform-origin: top right;
        }

        .dropdown-menu.hidden {
            transform: scaleY(0);
            opacity: 0;
        }

        .dropdown-menu:not(.hidden) {
            transform: scaleY(1);
            opacity: 1;
        }

        .hover-scale:hover {
            transform: scale(1.05);
            transition: transform 0.2s ease;
        }

        @media (max-width: 768px) {
            .admin-info {
                display: none;
            }
            .admin-info-mobile {
                display: flex;
            }
        }

        @media (min-width: 769px) {
            .admin-info-mobile {
                display: none;
            }
        }
    </style>
</head>
<body>
    <header class="bg-white header-shadow sticky top-0 z-1">
        <div class="container mx-auto px-4 py-3 flex items-center justify-between">
            <!-- Mobile Menu Button -->
            <button id="openSidebar" class="md:hidden text-gray-600 hover:text-blue-600 focus:outline-none">
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="4" x2="20" y1="12" y2="12" />
                    <line x1="4" x2="20" y1="6" y2="6" />
                    <line x1="4" x2="20" y1="18" y2="18" />
                </svg>
            </button>

            <!-- Logo and Title -->
            <div class="flex items-center space-x-3">
                <h1 class="text-xl md:text-2xl font-bold text-gray-800">Dashboard</h1>
            </div>

            <!-- Admin Info (Desktop) -->
            <div class="hidden md:flex items-center space-x-4 admin-info">
                <div class="flex items-center space-x-2">
                    <i class="fas fa-user-shield text-blue-600 text-lg"></i>
                    <span class="text-gray-700 font-medium"><?php echo htmlspecialchars($_SESSION['admin_username'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
                </div>
              
            </div>

            <!-- Admin Info (Mobile) -->
            <div class="md:hidden flex items-center space-x-2 admin-info-mobile">
                <i class="fas fa-user-shield text-blue-600 text-lg"></i>
                <span class="text-gray-700 font-medium"><?php echo htmlspecialchars($_SESSION['admin_username']); ?></span>
            </div>
        </div>
    </header>

    <script>
        // Toggle dropdown menu
        const dropdownToggle = document.getElementById('dropdownToggle');
        const dropdownMenu = document.getElementById('dropdownMenu');

        dropdownToggle.addEventListener('click', () => {
            dropdownMenu.classList.toggle('hidden');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (!dropdownToggle.contains(e.target) && !dropdownMenu.contains(e.target)) {
                dropdownMenu.classList.add('hidden');
            }
        });
    </script>
</body>
</html>