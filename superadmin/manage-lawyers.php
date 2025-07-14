<?php
// manage-lawyers.php
// Allows superadmin to view and manage lawyer accounts.

session_start();
if (!isset($_SESSION['superadmin_id'])) {
    header("Location: index.php");
    exit();
}

require_once "../config/db.php";

$success_message = "";
$error_message = "";

// Handle status toggle action
if (isset($_GET['toggle_status']) && isset($_GET['id']) && isset($_GET['status'])) {
    $lawyer_id = intval($_GET['id']);
    $current_status = $_GET['status'];
    
    // Determine the new status
    $new_status = ($current_status == 'active') ? 'locked' : 'active';
    
    $sql = "UPDATE lawyers SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $new_status, $lawyer_id);
    
    if ($stmt->execute()) {
        $success_message = "Lawyer status updated successfully.";
    } else {
        $error_message = "Failed to update lawyer status.";
    }
    $stmt->close();
}

// Fetch all lawyers from the database
$sql_all = "SELECT id, username, email, status, created_at FROM lawyers ORDER BY created_at DESC";
$result = $conn->query($sql_all);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SuperAdmin - Manage Lawyers</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
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
                    <li><a href="dashboard.php" class="flex items-center px-4 py-3 text-white hover:bg-purple-700 rounded-lg mx-2">Dashboard</a></li>
                    <li><a href="manage-admins.php" class="flex items-center px-4 py-3 text-white hover:bg-purple-700 rounded-lg mx-2">Manage Admins</a></li>
                    <li><a href="manage-lawyers.php" class="flex items-center px-4 py-3 text-white bg-purple-700 rounded-lg mx-2">Manage Lawyers</a></li>
                    <li><a href="settings.php" class="flex items-center px-4 py-3 text-white hover:bg-purple-700 rounded-lg mx-2">Settings</a></li>
                </ul>
            </nav>
            <div class="p-4 border-t border-purple-700"><a href="logout.php" class="flex items-center text-white hover:text-purple-200">Logout</a></div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <header class="bg-white shadow-sm">
                <div class="flex items-center justify-between p-4">
                    <h1 class="text-xl font-semibold text-gray-800">Manage Lawyer Accounts</h1>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto bg-gray-50 p-4 md:p-6">
                <?php if (!empty($success_message)): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4"><?php echo $success_message; ?></div>
                <?php endif; ?>
                <?php if (!empty($error_message)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4"><?php echo $error_message; ?></div>
                <?php endif; ?>

                <div class="bg-white rounded-lg shadow">
                    <div class="p-4 border-b"><h2 class="text-lg font-semibold text-gray-800">Registered Lawyers</h2></div>
                    <div class="p-4">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Username</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Registered On</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php if ($result->num_rows > 0): ?>
                                        <?php while($row = $result->fetch_assoc()): ?>
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo $row['id']; ?></td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo htmlspecialchars($row['username']); ?></td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($row['email']); ?></td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <?php if ($row['status'] == 'active'): ?>
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                                                    <?php else: ?>
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Locked</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                    <?php
                                                        $action_text = ($row['status'] == 'active') ? 'Lock' : 'Unlock';
                                                        $action_color = ($row['status'] == 'active') ? 'text-red-600 hover:text-red-900' : 'text-green-600 hover:text-green-900';
                                                        $confirm_msg = "Are you sure you want to " . strtolower($action_text) . " this lawyer's account?";
                                                    ?>
                                                    <a href="?toggle_status=1&id=<?php echo $row['id']; ?>&status=<?php echo $row['status']; ?>" class="<?php echo $action_color; ?>" onclick="return confirm('<?php echo $confirm_msg; ?>')">
                                                        <?php echo $action_text; ?>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr><td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">No lawyer accounts found.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>
