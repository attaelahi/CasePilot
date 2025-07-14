<?php
require_once 'config/database.php';
require_once 'config/session.php';

requireLogin();

try {
    $database = new Database();
    $db = $database->getConnection();
    
    // Get user metrics
    $user_id = $_SESSION['user_id'];
    
    // Active cases count
    $query = "SELECT COUNT(*) as count FROM cases WHERE user_id = :user_id AND status = 'Active'";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $active_cases = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Upcoming hearings (within 7 days)
    $query = "SELECT COUNT(*) as count FROM calendar_events WHERE user_id = :user_id AND event_type = 'hearing' AND date BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 7 DAY)";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $upcoming_hearings = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Tasks due (deadlines within 7 days)
    $query = "SELECT COUNT(*) as count FROM calendar_events WHERE user_id = :user_id AND event_type = 'deadline' AND date BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 7 DAY)";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $tasks_due = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Recent cases
    $query = "SELECT c.id, c.title, ct.name as case_type, c.status, 
                     MIN(ce.date) as next_hearing
              FROM cases c 
              LEFT JOIN case_types ct ON c.case_type_id = ct.id
              LEFT JOIN calendar_events ce ON c.id = ce.case_id AND ce.event_type = 'hearing' AND ce.date > NOW()
              WHERE c.user_id = :user_id 
              GROUP BY c.id, c.title, ct.name, c.status
              ORDER BY c.created_at DESC 
              LIMIT 10";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $recent_cases = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Case distribution for chart
    $query = "SELECT ct.name, COUNT(*) as count 
              FROM cases c 
              JOIN case_types ct ON c.case_type_id = ct.id 
              WHERE c.user_id = :user_id 
              GROUP BY ct.id, ct.name";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $case_distribution = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $exception) {
    $error = 'Database error: ' . $exception->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - CasePilot</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'cp-gray': '#F3F4F6',
                        'cp-blue': '#2563EB',
                        'cp-blue-hover': '#1E40AF',
                        'cp-red': '#EF4444',
                        'cp-red-hover': '#B91C1C'
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-cp-gray min-h-screen">
    <!-- Navigation Header -->
    <nav class="bg-cp-blue text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <h1 class="text-xl font-bold">CasePilot</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-sm">Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?> (<?php echo ucfirst($_SESSION['user_role']); ?>)</span>
                    <a href="logout.php" class="bg-cp-red hover:bg-cp-red-hover px-3 py-2 rounded text-sm font-medium transition duration-200">
                        Logout
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Metrics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-1">
                        <h3 class="text-lg font-medium text-gray-900">Active Cases</h3>
                        <p class="text-3xl font-bold text-cp-blue"><?php echo $active_cases; ?></p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-1">
                        <h3 class="text-lg font-medium text-gray-900">Upcoming Hearings</h3>
                        <p class="text-3xl font-bold text-cp-blue"><?php echo $upcoming_hearings; ?></p>
                        <p class="text-sm text-gray-500">Next 7 days</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-1">
                        <h3 class="text-lg font-medium text-gray-900">Tasks Due</h3>
                        <p class="text-3xl font-bold text-cp-blue"><?php echo $tasks_due; ?></p>
                        <p class="text-sm text-gray-500">Next 7 days</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <a href="#" class="bg-blue-100 hover:bg-blue-200 rounded-lg p-6 transition duration-200 block">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Case Management</h3>
                <p class="text-gray-600">Create and manage your cases</p>
            </a>
            
            <a href="#" class="bg-blue-100 hover:bg-blue-200 rounded-lg p-6 transition duration-200 block">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Calendar</h3>
                <p class="text-gray-600">Schedule hearings and deadlines</p>
            </a>
            
            <a href="#" class="bg-blue-100 hover:bg-blue-200 rounded-lg p-6 transition duration-200 block">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Documents</h3>
                <p class="text-gray-600">Upload and manage case files</p>
            </a>
            
            <a href="#" class="bg-blue-100 hover:bg-blue-200 rounded-lg p-6 transition duration-200 block">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Clients</h3>
                <p class="text-gray-600">Manage client information</p>
            </a>
            
            <a href="#" class="bg-blue-100 hover:bg-blue-200 rounded-lg p-6 transition duration-200 block">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Billing</h3>
                <p class="text-gray-600">Track billable hours</p>
            </a>
            
            <a href="#" class="bg-blue-100 hover:bg-blue-200 rounded-lg p-6 transition duration-200 block">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Reports</h3>
                <p class="text-gray-600">Analytics and insights</p>
            </a>
        </div>

        <!-- Recent Cases Table -->
        <div class="bg-white rounded-lg shadow mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Recent Cases</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Case Title</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Case Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Next Hearing</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if (empty($recent_cases)): ?>
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center text-gray-500">No cases found. Start by creating your first case!</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($recent_cases as $case): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        <?php echo htmlspecialchars($case['title']); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php echo htmlspecialchars($case['case_type']); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                            <?php echo $case['status'] == 'Active' ? 'bg-green-100 text-green-800' : 
                                                     ($case['status'] == 'Pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800'); ?>">
                                            <?php echo htmlspecialchars($case['status']); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php echo $case['next_hearing'] ? date('M j, Y', strtotime($case['next_hearing'])) : 'N/A'; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Case Distribution Chart -->
        <?php if (!empty($case_distribution)): ?>
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Cases by Type</h3>
            <div class="flex justify-center">
                <div class="w-full max-w-md">
                    <canvas id="caseChart"></canvas>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Chart Script -->
    <?php if (!empty($case_distribution)): ?>
    <script>
        const ctx = document.getElementById('caseChart').getContext('2d');
        const caseChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: <?php echo json_encode(array_column($case_distribution, 'name')); ?>,
                datasets: [{
                    data: <?php echo json_encode(array_column($case_distribution, 'count')); ?>,
                    backgroundColor: [
                        'rgba(248, 113, 113, 0.8)', // Red
                        'rgba(96, 165, 250, 0.8)',  // Blue
                        'rgba(251, 191, 36, 0.8)',  // Yellow
                        'rgba(52, 211, 153, 0.8)',  // Green
                        'rgba(168, 85, 247, 0.8)',  // Purple
                        'rgba(245, 101, 101, 0.8)'  // Pink
                    ],
                    borderColor: [
                        'rgba(248, 113, 113, 1)',
                        'rgba(96, 165, 250, 1)',
                        'rgba(251, 191, 36, 1)',
                        'rgba(52, 211, 153, 1)',
                        'rgba(168, 85, 247, 1)',
                        'rgba(245, 101, 101, 1)'
                    ],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((value / total) * 100).toFixed(1);
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    </script>
    <?php endif; ?>
</body>
</html>
