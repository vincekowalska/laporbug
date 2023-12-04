<?php
session_start();
function generateCSRFtoken(){
    $token = bin2hex(random_bytes(32));
    $_SESSION['csrf_token'] = $token;
    $cek = $_SESSION['csrf_token'];
    // echo "<script>alert('$cek') </script>";
    return $token;
}
function isValidCSRFToken($token){
    if (isset($_SESSION['csrf_token']) && $token === $_SESSION['csrf_token']){
        return true;
    }
    return false;
}

require_once 'vendor/autoload.php';
require_once 'classes/Database.php';
require_once 'classes/User.php';
require_once 'classes/Auth.php';
require_once 'classes/Finding.php';
require_once 'classes/Dashboard.php';
require_once 'classes/Report.php';
require_once 'classes/Profile.php';

$db = new Database();

$loader = new Twig\Loader\FilesystemLoader('templates');
$twig = new Twig\Environment($loader);
$twig->addGlobal('session', $_SESSION);

$auth = new Auth($twig, $db);
$users = new User($twig, $db);
$findings = new Finding($twig, $db, $auth);
$dashboard = new Dashboard($twig, $db, $auth);
$profile = new Profile($twig, $db, $auth);

$action = isset($_GET['route']) ? $_GET['route'] : 'landing-page';


switch ($action) {
    case 'landing-page':
        echo $auth->landing_page(); #tampilkan form login
        break;
    case 'login':
        $token =  generateCSRFtoken();
        echo $auth->index($token); #tampilkan form login
        break;
    case 'about_us':
        echo $twig->render('about_us.html');
        break;
    case 'profile':
        $token =  generateCSRFtoken();
        echo $profile->index($token);
        break;
    case 'verify-login':
        $token = $_POST['csrf_token'];
        $verify_token = isValidCSRFToken($token);
        if($verify_token === true){
            $result = $auth->verifyLogin($_POST);
            if ($result === true) {
                header("Location: index.php?route=dashboard");
            } else {
                echo '<script>alert("Wrong Username or Password "); window.location.href = "index.php?route=login";</script>';
            }
        }else{
            echo '<script>alert("Access Denied");window.location.href = "index.php?route=login";</script> </script>';
        }
        break;
    case 'registrasi': #tampilkan form registrasi
        $token =  generateCSRFtoken();
        echo $auth->registrasi($token);
        break;
    case 'simpan-registrasi': #ketika tombol registrasi disimpan
        $token = $_POST['csrf_token'];
        $verify_token = isValidCSRFToken($token);
        if($verify_token === true){
            $result = $auth->simpanRegistrasi($_POST);
            if ($result === true) {
                echo '<script>alert("Registration Success! You can Login"); window.location.href = "index.php?route=login";</script>';
            } else {
                echo '<script>alert("Failed to Register!."); window.location.href = "index.php?route=registrasi";</script>';
            }
        }else{
            echo '<script>alert("Access Denied");window.location.href = "index.php?route=registrasi";</script> </script>';
        }
        break;
    case 'logout': #ketika tombol logout ditekan
        if ($auth->isLoggedIn()) {
            $auth->logout();
        }
        header("Location: index.php?route=login");
        break;
    case 'dashboard': #tampilkan halaman dashboard
        if ($auth->isLoggedIn()) {
            echo $dashboard->index();
        }
        else {
            echo "Please Login first";
        }
        break;
    case 'users':
        if ($auth->isLoggedIn()) {
            include_once "routes/user.php"; 
            }
        else {
            echo "Please Login first";
        }
        break;
    case 'findings':
        include_once "routes/finding.php";
        break;
    case 'generate_report': #export finding dengan status approved ke PDF
        $html = $findings->getAll();
        Report::generatePDFFromHTML($html);
        break;
    default:
        echo $twig->render('404.html');
        break;
}

?>
