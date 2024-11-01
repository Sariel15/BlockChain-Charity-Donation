<?php
session_start();

include("php/config.php");

if (!isset($_SESSION['valid'])) {
    header("Location: user-login.php");
    exit();
}

if (!isset($_SESSION['id'])) {
    header("Location: user-login.php");
    exit();
}

// Check if the MetaMask address is set in the session, otherwise use the default value
if (!isset($_SESSION['metamask_address'])) {
    $_SESSION['metamask_address'] = '0x0B53Db0122c2430590240e5CE54cA1E86542f1df';  // Default value
}

// Retrieve the MetaMask address from the session
$metamask_address = $_SESSION['metamask_address'];

$totalDonationResult = mysqli_query($con, "SELECT SUM(donation_amount) AS total_donations FROM donations");
$totalDonations = mysqli_fetch_assoc($totalDonationResult)['total_donations'];

$id = $_SESSION['id'];

$stmt = $con->prepare("SELECT * FROM users WHERE Id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$query_result = $stmt->get_result();

if ($query_result->num_rows > 0) {
    $result = $query_result->fetch_assoc();
    $res_Uname = $result['Username'];
    $res_Email = $result['Email'];
    $res_Age = $result['Age'];
    $res_id = $result['Id'];
} else {
    // Handle the case where the user is not found
    echo "User not found.";
    exit();
}

// Fetch the total donation amount
$totalDonationResult = mysqli_query($con, "SELECT SUM(donation_amount) AS total_donations FROM donations");
if ($totalDonationResult) {
    $totalDonations = mysqli_fetch_assoc($totalDonationResult)['total_donations'];
} else {
    $totalDonations = 0;
}

// Fetch the list of donations
$donationResult = mysqli_query($con, "SELECT username, donation_amount, created_at FROM donations ORDER BY created_at ASC");

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/style.css">
    <title>User Home</title>
</head>
<body>
    <div class="nav">
        <div class="logo">
            <a href="user-home.php">
                <img src="giftchain-withoutname.png" alt="GiftChain Logo" class="logo">
            </a>
        </div>

        <div class="right-links">
            <?php echo "<a href='user-changeProfile.php?Id=$res_id'>Change Profile</a>"; ?>
            <a href="php/logout.php"><button class="btn">Logout</button></a>
        </div>
    </div>
    <main>
        <div class="main-box top">
            <div class="top">
                <div class="box">
                    <p>Hello <b><?php echo htmlspecialchars($res_Uname); ?></b>, Welcome!</p>
                </div>
                <div class="box">
                    <p>Where to Donate: <b id="metamask-address"><?php echo htmlspecialchars($metamask_address); ?></b></p>
                </div>
            </div>
            <div class="bottom">
                <div class="box">
                    <h1>Charity Donation Tracker</h1>

                    <p id="status">Connecting to MetaMask...</p>

                    <h3>Make a Donation (ETH)</h3>
                    <input type="number" id="donation-amount" placeholder="Enter donation amount in ETH">

                    <button class="btn" id="donate-button">Donate</button>

                    <div class="summary">
                        <h3>Total Donations: <span id="total-donations">
                            <?php echo $totalDonations ? $totalDonations : '0'; ?>
                        </span> ETH</h3>
                    </div>

                    <div class="donor-list">
                        <?php if (mysqli_num_rows($donationResult) > 0) { ?>
                            <table>
                                <thead>
                                    <tr>
                                        <th>Username</th>
                                        <th>Amount (ETH)</th>
                                        <th>Time</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    while ($row = mysqli_fetch_assoc($donationResult)) {
                                        echo "<tr>";
                                        echo "<td>" . $row['username'] . "</td>";
                                        echo "<td>" . $row['donation_amount'] . " ETH</td>";
                                        echo "<td>" . $row['created_at'] . "</td>";
                                        echo "</tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        <?php } else { ?>
                            <p class="no-data">No donations yet.</p>
                        <?php } ?>
                    </div>
                </div>
            
                <script src="https://cdn.jsdelivr.net/npm/web3/dist/web3.min.js"></script>
                <script src="user-app.js"></script>
            </div>
        </div>
    </main>
</body>
</html>