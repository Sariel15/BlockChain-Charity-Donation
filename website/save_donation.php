<?php 
session_start();
include("php/config.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $donor_address = mysqli_real_escape_string($con, $_POST['donor_address']);
    $donation_amount = $_POST['donation_amount'];
    $transaction_hash = mysqli_real_escape_string($con, $_POST['transaction_hash']);

    $user_id = $_SESSION['id'];

    $query = mysqli_query($con, "SELECT Username FROM users WHERE Id=$user_id");
    $result = mysqli_fetch_assoc($query);
    $username = $result['Username'];

    if (!empty($donor_address) && !empty($donation_amount) && !empty($transaction_hash) && !empty($username)) {
        $stmt = $con->prepare("INSERT INTO donations (donor_address, donation_amount, transaction_hash, username) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $donor_address, $donation_amount, $transaction_hash, $username);

        if ($stmt->execute()) {
            echo "Donation saved successfully!";
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Invalid data received.";
    }
} else {
    echo "Invalid request method.";
}
?>
