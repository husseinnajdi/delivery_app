<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Reset Password</title>
<style>
    /* Basic Reset */
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

    /* Responsive */
    @media (max-width: 480px) {
        .card {
            padding: 30px 20px;
        }

        .card h2 {
            font-size: 1.5em;
        }

        input, button {
            padding: 12px;
            font-size: 0.95em;
        }
    }

    /* Success & Error alert styles */
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
    <input type="email" id="email" placeholder="Email">
    <input type="text" id="otp" placeholder="OTP Code">
    <input type="password" id="password" placeholder="New Password">
    <button onclick="resetPassword()">Reset Password</button>

    <div id="alert" class="alert" style="display:none;"></div>
</div>

<script>
function resetPassword() {
    const alertBox = document.getElementById('alert');
    alertBox.style.display = 'none';

    fetch("https://delivery-app-ebex.onrender.com/api/reset-password", {
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
    .then(data => {
        alertBox.style.display = 'block';
        if (data.message) {
            alertBox.className = 'alert alert-success';
            alertBox.innerText = data.message;
        } else if (data.error) {
            alertBox.className = 'alert alert-error';
            alertBox.innerText = data.error;
        }
    })
    .catch(err => {
        alertBox.style.display = 'block';
        alertBox.className = 'alert alert-error';
        alertBox.innerText = 'Something went wrong. Please try again.';
    });
}
</script>

</body>
</html>
