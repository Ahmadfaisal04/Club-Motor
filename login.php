<?php 
session_start();
require_once("config.php");

// Inisialisasi variabel session untuk melacak jumlah percobaan login gagal
if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
}

// Periksa apakah login disubmit
if (isset($_POST['login'])) {
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);

    $sql = "SELECT * FROM users WHERE username=:username OR email=:email";
    $stmt = $db->prepare($sql);

    // bind parameter ke query
    $params = array(
        ":username" => $username,
        ":email" => $username
    );

    $stmt->execute($params);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // jika user terdaftar
    if ($user) {
        // verifikasi password
        if (password_verify($password, $user["password"])) {
            // reset jumlah percobaan login gagal
            $_SESSION['login_attempts'] = 0;

            // buat Session
            $_SESSION["user"] = $user;
            // login sukses, alihkan ke halaman timeline
            header("Location: ./html/home.html#artikel");
            exit();
        } else {
            // Jika login gagal, tambahkan 1 ke jumlah percobaan login gagal
            $_SESSION['login_attempts']++;

            // Jika jumlah percobaan login gagal mencapai tiga kali, tampilkan opsi reset sandi
            if ($_SESSION['login_attempts'] >= 3) {
                // Redirect ke halaman reset sandi
                header("Location: reset.php");
                exit();
            }
        }
    }
}

// Periksa apakah halaman direload, jika iya, set jumlah percobaan login gagal kembali ke 0
if ($_SERVER['REQUEST_METHOD'] === 'GET' && !isset($_POST['login'])) {
    $_SESSION['login_attempts'] = 0;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 400px;
            margin: 100px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .container h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        input[type="submit"] {
            width: 100%;
            padding: 10px;
            border: none;
            border-radius: 5px;
            background-color: #4caf50;
            color: #fff;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
        }
        p {
            text-align: center;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Login</h2>

    <form action="" method="POST">

        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" name="username" placeholder="Username atau email" required />
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" name="password" placeholder="Password" required />
        </div>

        <input type="submit" name="login" value="Masuk" />

    </form>

    <p>Belum punya akun? <a href="register.php">Daftar di sini</a></p>

</div>

</body>
</html>
