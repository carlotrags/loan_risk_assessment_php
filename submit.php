<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST['name'];
    $data = [
        'income' => $_POST['income'],
        'loan_amount' => $_POST['loan_amount'],
        'loan_term' => $_POST['loan_term'],
        'credit_score' => $_POST['credit_score'],
        'previous_defaults' => $_POST['previous_defaults']
    ];

    // Send data to Flask API
    $ch = curl_init('http://127.0.0.1:5000/predict');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POST, 1);
    $response = curl_exec($ch);
    curl_close($ch);

    $result = json_decode($response, true);

    if (isset($result['prediction'])) {
        $prediction = $result['prediction'];
        $explanation = isset($result['explanation']) ? $result['explanation'] : '';
        $message = $prediction == 1 ? "Loan Approved" : "Loan Denied";

        // Save to MySQL
        $conn = new mysqli("127.0.0.1", "root", "", "loan_system", 3307);
        if ($conn->connect_error) {
            die("MySQL Connection failed: " . $conn->connect_error);
        }

        $stmt = $conn->prepare("INSERT INTO loan_applications (name, income, loan_amount, loan_term, credit_score, previous_defaults, prediction, submitted_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("sdddddi", $name, $data['income'], $data['loan_amount'], $data['loan_term'], $data['credit_score'], $data['previous_defaults'], $prediction);
        $stmt->execute();
        $stmt->close();
        $conn->close();
    } else {
        $message = "Error: " . $result['error'];
    }

    // Show the result
    echo "<h2>Assessment Result:</h2>";
    echo "<p><strong>Status:</strong> $message</p>";

    // Show explanation if denied
    if (isset($explanation) && $prediction == 0) {
        echo "<h4>Reason(s) for Denial:</h4><ul>";
foreach ($explanation as $reason) {
    echo "<li>" . htmlspecialchars($reason) . "</li>";
}

        echo "</ul>";
    }
}
?>
