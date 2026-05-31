<?php
require __DIR__ . '/php/session_check.php';
require __DIR__ . '/php/db.php';

$user_id = $_SESSION['user_id'];

$stmt0 = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt0->bind_param("i", $user_id);
$stmt0->execute();
$user = $stmt0->get_result()->fetch_assoc();

// Get ALL transactions
$stmt = $conn->prepare("SELECT * FROM transactions WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$all = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Get SENT only
$stmt2 = $conn->prepare("SELECT * FROM transactions WHERE user_id = ? AND type = 'sent' ORDER BY created_at DESC");
$stmt2->bind_param("i", $user_id);
$stmt2->execute();
$sent = $stmt2->get_result()->fetch_all(MYSQLI_ASSOC);

// Get RECEIVED only
$stmt3 = $conn->prepare("SELECT * FROM transactions WHERE user_id = ? AND type = 'received' ORDER BY created_at DESC");
$stmt3->bind_param("i", $user_id);
$stmt3->execute();
$received = $stmt3->get_result()->fetch_all(MYSQLI_ASSOC);

// Get TOPUP only
$stmt4 = $conn->prepare("SELECT * FROM transactions WHERE user_id = ? AND type = 'topup' ORDER BY created_at DESC");
$stmt4->bind_param("i", $user_id);
$stmt4->execute();
$topup = $stmt4->get_result()->fetch_all(MYSQLI_ASSOC);


// Helper function to build table rows
function buildRows($transactions) {
    if (empty($transactions)) {
        echo '<tr><td colspan="6">No transactions found</td></tr>';
        return;
    }
    foreach ($transactions as $i => $row) {
        $amountClass = $row['type'] === 'sent' ? 'td-sent' : ($row['type'] === 'topup' ? 'td-topup' : 'td-received');
        $amountSign = $row['type'] === 'sent' ? '− PKR ' : '+ PKR ';
        $badgeClass = $row['status'] === 'completed' ? 'badge-success' : 'badge-danger';
        echo '
        <tr>
            <td><p>TXN-00' . $row['id'] . '</p></td>
            <td class="td-name">' . $row['recipient'] . '</td>
            <td>' . $row['type'] . '</td>
            <td class="' . $amountClass . '">' . $amountSign . number_format($row['amount'], 2) . '</td>
            <td>' . $row['created_at'] . '</td>
            <td><span class="badge ' . $badgeClass . '">' . $row['status'] . '</span></td>
        </tr>';
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>History</title>
    <link rel="stylesheet" href="css/history.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>
<body>
<header><nav>
    <h2>Pay<em>Pilot</em></h2>
    <ul>
        <li><a href="dashboard.php"><h4>← Dashboard</h4></a></li>
        <li>
            <div class="user-dropdown" style="position:relative;">
                <button class="user-btn" onclick="toggleDropdown()">
                    <div class="user-avatar"><?php echo strtoupper(substr($user['name'], 0, 2)); ?></div>
                    <?php echo $user['name']; ?>
                    <span class="chevron">▾</span>
                </button>
                <div id="dropdown-menu" style="display:none; position:absolute; right:0; background:white; border:1px solid #e4e9f2; border-radius:10px; padding:10px; z-index:100; min-width:150px;">
                    <a href="profile.php" style="display:block; padding:8px 15px; color:#0f1c2e; text-decoration:none;">👤 Profile</a>
                    <a href="php/logout.php" style="display:block; padding:8px 15px; color:#e53e3e; text-decoration:none;">🚪 Logout</a>
                </div>
            </div>
        </li>
    </ul>
</nav></header>

    <div class="set">
        <h2>Transaction History</h2>
        <p>All your past payments and transfers in one place.</p>

        <div class="amount-buttons">
            <span class="box" data-tab="1">All</span>
            <span class="box" data-tab="2">Sent</span>
            <span class="box" data-tab="3">Received</span>
            <span class="box" data-tab="4">Top up</span>
        </div>
        <br>

        <!-- ALL -->
        <div class="table-section" id="1">
            <table>
                <thead>
                    <tr>
                        <th>Transaction ID</th>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Amount</th>
                        <th>Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php buildRows($all); ?>
                </tbody>
            </table>
        </div>

        <!-- SENT -->
        <div class="table-section" id="2">
            <table>
                <thead>
                    <tr>
                        <th>Transaction ID</th>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Amount</th>
                        <th>Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php buildRows($sent); ?>
                </tbody>
            </table>
        </div>

        <!-- RECEIVED -->
        <div class="table-section" id="3">
            <table>
                <thead>
                    <tr>
                        <th>Transaction ID</th>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Amount</th>
                        <th>Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php buildRows($received); ?>
                </tbody>
            </table>
        </div>

        <!-- TOPUP -->
        <div class="table-section" id="4">
            <table>
                <thead>
                    <tr>
                        <th>Transaction ID</th>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Amount</th>
                        <th>Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php buildRows($topup); ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
    $(function() {
        $('#1').show();
        $('[data-tab="1"]').addClass('active');

        $('.box').on('click', function() {
            $('.table-section').hide();
            $('.box').removeClass('active');
            $(this).addClass('active');
            var id = $(this).data('tab');
            $('#' + id).show();
        });
    });

    function toggleDropdown() {
    var menu = document.getElementById('dropdown-menu');
    menu.style.display = menu.style.display === 'none' ? 'block' : 'none';
    }
    document.addEventListener('click', function(e) {
    if (!e.target.closest('.user-dropdown')) {
        document.getElementById('dropdown-menu').style.display = 'none';
    }
    });

    </script>

</body>
</html>