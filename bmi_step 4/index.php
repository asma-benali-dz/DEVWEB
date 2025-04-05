<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "bmi_db");

$result = "";
$records = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['delete_id'])) {
        $delete_id = intval($_POST['delete_id']);
        $conn->query("DELETE FROM bmi_records WHERE id = $delete_id");
    } else {
        $name = htmlspecialchars($_POST['name']);
        $weight = floatval($_POST['weight']);
        $height = floatval($_POST['height']);

        if ($weight > 0 && $height > 0) {
            $bmi = $weight / ($height * $height);
            if ($bmi < 18.5) $interpretation = "Underweight";
            elseif ($bmi < 25) $interpretation = "Normal weight";
            elseif ($bmi < 30) $interpretation = "Overweight";
            else $interpretation = "Obesity";

            $stmt = $conn->prepare("INSERT INTO bmi_records (name, weight, height, bmi, interpretation) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sddds", $name, $weight, $height, $bmi, $interpretation);
            $stmt->execute();
            $result = "BMI: " . number_format($bmi, 2) . " ($interpretation)";
        } else {
            $result = "Please enter valid values.";
        }
    }
}

$records_query = $conn->query("SELECT * FROM bmi_records ORDER BY created_at DESC LIMIT 10");
while ($row = $records_query->fetch_assoc()) {
    $records[] = $row;
}
?>

<h2>BMI Calculator</h2>
<a href="logout.php" style="float:right;">Logout</a>
<?php if ($result) echo "<p>$result</p>"; ?>

<form method="POST">
    <label>Name</label><br>
    <input type="text" name="name" required><br>
    <label>Weight (kg)</label><br>
    <input type="number" name="weight" step="0.01" required><br>
    <label>Height (m)</label><br>
    <input type="number" name="height" step="0.01" required><br><br>
    <button type="submit">Calculate</button>
</form>

<h3>Recent Records</h3>
<table border="1" cellpadding="5">
    <tr>
        <th>Name</th><th>Weight</th><th>Height</th><th>BMI</th><th>Interpretation</th><th>Time</th><th>Action</th>
    </tr>
    <?php foreach ($records as $r): ?>
        <tr>
            <td><?= $r['name'] ?></td>
            <td><?= $r['weight'] ?></td>
            <td><?= $r['height'] ?></td>
            <td><?= number_format($r['bmi'], 2) ?></td>
            <td><?= $r['interpretation'] ?></td>
            <td><?= $r['created_at'] ?></td>
            <td>
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="delete_id" value="<?= $r['id'] ?>">
                    <button type="submit">Delete</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
</table>
