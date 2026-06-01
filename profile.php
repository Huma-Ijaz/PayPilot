<?php
require __DIR__ . '/php/session_check.php';
require __DIR__ . '/php/db.php';

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>PayPilot — Profile</title>
    <link rel="stylesheet" href="/paypilot/css/profile.css">
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

    <div id="container">
        <h1>My Profile</h1>
        <div id="div1">
            <div class="initials">
                <?php echo strtoupper(substr($user['name'], 0, 2)); ?>
            </div>
            <div class="user-info">
                <div class="username"><?php echo $user['name']; ?></div>
                <div class="profile-email"><?php echo $user['email']; ?></div>
                <div class="verification">Verified</div>
            </div>
        </div>

        <div id="view-mode" style="width:65%; background:white; border-radius:16px; border:1px solid #e4e9f2; overflow:hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.06);">
            <table>
                <tr>
                    <td>
                        <h3 class="info">Account Details</h3>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="icon">📱</div>
                        <div class="info-box">
                            <div class="info-label">Phone</div>
                            <div class="info"><?php echo $user['phone']; ?></div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="icon">🪪</div>
                        <div class="info-box">
                            <div class="info-label">CNIC</div>
                            <div class="info"><?php echo $user['cnic']; ?></div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="icon">📅</div>
                        <div class="info-box">
                            <div class="info-label">Member Since</div>
                            <div class="info"><?php echo date('F Y', strtotime($user['created_at'])); ?></div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="icon">⭐</div>
                        <div class="info-box">
                            <div class="info-label">Account Level</div>
                            <div class="info">Premium</div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <button class="btn" onclick="showEdit()">Edit Profile</button>

        <div id="edit-mode" style="display:none; width:65%; background:white; border-radius:16px; border:1px solid #e4e9f2; padding:24px; box-shadow: 0 2px 10px rgba(0,0,0,0.06);">
            <input type="text" id="edit-name" placeholder="Full Name" value="<?php echo $user['name']; ?>"><br><br>
            <input type="text" id="edit-phone" placeholder="Phone Number" value="<?php echo $user['phone']; ?>"><br><br>
            
            <div class="btn-group">
            <button class="btn" onclick="saveProfile()">Save Changes</button>
            <button class="btn" style="background-color:gray;" onclick="cancelEdit()">Cancel</button>
            </div>
        </div>
    </div>

    <script>
    function showEdit() {
        document.getElementById('view-mode').style.display = 'none';
        document.getElementById('edit-mode').style.display = 'block';
    }

    function cancelEdit() {
        document.getElementById('edit-mode').style.display = 'none';
        document.getElementById('view-mode').style.display = 'block';
    }

    function saveProfile() {
        var name = document.getElementById('edit-name').value;
        var phone = document.getElementById('edit-phone').value;

        if (name === '' || phone === '') {
            alert('Please fill in all fields');
            return;
        }

        var formData = new FormData();
        formData.append('name', name);
        formData.append('phone', phone);

        fetch('php/profile_update.php', {
            method: 'POST',
            body: formData
        })
        .then(function(response) {
            if (response.ok) {
                alert('Profile updated successfully!');
                location.reload();
            }
        });
    }

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