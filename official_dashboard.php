<?php 
include('db.php');
session_start();

// Redirect if not logged in
if (!isset($_SESSION['official_id'])) {
    header("Location: official_login.php");
    exit();
}

// Get voting results
$results = [];
$sql = "SELECT p.party_name, p.party_symbol, COUNT(v.vote_id) as vote_count 
        FROM votes v
        JOIN parties p ON v.party_id = p.party_id
        GROUP BY p.party_id
        ORDER BY vote_count DESC";
$result = $conn->query($sql);
if($result->num_rows>0)
 {
    while($row = $result->fetch_assoc()) {
        $results[] = $row;
    }
}

// Get total votes
$total_votes = 0;
$sql = "SELECT COUNT(*) as total FROM votes";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $total_votes = $row['total'];
}

// Get total registered voters
$total_voters = 0;
$sql = "SELECT COUNT(*) as total FROM voters";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $total_voters = $row['total'];
}

// Get voter turnout percentage
$turnout = $total_voters > 0 ? ($total_votes / $total_voters) * 100 : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Official Dashboard - Fingerprint Voting System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="bg-dark text-white">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center py-3">
                <div class="logo fs-4 fw-bold">Fingerprint Voting</div>
                <nav>
                    <ul class="nav">
                        <li class="nav-item"><a class="nav-link text-white" href="index.php">Home</a></li>
                        <li class="nav-item"><a class="nav-link text-white" href="register.php">Voter Registration</a></li>
                        <li class="nav-item"><a class="nav-link text-white" href="vote.php">Cast Your Vote</a></li>
                        <li class="nav-item"><a class="nav-link active text-white" href="official_dashboard.php">Election Official</a></li>
                    </ul>
                </nav>
                <div class="text-white">
                    Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?> | 
                    <a href="logout.php" class="text-white">Logout</a>
                </div>
            </div>
        </div>
    </header>

    <div class="container my-4">
        <h2 class="mb-4">Election Results Dashboard</h2>
        
        <!-- Summary Cards -->
        <div class="row mb-4">
            <div class="col-md-4 mb-3">
                <div class="card text-white bg-primary h-100">
                    <div class="card-body text-center">
                        <h5 class="card-title">Total Votes</h5>
                        <p class="card-text display-4"><?php echo number_format($total_votes); ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card text-white bg-success h-100">
                    <div class="card-body text-center">
                        <h5 class="card-title">Registered Voters</h5>
                        <p class="card-text display-4"><?php echo number_format($total_voters); ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card text-white bg-info h-100">
                    <div class="card-body text-center">
                        <h5 class="card-title">Voter Turnout</h5>
                        <p class="card-text display-4"><?php echo round($turnout, 2); ?>%</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Results Table -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h4>Party-wise Results</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Rank</th>
                                <th>Party</th>
                                <th>Symbol</th>
                                <th>Votes</th>
                                <th>Percentage</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($results as $index => $row): ?>
                            <tr>
                                <td><?php echo $index + 1; ?></td>
                                <td><?php echo htmlspecialchars($row['party_name']); ?></td>
                                <td>
                                    <?php if(!empty($row['party_symbol'])): ?>
                                        <img src="<?php echo htmlspecialchars($row['party_symbol']); ?>" alt="Party Symbol" style="height: 30px;">
                                    <?php endif; ?>
                                </td>
                                <td><?php echo number_format($row['vote_count']); ?></td>
                                <td><?php echo $total_votes > 0 ? round(($row['vote_count'] / $total_votes) * 100, 2) : 0; ?>%</td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Results Chart -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h4>Results Visualization</h4>
            </div>
            <div class="card-body">
                <canvas id="resultsChart" height="100"></canvas>
            </div>
        </div>
        
        <!-- Export Options -->
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4>Export Results</h4>
            </div>
            <div class="card-body text-center">
                <a href="export_results.php?type=csv" class="btn btn-success me-2">
                    <i class="fas fa-file-csv"></i> Export as CSV
                </a>
                <a href="export_results.php?type=pdf" class="btn btn-danger">
                    <i class="fas fa-file-pdf"></i> Export as PDF
                </a>
            </div>
        </div>
    </div>

    <!-- JavaScript Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Chart.js implementation
        const ctx = document.getElementById('resultsChart').getContext('2d');
        const resultsChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: [<?php 
                    echo implode(',', array_map(function($row) { 
                        return "'" . addslashes($row['party_name']) . "'"; 
                    }, $results)); 
                ?>],
                datasets: [{
                    label: 'Votes',
                    data: [<?php echo implode(',', array_column($results, 'vote_count')); ?>],
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.7)',
                        'rgba(54, 162, 235, 0.7)',
                        'rgba(255, 206, 86, 0.7)',
                        'rgba(75, 192, 192, 0.7)',
                        'rgba(153, 102, 255, 0.7)',
                        'rgba(255, 159, 64, 0.7)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value.toLocaleString();
                            }
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.parsed.y.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>