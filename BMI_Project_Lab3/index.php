<?php
session_start();
require 'config/database.php';
require 'models/BmiModel.php';
require 'controllers/BmiController.php';

$model = new BmiModel($db);
$controller = new BmiController($model);

if (!isset($_SESSION['user']) && isset($_POST['username'])) {
    $stmt = $db->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->execute([':username' => $_POST['username']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user && password_verify($_POST['password'], $user['password'])) {
        $_SESSION['user'] = $user;
    } else {
        $error = "Invalid credentials.";
    }
}

if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit;
}

$result = null;
$bmiHistory = [];

if (isset($_SESSION['user'])) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['weight'])) {
        $result = $controller->calculateBmi($_SESSION['user']['id'], $_POST['name'], $_POST['weight'], $_POST['height']);
    }
    $bmiHistory = $model->getUserBmiHistory($_SESSION['user']['id']);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>BMI Calculator</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="container mt-5">
    <h2 class="mb-4">BMI Calculator</h2>

    <?php if (!isset($_SESSION['user'])): ?>
        <form method="POST" class="mb-4">
            <input type="text" name="username" placeholder="Username" class="form-control mb-2" required>
            <input type="password" name="password" placeholder="Password" class="form-control mb-2" required>
            <button class="btn btn-success">Login</button>
            <?php if (isset($error)): ?>
                <div class="text-danger mt-2"><?= $error ?></div>
            <?php endif; ?>
        </form>
    <?php else: ?>
        <form method="POST" class="mb-3">
            <button name="logout" class="btn btn-danger">Logout</button>
        </form>
        <?php include 'views/bmi_form.php'; ?>
        <?php include 'views/bmi_result.php'; ?>

        <?php if (!empty($bmiHistory)): ?>
            <h4 class="mt-4">BMI Chart</h4>
            <canvas id="bmiChart" height="100"></canvas>
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script>
                const labels = <?= json_encode(array_column($bmiHistory, 'timestamp')) ?>;
                const data = <?= json_encode(array_column($bmiHistory, 'bmi')) ?>;
                new Chart(document.getElementById('bmiChart'), {
                    type: 'line',
                    data: {
                        labels,
                        datasets: [{
                            label: 'BMI History',
                            data,
                            borderColor: 'blue',
                            tension: 0.3
                        }]
                    }
                });
            </script>
        <?php endif; ?>
    <?php endif; ?>
</body>
</html>
