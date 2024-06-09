<?php
// Mulai sesi
session_start();

// Include file koneksi database atau file konfigurasi lainnya
require_once "config.php";

// Inisialisasi variabel
$email = "";
$email_err = "";
$password = "";
$password_err = "";
$confirm_password = "";
$confirm_password_err = "";

// Membuat koneksi ke database menggunakan PDO
try {
    $pdo = new PDO("mysql:host=localhost;dbname=users", "faisal", "123");
    // Set atribut PDO untuk mode error
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    // Menampilkan pesan kesalahan jika koneksi gagal
    echo "Koneksi ke database gagal: " . $e->getMessage();
    exit(); // Keluar dari skrip jika koneksi gagal
}

// Memproses formulir ketika disubmit
if($_SERVER["REQUEST_METHOD"] == "POST"){
    
    // Memvalidasi email
    if(empty(trim($_POST["email"]))){
        $email_err = "Masukkan alamat email.";
    } else{
        $email = trim($_POST["email"]);
    }

    // Memvalidasi password
    if(empty(trim($_POST["password"]))){
        $password_err = "Masukkan password baru.";
    } elseif(strlen(trim($_POST["password"])) < 6){
        $password_err = "Password harus memiliki setidaknya 6 karakter.";
    } else{
        $password = trim($_POST["password"]);
    }

    // Memvalidasi konfirmasi password
    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = "Konfirmasi password.";
    } else{
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($password_err) && ($password != $confirm_password)){
            $confirm_password_err = "Password tidak sesuai.";
        }
    }
    
    // Memeriksa kesalahan masukan sebelum melakukan reset password
    if(empty($email_err) && empty($password_err) && empty($confirm_password_err)){
        // Cek apakah email pengguna ada di database
        $sql = "SELECT id FROM users WHERE email = :email";
        
        if($stmt = $pdo->prepare($sql)){
            // Bind variabel ke pernyataan persiapan sebagai parameter
            $stmt->bindParam(":email", $param_email, PDO::PARAM_STR);
            
            // Set parameter
            $param_email = trim($_POST["email"]);
            
            // Mencoba mengeksekusi pernyataan persiapan
            if($stmt->execute()){
                // Jika email ditemukan, update password
                if($stmt->rowCount() == 1){
                    // Siapkan pernyataan UPDATE
                    $sql = "UPDATE users SET password = :password WHERE email = :email";
                    
                    if($stmt = $pdo->prepare($sql)){
                        // Bind variabel ke pernyataan persiapan sebagai parameter
                        $stmt->bindParam(":password", $param_password, PDO::PARAM_STR);
                        $stmt->bindParam(":email", $param_email, PDO::PARAM_STR);
                        
                        // Set parameter
                        $param_password = password_hash($password, PASSWORD_DEFAULT); // Membuat hash password baru
                        $param_email = $email;
                        
                        // Mencoba mengeksekusi pernyataan persiapan
                        if($stmt->execute()){
                            // Password berhasil direset. Alihkan ke halaman login
                            header("location: login.php");
                            exit();
                        } else{
                            echo "Oops! Terjadi kesalahan. Silakan coba lagi nanti.";
                        }
                    }
                    
                    // Menutup pernyataan
                    unset($stmt);
                } else{
                    // Jika email tidak ditemukan, tampilkan pesan kesalahan
                    $email_err = "Tidak ada akun terkait dengan alamat email tersebut.";
                }
            } else{
                echo "Oops! Terjadi kesalahan. Silakan coba lagi nanti.";
            }
        }
        
        // Menutup koneksi
        unset($pdo);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
</head>

<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f2f2f2;
        margin: 0;
        padding: 0;
    }
    h2 {
        text-align: center;
        margin-top: 50px;
    }
    form {
        width: 300px;
        margin: 0 auto;
        padding: 20px;
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }
    form div {
        margin-bottom: 10px;
    }
    label {
        display: block;
        margin-bottom: 5px;
    }
    input[type="email"], input[type="password"] {
        width: 100%;
        padding: 5px;
        border-radius: 5px;
        border: 1px solid #ccc;
    }
    input[type="submit"] {
        width: 100%;
        padding: 5px;
        border-radius: 5px;
        border: none;
        background: #333;
        color: #fff;
        cursor: pointer;
    }
    span {
        color: red;
    }
</style>
<body>
    <h2>Reset Password</h2>
    <p>Masukkan alamat email dan password baru Anda untuk mereset password.</p>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <div>
            <label>Alamat Email</label>
            <input type="email" name="email" value="<?php echo $email; ?>">
            <span><?php echo $email_err; ?></span>
        </div>
        <div>
            <label>Password Baru</label>
            <input type="password" name="password" value="<?php echo $password; ?>">
            <span><?php echo $password_err; ?></span>
        </div>
        <div>
            <label>Konfirmasi Password Baru</label>
            <input type="password" name="confirm_password" value="<?php echo $confirm_password; ?>">
            <span><?php echo $confirm_password_err; ?></span>
        </div>
        <div>
            <input type="submit" value="Reset Password">
        </div>
    </form>
</body>
</html>
