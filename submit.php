<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to submit an assessment.");
}

$user_id = $_SESSION['user_id'];
$first_name = $_SESSION['first_name'] ?? '';
$username = $_SESSION['username'] ?? '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Get form data
    $name = $_POST['name'] ?? '';
    $data = [
        'income' => (float)($_POST['income'] ?? 0),
        'loan_amount' => (float)($_POST['loan_amount'] ?? 0),
        'loan_term' => (int)($_POST['loan_term'] ?? 0),
        'credit_score' => (float)($_POST['credit_score'] ?? 0),
        'previous_defaults' => ($_POST['previous_defaults'] == "1") ? 1 : 0
    ];

    // Call Flask API
    $ch = curl_init('http://127.0.0.1:5000/predict');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POST, 1);
    $response = curl_exec($ch);

    if ($response === false) {
        die("Error calling prediction API: " . curl_error($ch));
    }
    curl_close($ch);

    $result = json_decode($response, true);

    if (isset($result['prediction'])) {
        $prediction = $result['prediction'];
        $explanation = $result['explanation'] ?? [];
        $message = $prediction == 1 ? "Loan Approved" : "Loan Denied";

        // Save to MySQL
        $conn = new mysqli("127.0.0.1", "root", "", "loan_system", 3306);
        if ($conn->connect_error) {
            die("MySQL Connection failed: " . $conn->connect_error);
        }

        $stmt = $conn->prepare("INSERT INTO loan_applications (name, income, loan_amount, loan_term, credit_score, previous_defaults, prediction, submitted_at, user_id) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), ?)");
        $stmt->bind_param(
            "sdddddii",
            $name,
            $data['income'],
            $data['loan_amount'],
            $data['loan_term'],
            $data['credit_score'],
            $data['previous_defaults'],
            $prediction,
            $user_id
        );
        $stmt->execute();
        $stmt->close();
        $conn->close();
    } else {
        $message = "Error: " . ($result['error'] ?? 'Unknown error from API');
    }

    // Display result safely
    echo "<h2>Assessment Result for " . htmlspecialchars($name) . ":</h2>";
    echo "<p><strong>Status:</strong> " . htmlspecialchars($message) . "</p>";

    if (!empty($explanation) && $prediction == 0) {
        echo "<h4>Reason(s) for Denial:</h4><ul>";
        foreach ($explanation as $reason) {
            echo "<li>" . htmlspecialchars($reason) . "</li>";
        }
        echo "</ul>";
    }
}
?>
