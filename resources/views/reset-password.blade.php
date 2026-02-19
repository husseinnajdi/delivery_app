<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
    <style>
        body {
            font-family: Arial;
            background: #f4f6f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .card {
            background: white;
            padding: 30px;
            width: 350px;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
        }
        button {
            width: 100%;
            padding: 10px;
            background: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }
    </style>
</head>
<body>

<div class="card">
    <h2>Reset Password</h2>
    <input type="email" id="email" placeholder="Email">
    <input type="text" id="otp" placeholder="OTP Code">
    <input type="password" id="password" placeholder="New Password">
    <button onclick="resetPassword()">Reset Password</button>
</div>

<script>
function resetPassword() {
    fetch("http://127.0.0.1:8000/api/reset-password", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({
            email: document.getElementById("email").value,
            otp: document.getElementById("otp").value,
            password: document.getElementById("password").value
        })
    })
    .then(res => res.json())
    .then(data => alert(data.message || data.error));
}
</script>

</body>
</html>
