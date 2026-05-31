<?php
require __DIR__ . '/php/session_check.php';
require __DIR__ . '/php/db.php';

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Get total sent
$stmt2 = $conn->prepare("SELECT COALESCE(SUM(amount), 0) as total FROM transactions WHERE user_id = ? AND type = 'sent'");
$stmt2->bind_param("i", $user_id);
$stmt2->execute();
$sent = $stmt2->get_result()->fetch_assoc()['total'];

// Get total received
$stmt3 = $conn->prepare("SELECT COALESCE(SUM(amount), 0) as total FROM transactions WHERE user_id = ? AND type = 'received'");
$stmt3->bind_param("i", $user_id);
$stmt3->execute();
$received = $stmt3->get_result()->fetch_assoc()['total'];

// Get total topup
$stmt4 = $conn->prepare("SELECT COALESCE(SUM(amount), 0) as total FROM transactions WHERE user_id = ? AND type = 'topup'");
$stmt4->bind_param("i", $user_id);
$stmt4->execute();
$topup = $stmt4->get_result()->fetch_assoc()['total'];

// Calculate balance
$balance = $received + $topup - $sent;

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
<title> Dashboard </title>
<link rel="stylesheet" href="css/Dashboard.css">
    </head>
<body>
    <header><nav>
<h2> Pay<em>Pilot</em> </h2>
<ul>
    <li style="position:relative; cursor:pointer;" onclick="alert('No new notifications')">
    <h4>🔔</h4>
    <span style="position:absolute; top:-5px; right:-5px; background:#e53e3e; color:white; border-radius:50%; width:16px; height:16px; font-size:10px; display:flex; align-items:center; justify-content:center;">0</span>
    </li>
    <li><div class="user-dropdown">
    <div class="user-dropdown">
    <button class="user-btn" onclick="toggleDropdown()">
        <div class="user-avatar"><?php echo strtoupper(substr($user['name'], 0, 2)); ?></div>
        <?php echo $user['name']; ?>
        <span class="chevron">▾</span>
    </button>
    <div id="dropdown-menu" style="display:none; position:absolute; right:20px; background:white; border:1px solid #e4e9f2; border-radius:10px; padding:10px; z-index:100;">
        <a href="profile.php" style="display:block; padding:8px 15px; color:#0f1c2e; text-decoration:none;">👤 Profile</a>
        <a href="php/logout.php" style="display:block; padding:8px 15px; color:#e53e3e; text-decoration:none;">🚪 Logout</a>
    </div>
</div>
</div></li>
</ul>
</nav></header>
<h1> Good Morning, <?php echo $user['name']; ?>👏</h1>
<p>Here's what's happening with your account today.</p><br>
<div class="display-card">
<p>Available Balance</p>
<p><span class="balance">PKR <?php echo number_format($balance, 2); ?></span></p>
<div class="cardbox"> 
    <p>SENT <br> <span class="sent">-PKR <?php echo number_format($sent, 2); ?></span></p>
    <div class="divider"></div>
    <p>RECEIVED <br> <span class="received">+PKR <?php echo number_format($received, 2); ?></span></p>
    <div class="divider"></div>
    <p>TOPUP <br> <span style="color:#1a6ef5; font-weight:bold; font-size:larger;">+PKR <?php echo number_format($topup, 2); ?></span></p>
</div>
</div>

<br>
<p>QUICK ACTIONS</p>

<div class="display-card1">
  <div class="cardgrid">
    <div class="gridbox" onclick="window.location.href='Send.html'"><h1>💸</h1><br><p>Send Money</p></div>
    <div class="gridbox" onclick="alert('Request feature coming soon!')"><h1>📥</h1><br><p>Request</p></div>
    <div class="gridbox" onclick="window.location.href='Wallet.html'"><h1>👛</h1><br><p>Top up</p></div>
    <div class="gridbox" onclick="window.location.href='History.html'"><h1>🗒️</h1><br><p>History</p></div>
  </div>
</div>

<div class="table-section">
  
  <!-- Table Header -->
  <div class="table-header">
    <span class="table-title">Recent Transactions</span>
    <a href="History.html" class="table-link">View all →</a>
  </div>

  <!-- Table -->
  <table>
    <thead>
      <tr>
        <th>Name</th>
        <th>Type</th>
        <th>Amount</th>
        <th>Date</th>
        <th>Status</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td class="td-name">Sara Khan</td>
        <td>Sent</td>
        <td class="td-sent">− PKR 2,500</td>
        <td>20 Mar 2026</td>
        <td><span class="badge badge-success">Completed</span></td>
      </tr>
      <tr>
        <td class="td-name">Ahmed Ali</td>
        <td>Received</td>
        <td class="td-received">+ PKR 5,000</td>
        <td>19 Mar 2026</td>
        <td><span class="badge badge-success">Completed</span></td>
      </tr>
      <tr>
        <td class="td-name">Bilal Raza</td>
        <td>Sent</td>
        <td class="td-sent">− PKR 1,500</td>
        <td>17 Mar 2026</td>
        <td><span class="badge badge-danger">Failed</span></td>
      </tr>
    </tbody>
  </table>

</div>

<script>
  document.addEventListener('DOMContentLoaded', function() {

    fetch('php/get_transactions.php')
      .then(function(response) {
        return response.json();
      })
      .then(function(data) {
        renderTable(data);
      });

    function renderTable(data) {
      var tbody = document.querySelector('tbody');
      tbody.innerHTML = '';

      data.forEach(function(row) {
        var amountClass = row.type === 'sent' ? 'td-sent' : 'td-received';
        var amountSign = row.type === 'sent' ? '− PKR ' : '+ PKR ';
        var badgeClass = row.status === 'completed' ? 'badge-success' : 'badge-danger';

        tbody.innerHTML += '<tr>' +
          '<td class="td-name">' + row.recipient + '</td>' +
          '<td>' + row.type + '</td>' +
          '<td class="' + amountClass + '">' + amountSign + row.amount + '</td>' +
          '<td>' + row.created_at + '</td>' +
          '<td><span class="badge ' + badgeClass + '">' + row.status + '</span></td>' +
          '</tr>';
      });
    }

  });

  function toggleDropdown() {
    var menu = document.getElementById('dropdown-menu');
    menu.style.display = menu.style.display === 'none' ? 'block' : 'none';
}

// Close dropdown when clicking outside
document.addEventListener('click', function(e) {
    if (!e.target.closest('.user-dropdown')) {
        document.getElementById('dropdown-menu').style.display = 'none';
    }
});
</script>


</body>
</html>