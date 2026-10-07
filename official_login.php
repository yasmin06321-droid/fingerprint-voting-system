<?php 
include('db.php');
session_start();

// Redirect if already logged in
if (isset($_SESSION['official_id'])) {
    header("Location: official_dashboard.php");
    exit();
}

$error = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    // Use prepared statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM officials WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows>0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            // Set session variables
            $_SESSION['official_id'] = $row['official_id'];
            $_SESSION['username'] = $row['username'];
           
            
            // Update last login
            $update_stmt = $conn->prepare("UPDATE officials SET last_login = NOW() WHERE official_id = ?");
            $update_stmt->bind_param("i", $row['official_id']);
            $update_stmt->execute();
            
            // Log login action
            $audit_stmt = $conn->prepare("INSERT INTO audit_log (official_id, action, timestamp) VALUES (?, 'login', NOW())");
            $audit_stmt->bind_param("i", $row['official_id']);
            $audit_stmt->execute();
            header("Location: official_dashboard.php");
            exit();
        } else {
            $error = "Invalid password";
        }
    } else {
        $error = "Username not found";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Election Official Login - Fingerprint Voting System</title>
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
                        <li class="nav-item"><a class="nav-link text-white" href="index.html">Home</a></li>
                        <li class="nav-item"><a class="nav-link text-white" href="registration.php">Voter Registration</a></li>
                        <li class="nav-item"><a class="nav-link text-white" href="registervoter.php">Cast Your Vote</a></li>
                        <li class="nav-item"><a class="nav-link active text-white" href="official_login.php">Election Official</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </header>

    <div class="container my-5">
        <div class="card mx-auto" style="max-width: 500px;">
            <div class="card-header bg-primary text-white">
                <h2 class="text-center">Election Official Login</h2>
            </div>
            <div class="card-body">
                <?php if($error): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                
                <form method="post" action="official_login.php">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100">Login</button>
                </form>
                
                <p class="mt-3 text-center">Don't have an account? <a href="official_register.php">Register as Official</a></p>
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