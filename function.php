<?php
session_start();
// koneksi ke database
$conn = mysqli_connect('localhost','root','','kpl');

function logUserActivity($userId, $activityType, $activityDescription) {
    // Sesuaikan dengan koneksi database Anda
    $conn = mysqli_connect('localhost','root','','kpl');
    
    if ($conn->connect_error) {
        die("Koneksi database gagal: " . $conn->connect_error);
    }

    $userId = intval($userId); // Pastikan userId adalah integer
    $activityType = $conn->real_escape_string($activityType);
    $activityDescription = $conn->real_escape_string($activityDescription);
    
    $query = "INSERT INTO user_activity (user_id, activity_type, activity_description) 
              VALUES ($userId, '$activityType', '$activityDescription')";
    
    if ($conn->query($query) === TRUE) {
        echo "Aktivitas berhasil direkam";
    } else {
        echo "Error: " . $query . "<br>" . $conn->error;
    }
    
    $conn->close();
}

//Register
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["register"])) {
    // jika tombol register diklik
    $email = $_POST['email'];
    $password = $_POST['password']; // inputan tanpa enkripsi
    $role = $_POST["role"];
   
    // fungsi password enkripsi
    $epassword = password_hash($password, PASSWORD_DEFAULT);

    // insert to db menggunakan prepared statement
    $stmt = $conn->prepare("INSERT INTO users (email, password, role) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $email, $epassword, $role);

    if ($stmt->execute()) {
        // Registrasi berhasil
        // Setelah berhasil registrasi, catat aktivitas
        $lastInsertedId = $stmt->insert_id; // Ambil ID pengguna yang baru disisipkan
        $_SESSION['user_id'] = $lastInsertedId; // Setel sesi 'user_id' dengan nilai ID pengguna
    
        $activityType = "Register";
        $activityDescription = "Pengguna dengan ID $lastInsertedId berhasil mendaftar pada " . date('Y-m-d H:i:s');
        logUserActivity($lastInsertedId, $activityType, $activityDescription);
    
        header('location:signin.php'); // redirect ke halaman login
    } else {
        // Registrasi gagal
        echo '
        <script>
            alert("REGISTRASI GAGAL");
            window.location.href="signup.php"
        </script>
        ';
    }

    $stmt->close();
    $conn->close();
}

// Login
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["login"])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role']; // Ambil role dari formulir
    
    // Query database berdasarkan email
    $stmt = $conn->prepare("SELECT id_user, email, password, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows == 1) {
        $stmt->bind_result($userId, $email, $hashed_password, $dbRole);
        $stmt->fetch();
    
        if (password_verify($password, $hashed_password) && $role === $dbRole) {
            // Password benar dan role sesuai
            // Setelah pengguna berhasil login
            $_SESSION['user_id'] = $userId; // Setel ID pengguna
            $_SESSION['email'] = $email;   // Setel email pengguna
            $_SESSION['role'] = $dbRole;   // Setel peran pengguna

            // Catat aktivitas login
            $activityType = "Login";
            $activityDescription = "Pengguna dengan ID $userId berhasil login pada " . date('Y-m-d H:i:s');
            logUserActivity($userId, $activityType, $activityDescription);

            if (isset($_POST['remember_me'])) {
                // Jika "Remember Me" dicentang, set cookie yang berlaku selama 30 hari
                setcookie("user_id", $userId, time() + 30 * 24 * 60 * 60, "/");
                setcookie("email", $email, time() + 30 * 24 * 60 * 60, "/");
                setcookie("role", $dbRole, time() + 30 * 24 * 60 * 60, "/");
            }
            
            // Redirect pengguna berdasarkan peran
            if ($dbRole === "admin") {
                header("location: admin.php"); // Redirect ke halaman admin
            } elseif ($dbRole === "pengguna") {
                header("location: index.php"); // Redirect ke halaman pengguna biasa
            } elseif ($dbRole === "petugas") {
                header("location: petugas.php"); // Redirect ke halaman pengguna biasa
            } else {
                // Handle peran lain (jika ada) atau tindakan khusus
                echo "Tidak ada akses yang sesuai dengan peran ini.";
            }
            exit; // Pastikan untuk keluar dari skrip setelah pengalihan
        } else {
            // Password salah atau role tidak sesuai
            handleFailedLogin();
        }
    } else {
        // Email tidak ditemukan
        handleFailedLogin();
    }
}

function handleFailedLogin() {
    if (!isset($_SESSION['failed'])) {
        $_SESSION['failed'] = 0;
    }
    $_SESSION['failed']++;
    
    // Batasi jumlah percobaan gagal yang diizinkan
    $maxFailedAttempts = 5;
    
    if ($_SESSION['failed'] >= $maxFailedAttempts) {
        // Blokir akun atau terapkan tindakan lain jika mencapai batas
        // ...
        echo "Akun Anda diblokir karena terlalu banyak percobaan login yang gagal.";
    } else {
        // Hitung waktu tunda (misalnya, 2^percobaan gagal detik)
        $delay = pow(2, $_SESSION['failed']);
        $_SESSION['delayto'] = strtotime("+{$delay} seconds");
    }
}


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["logout"])) {
    // Pastikan sesi 'user_id' ada sebelum logout
    if (isset($_SESSION['user_id']))  {

        // Hapus cookie yang mungkin telah disetel sebelumnya
        setcookie("user_id", "", time() - 3600, "/");
        setcookie("email", "", time() - 3600, "/");
        setcookie("role", "", time() - 3600, "/");
 
        // Catat aktivitas logout
        $userId = $_SESSION['user_id'];
        $role = $_SESSION['role']; // Ambil role pengguna

        $activityType = "Logout";
        $activityDescription = "Pengguna dengan ID $userId (Role: $role) berhasil logout pada " . date('Y-m-d H:i:s');
        logUserActivity($userId, $activityType, $activityDescription);

        // Hapus semua sesi
        session_unset();
        session_destroy();

        // Redirect pengguna ke halaman signin.php atau halaman lain yang sesuai
        header("location: signin.php");
    } else {
        // Jika sesi 'user_id' tidak ada, mungkin pengguna belum login
        // Redirect pengguna ke halaman signin.php atau halaman lain yang sesuai
        header("location: signin.php");
    }
}
?>