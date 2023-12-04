<?php
class Profile {
    private $twig, $pdo, $auth;
    
    public function __construct($twig, $db, $auth)
    {
        $this->twig = $twig;
        $this->pdo = $db->getConnection();
        $this->auth = $auth;
    }

    public function index($token)
    {
        $user_info = $this->auth->getUserInfo();
        return $this->twig->render('users/profile.html', ['user_info' => $user_info, 'token' => $token]);
    }
}
?>