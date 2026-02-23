<?php
$alertMessage = '';
$alertType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $otp      = trim($_POST['otp'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($email) || empty($otp) || empty($password)) {
        $alertMessage = 'All fields are required.';
        $alertType    = 'error';
    } else {
        $url = 'http://127.0.0.1:8000/api/reset-password';
        
        $payload = json_encode([
            'email'    => $email,
            'otp'      => $otp,
            'password' => $password,
        ]);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json'
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10); // optional timeout

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($response === false) {
            $alertMessage = 'cURL Error: ' . $curlError;
            $alertType    = 'error';
        } else {
            $data = json_decode($response, true);

            if ($httpCode >= 200 && $httpCode < 300) {
                if (!empty($data['message'])) {
                    $alertMessage = $data['message'];
                    $alertType    = 'success';
                } else {
                    $alertMessage = 'Password reset successfully.';
                    $alertType    = 'success';
                }
            } else {
                $alertMessage = $data['error'] ?? 'Unexpected response from server.';
                $alertType    = 'error';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Reset Password</title>
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: linear-gradient(to right, #4CAF50, #81C784);
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        padding: 20px;
    }

    .card {
        background: white;
        padding: 40px 30px;
        max-width: 400px;
        width: 100%;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        text-align: center;
    }

    .card h2 {
        color: #333;
        margin-bottom: 20px;
        font-size: 1.8em;
    }

    input {
        width: 100%;
        padding: 12px 15px;
        margin: 12px 0;
        border: 1px solid #ccc;
        border-radius: 8px;
        font-size: 1em;
        transition: border 0.3s;
    }

    input:focus {
        border-color: #4CAF50;
        outline: none;
    }

    button {
        width: 100%;
        padding: 14px;
        background: #4CAF50;
        color: white;
        font-size: 1em;
        font-weight: bold;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        transition: background 0.3s, transform 0.2s;
        margin-top: 10px;
    }

    button:hover {
        background: #45a049;
        transform: translateY(-2px);
    }

    @media (max-width: 480px) {
        .card { padding: 30px 20px; }
        .card h2 { font-size: 1.5em; }
        input, button { padding: 12px; font-size: 0.95em; }
    }

    .alert {
        padding: 12px;
        margin-top: 15px;
        border-radius: 8px;
        font-weight: bold;
    }
    .alert-success {
        background-color: #d4edda;
        color: #155724;
    }
    .alert-error {
        background-color: #f8d7da;
        color: #721c24;
    }
</style>
</head>
<body>

<div class="card">
    <h2>Reset Password</h2>

    <form method="POST" action="">
        <input type="email"    name="email"    placeholder="Email"        value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
        <input type="text"     name="otp"      placeholder="OTP Code"     value="<?= htmlspecialchars($_POST['otp']   ?? '') ?>">
        <input type="password" name="password" placeholder="New Password">
        <button type="submit">Reset Password</button>
    </form>

    <?php if ($alertMessage): ?>
        <div class="alert alert-<?= $alertType ?>">
            <?= htmlspecialchars($alertMessage) ?>
        </div>
    <?php endif; ?>
</div>

</body>
</html>