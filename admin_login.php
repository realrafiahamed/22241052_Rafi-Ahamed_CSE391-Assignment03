<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f0f2f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .login-box {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px #aaa;
            width: 300px;
            text-align: center;
        }
        .info-box{
            background:#e9ecef;
            color:#333;
            padding:10px;
            border-radius:6px;
            margin-bottom:20px;
            font-size:14px;
        }
        h2 {
            margin-bottom: 30px;
            color: #333;
        }
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0 20px 0;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        input[type="submit"] {
            padding: 12px;
            width: 100%;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #0056b3;
        }
        .home-btn {
            display: block;
            margin-top: 15px;
            padding: 10px;
            text-decoration: none;
            background: #6c757d;
            color: white;
            border-radius: 8px;
        }
        .home-btn:hover {
            background: #5a6268;
        }
    </style>
</head>
<body>
<div class="login-box">
    <h2>Admin Login</h2>

    <div class="info-box">
        Default Admin Login (For Testing)<br>
        Username: <b>admin</b><br>
        Password: <b>admin123</b>
    </div>
    
    <form action="admin_login_process.php" method="post">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="submit" value="Login">
    </form>
    <a href="index.php" class="home-btn">Back to Homepage</a>
</div>
</body>
</html>