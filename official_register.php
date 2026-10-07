<?php 
include('db.php');
session_start();

// Redirect if already logged in
if (isset($_SESSION['official_id'])) {
    header("Location: official_dashboard.php");
    exit();
}

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = trim($_POST['full_name']);
    $username = trim($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = trim($_POST['role']);
    // Check if username exists
    $check_stmt = $conn->prepare("SELECT * FROM officials WHERE username = ?");
    $check_stmt->bind_param("s", $username);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows > 0) {
        $error = "Username already exists";
    } else {
        // Insert new official
        $insert_stmt = $conn->prepare("INSERT INTO officials (username, password, full_name, role) VALUES (?, ?, ?, ?)");
        $insert_stmt->bind_param("ssss", $username, $password, $full_name, $role);
        if ($insert_stmt->execute()) {
            $success = "Official registration successful! You can now login.";
        } else {
            $error = "Error: " . $conn->error;
        }
        $insert_stmt->close();
    }
    $check_stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Official Registration - Fingerprint Voting System</title>
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
                        <li class="nav-item"><a class="nav-link active text-white" href="official_login.php">Election Official</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </header>

    <div class="container my-5">
        <div class="card mx-auto" style="max-width: 500px;">
            <div class="card-header bg-primary text-white">
                <h2 class="text-center">Election Official Registration</h2>
            </div>
            <div class="card-body">
                <?php if($error): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                
                <?php if($success): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                <?php endif; ?>
                
                <form method="post" action="official_register.php">
                    <div class="mb-3">
                        <label for="full_name" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="full_name" name="full_name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="role" class="form-label">Role</label>
                        <select class="form-select" id="role" name="role" required>
                            <option value="poll_worker">Poll Worker</option>
                            <option value="admin">Administrator</option>
                            <option value="auditor">Auditor</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100">Register</button>
                </form>
                
                <p class="mt-3 text-center">Already have an account? <a href="official_login.php">Login here</a></p>
            </div>
        </div>
    </div>

    <footer class="bg-dark text-white py-3">
        <div class="container text-center">
            <p>&copy; 2023 Fingerprint Voting System. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>