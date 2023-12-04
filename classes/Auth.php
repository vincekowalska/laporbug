<?php
class Auth
{
    private $twig, $pdo;

    public function __construct($twig, $db)
    {
        $this->twig = $twig;
        $this->pdo = $db->getConnection();
    }

    public function landing_page() {
        return $this->twig->render('landing_page.html');
    }
    public function index($token) {
        // echo "<script> alert ('$token') </script>";
        return $this->twig->render('login.html',['token' => $token]);
    }
    
    public function verifyLogin($requests)
    {
        $email = $requests['email'];
        $password = $requests['password'];
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_logged_in'] = true;
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['phone'] = $user['phone_number'];

                header("Location: index.php?route=dashboard");
            }
            echo '<script>alert("Wrong Username or Password."); window.location.href = "index.php?route=login";</script>';
        } catch (PDOException $e) {
            echo "Database error";
            return false;
        }
    }

    public function registrasi($token)
    {
        return $this->twig->render('registrasi.html', ['token'=>$token]);
    }

    public function simpanRegistrasi($requests)
    {
        $name = $requests['name'];
        $email = $requests['email'];
        $password = $requests['password'];
        $role = "student";
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT); #Brypt
        $sql = "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)";
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$name, $email, $hashedPassword, $role]);
            if ($stmt->rowCount() > 0) {
                return true;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            return false;
        }
    }

    public function logout()
    {
        $cek = self::isLoggedIn();
        if ($cek === true) {
            session_unset();
            session_destroy();
        }
        header("Location: index.php?route=login");
    }

    public function isLoggedIn()
    {
        return isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true;
    }

    public function getUserId() {
        return $_SESSION['user_id'];
    }

    public function getRole() {
        return $_SESSION['user_role'];
    }

    public function getUserName() {
        
        return $_SESSION['user_name'];
    }

    public function getUserEmail() {
        return $_SESSION['email'];
    }

    public function getUserPhoneNumber() {
        return $_SESSION['phone'];
    }
    
    public function getUserInfo() {
        $id = $this->getUserId();
        $sql = $this->pdo->prepare("SELECT id,name,email,phone_number,role,profile_picture FROM users WHERE id = ? ");
        try {
            $sql->execute([$id]);
            $user = $sql->fetch(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            return "<script> alert ('Error'); window.location.href='index.php?route=profile' </script>";
        }
        $id = $user['id'];
        $name = $user['name'];
        $email = $user['email'];
        $role = $user['role'];
        $phone_number = $user['phone_number'];
        $profile_picture = $user['profile_picture'];

        $info_lengkap ['user_id'] = $id;
        $info_lengkap ['name'] = $name;
        $info_lengkap ['role']= $role;
        $info_lengkap ['email']= $email;
        $info_lengkap ['phone']= $phone_number;
        $info_lengkap ['profile_picture']= $profile_picture;
        return $info_lengkap;
    }

    public function isAdmin()
    {
        return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
    }

    public function isStudent()
    {
        return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'student';
    }
}
?>