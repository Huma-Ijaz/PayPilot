<?php
require __DIR__ . '/php/session_check.php';
require __DIR__ . '/php/db.php';

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i",$user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// sent
$stmt2 = $conn->prepare("
SELECT COALESCE(SUM(amount),0) total
FROM transactions
WHERE user_id=? AND type='sent'
");
$stmt2->bind_param("i",$user_id);
$stmt2->execute();
$sent = $stmt2->get_result()->fetch_assoc()['total'];


// received
$stmt3 = $conn->prepare("
SELECT COALESCE(SUM(amount),0) total
FROM transactions
WHERE user_id=? AND type='received'
");
$stmt3->bind_param("i",$user_id);
$stmt3->execute();
$received = $stmt3->get_result()->fetch_assoc()['total'];


// topup
$stmt4 = $conn->prepare("
SELECT COALESCE(SUM(amount),0) total
FROM transactions
WHERE user_id=? AND type='topup'
");
$stmt4->bind_param("i",$user_id);
$stmt4->execute();
$topup = $stmt4->get_result()->fetch_assoc()['total'];

$balance = $received + $topup - $sent;
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title> Wallet </title>
<link rel = "stylesheet" href="css/wallet.css">
    </head>
    <body>
        <Header><nav>
<h2> Pay<em>Pilot</em> </h2>
<ul>
    <li> <a href="Dashboard.php"><h4>←Dashboard</h4> </a></li>
    <li><div class="user-dropdown" style="position:relative;">
<button type="button"
        class="user-btn"
        onclick="toggleDropdown()">
        <div class="user-avatar"><?php echo strtoupper(substr($user['name'], 0, 2)); ?></div>
        <?php echo $user['name']; ?>
        <span class="chevron">▾</span>
    </button>
    <div id="dropdown-menu" style="display:none; position:absolute; right:20px; background:white; border:1px solid #e4e9f2; border-radius:10px; padding:10px; z-index:100;">
        <a href="profile.php" style="display:block; padding:8px 15px; color:#0f1c2e; text-decoration:none;">👤 Profile</a>
        <a href="php/logout.php" style="display:block; padding:8px 15px; color:#e53e3e; text-decoration:none;">🚪 Logout</a>
    </div>
</div></li>
</ul>
</nav></Header>

<h1> My Wallet </h1>
<p>Manage your balance and top up anytime.</p><br>
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
<div class="box2">
<div class="card"> 
<h4><pre>💰   Add Money to Wallet</pre></h4> 

<div class="field">
  <label>AMOUNT</label>
  <input type="text" id="amount" name="amount" placeholder="PKR 0.00">
</div>

<div class="amount-buttons">
  <span class="box">500</span>
  <span class="box">1,000</span>
  <span class="box">2,000</span>
  <span class="box">5,000</span>
</div>

<div class="field">
  <label>PAYMENT METHOD</label>
<select name="method" id="method" class="input-field">
      <option value="" selected disabled>Select Payment Method</option>
    <option> Jazzcash</option>
    <option> EasyPaisa</option>
    <option> NayaPay</option></select>
</div>

<div id="success-box" style="display:none; background:#e6f4ea; padding:20px; border-radius:10px; margin-bottom:15px; text-align:center;">
  <h2>🎉</h2>
  <h3 style="color:green;">Top-up Successful!</h3>
  <p style="color:#333;">Your wallet has been topped up successfully.</p>
</div>
<span id="wallet-error" style="color:red; font-size:13px;"></span>
<button class="btn" id="proceed-btn">Proceed →</button>
</div>

<div class="right"> 
<h3 class="section-title">Recent Top-ups</h3>
<br>
<table class="table-section">
    <thead>
      <tr>
        <th>Date</th>
        <th>Source</th>
        <th>Amount</th>
        <th> </th>
      </tr>
    </thead>

  <tbody id="topups-tbody">
  <tr><td colspan="3">Loading...</td></tr>
</tbody>

  </table>
</div></div>

<br><br>
<p>Saved Payment Method</p>
<div class="display-card1">
  <div class="cardgrid">
    <div class="gridbox" onclick="savedMethod('Jazzcash')"><h1>📲</h1><br> <p>Jazzcash*1234</p></div>
    <div class="gridbox" onclick="savedMethod('HBL Bank')"><h1>🏦</h1><br> <p>HBL Bank*5678</p></div>
    <div class="gridbox" onclick="addMethod()"><h1>➕</h1><br> <p>Add New</p></div>
</div></div>

<div class="footer"> </div>

<script>

  // Load recent top-ups from database
  function loadTopups() {
    fetch('php/wallet_topup.php')
    .then(response => response.json())
    .then(data => {
      let tbody = document.getElementById('topups-tbody');
      if (data.length === 0) {
        tbody.innerHTML = '<tr><td colspan="3">No top-ups yet</td></tr>';
        return;
      }
      tbody.innerHTML = '';
      data.forEach(function(row) {
        let date = new Date(row.created_at).toLocaleDateString();
        tbody.innerHTML += `
          <tr>
            <td>${date}</td>
            <td class="td-name">${row.recipient}</td>
            <td class="td-topup">+ PKR ${row.amount}</td>
          </tr>`;
      });
    });
  }

  // Load on page open
  loadTopups();
  // Chip click fills amount
  document.querySelectorAll('.box').forEach(function(btn) {
    btn.addEventListener('click', function() {
      document.getElementById('amount').value = this.textContent.replace(',', '');
    });
  });

  // Proceed button — AJAX
document.getElementById('proceed-btn').addEventListener('click', function() {

    let amount = document.getElementById('amount').value;
    let method = document.getElementById('method').value;

    if (amount === '' || amount <= 0) {
        document.getElementById('wallet-error').textContent =
            'Please enter a valid amount';
        return;
    }

    if (!method) {
        document.getElementById('wallet-error').textContent =
            'Please select a payment method';
        return;
    }

    document.getElementById('wallet-error').textContent = '';

    fetch('php/wallet_topup.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            amount: amount,
            method: method
        })
    })
    .then(response => response.json())
    .then(data => {

        if (data.success) {

            document.getElementById('success-box').style.display = 'block';
            document.getElementById('amount').value = '';
            document.getElementById('method').selectedIndex = 0;

            loadTopups();

            setTimeout(() => {
                location.reload();
            }, 3000);

        } else {

            document.getElementById('wallet-error').textContent =
                '❌ Error: ' + data.error;
        }

    })
    .catch(error => {
        console.error(error);
    });

});   // <-- THIS WAS MISSING

  function toggleDropdown() {

    let menu = document.getElementById("dropdown-menu");

    if(menu.style.display === "block"){
        menu.style.display = "none";
    }else{
        menu.style.display = "block";
    }
}

document.addEventListener('click', function(e) {

    if (!e.target.closest('.user-dropdown')) {
        document.getElementById('dropdown-menu').style.display = 'none';
    }

});

function savedMethod(method){
    alert(method + ' integration coming soon');
}

function addMethod(){
    alert('Add payment method feature coming soon');
}

</script>
</body></html>