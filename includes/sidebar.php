 <aside id="sidebar" class="sidebar bg-teal-800 text-white w-64 min-h-screen flex flex-col z-10 fixed md:relative">
            <div class="p-4 border-b border-teal-700">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-bold">CasePilot - Agri</h2>
                    <button id="closeSidebar" class="md:hidden text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-x">
                            <path d="M18 6 6 18" />
                            <path d="m6 6 12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <nav class="flex-1 overflow-y-auto py-4">
                <ul class="space-y-1">
                    <li>
                        <a href="dashboard.php" class="flex items-center px-4 py-3 text-white hover:bg-teal-700 bg-teal-700 rounded-lg mx-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-3">
                                <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                                <polyline points="9 22 9 12 15 12 15 22" />
                            </svg>
                            Dashboard
                        </a>
                    </li>
                    <li>
                        <div id="dealerMenu" class="flex items-center justify-between px-4 py-3 text-white hover:bg-teal-600 rounded-lg mx-2 cursor-pointer">
                            <div class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-3">
                                    <path d="M12 21a9 9 0 0 0 9-9c0-5-4-9-9-9s-9 4-9 9a9 9 0 0 0 9 9z" />
                                    <path d="M12 3v9l3-3m-3 3l-3-3" />
                                </svg>
                                Dealers
                            </div>
                            <svg id="dealerMenuArrow" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="transform transition-transform duration-200">
                                <polyline points="6 9 12 15 18 9"></polyline>
                            </svg>
                        </div>
                        <ul id="dealerSubmenu" class="ml-8 mt-1">
                            <li>
                                <a href="fertilizer_dealers.php" class="flex items-center px-4 py-2 text-white hover:bg-teal-500 rounded-lg transition-colors duration-200">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                                        <path d="M12 2v4" />
                                        <path d="m16.24 7.76 2.83-2.83" />
                                        <path d="M18 12h4" />
                                        <path d="m16.24 16.24 2.83 2.83" />
                                        <path d="M12 18v4" />
                                        <path d="m4.93 19.07 2.83-2.83" />
                                        <path d="M2 12h4" />
                                        <path d="m4.93 4.93 2.83 2.83" />
                                    </svg>
                                    Fertilizer Dealers
                                </a>
                            </li>
                            <li>
                                <a href="pesticide_dealers.php" class="flex items-center px-4 py-2 text-white hover:bg-teal-500 rounded-lg transition-colors duration-200">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                                    </svg>
                                    Pesticide Dealers
                                </a>
                            </li>
                            <li>
                                <a href="cancelled_dealers.php" class="flex items-center px-4 py-2 text-white hover:bg-teal-700 rounded-lg transition-colors duration-200">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <line x1="4.93" y1="4.93" x2="19.07" y2="19.07"></line>
                                    </svg>
                                    Cancelled Dealers
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li>
                <a href="staff.php" class="flex items-center px-4 py-3 text-white hover:bg-teal-500 rounded-lg mx-3 transition-colors duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-3">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                        <circle cx="12" cy="7" r="4" />
                    </svg>
                    Staff
                </a>
            </li>
                    <li>
                        <a href="visits.php" class="flex items-center px-4 py-3 text-white hover:bg-teal-700 rounded-lg mx-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-3">
                                <path d="M21 12H3" />
                                <path d="M12 3v18" />
                                <path d="M18 6l3 3-3 3" />
                                <path d="M6 18l-3-3 3-3" />
                            </svg>
                            Visits
                        </a>
                    </li>
                    <li>
                        <a href="samples.php" class="flex items-center px-4 py-3 text-white hover:bg-teal-700 rounded-lg mx-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-3">
                                <path d="M12 20h9" />
                                <path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4z" />
                            </svg>
                            Samples
                        </a>
                    </li>
                    <li>
                        <a href="stock.php" class="flex items-center px-4 py-3 text-white hover:bg-teal-700 rounded-lg mx-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-3">
                                <path d="M4 4h16v2H4z" />
                                <path d="M6 8h12v12H6z" />
                                <path d="M8 12h8" />
                                <path d="M8 16h8" />
                            </svg>
                            Stock Availability
                        </a>
                    </li>
                    <li>
                        <a href="financial_management.php" class="flex items-center px-4 py-3 text-white  rounded-lg mx-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-3">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                                <circle cx="9" cy="7" r="4" />
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                                <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                            </svg>
                            Financial Management
                        </a>
                    </li>
                    
                </ul>
            </nav>

            <div class="p-4 border-t border-teal-700">
                <a href="logout.php" class="flex items-center text-white hover:text-teal-200">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-3">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                        <polyline points="16 17 21 12 16 7" />
                        <line x1="21" x2="9" y1="12" y2="12" />
                    </svg>
                    Logout
                </a>
            </div>
        </aside>

<script>
    document.getElementById('dealerMenu').addEventListener('click', function() {
        const submenu = document.getElementById('dealerSubmenu');
        const arrow = document.getElementById('dealerMenuArrow');
        if (submenu.classList.contains('hidden')) {
            submenu.classList.remove('hidden');
            arrow.classList.add('rotate-180');
        } else {
            submenu.classList.add('hidden');
            arrow.classList.remove('rotate-180');
        }
    });
</script>