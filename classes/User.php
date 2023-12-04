<?php
class User
{
    private $twig, $pdo;
    
    public function __construct($twig, $db)
    {
        $this->twig = $twig;
        $this->pdo = $db->getConnection();
    }

    public function list()
    {
        $sql = "SELECT * FROM users";

        try {
            $stmt = $this->pdo->query($sql);
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $this->twig->render('users/list.html', ['users' => $users]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function show($id)
    {
        $sql = "SELECT * FROM users WHERE id = ?";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                return $this->twig->render('users/show.html', ['user' => $user]);
            } else {
                return 'User tidak ditemukan.';
            }
        } catch (PDOException $e) {
            return 'Database error.';
        }
    }

    public function create($token)
    {
        return $this->twig->render('users/create.html',['csrf_token' => $token]);
    }
    
    public function insert($requests)
    {
        $name = $requests['name'];
        $email = $requests['email'];
        $password = $requests['password'];
        $role = $requests['role'];
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
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

    public function edit($id,$token)
    {
        $sql = "SELECT * FROM users WHERE id = ?";
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($user) {
                return $this->twig->render('users/edit.html', ['user' => $user,'csrf_token' => $token]);
            } else {
                return 'User tidak ditemukan.';
            }
        } catch (PDOException $e) {
            return 'Database error.';
        }
    }

    public function upload_file($file, $uploadDirectory){
        if ($file['error'] === UPLOAD_ERR_OK) {
            $proofOfConceptFileName = $file['name'];
            $proofOfConceptTmpName = $file['tmp_name'];
            
            $uniqueFileName = uniqid() . '_' . $proofOfConceptFileName;
            if (move_uploaded_file($proofOfConceptTmpName, $uploadDirectory . $uniqueFileName)) {
                return $uniqueFileName;
            } else {
                return false; 
            }
        } else {
            return false; 
        }
    }

    public function db_insert_pp($requests){
        $id = $requests['id'];
        $profile_picture = $requests['profile_image'];

        // SQL statement
        $sql = "UPDATE users set profile_picture = ? where id = ?";
        try{
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$profile_picture, $id]);
            if ($stmt->rowCount() > 0) {
                return true;
            } else {
                return false;
            }
        }catch(PDOException $e){
            return false;
        }
    }

    public function update($requests)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            print_r($_POST);
            $id = $requests['id'];
            $name = $requests['name'];
            $email = $requests['email'];
            $role = $requests['role'];
            $phone = $requests['phone_number'];
            $location = $requests['location'];
            if($location === 'update'){
                $password = $requests['password'];
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $sql = "UPDATE users SET name = ?, email = ?, role = ?, phone_number = ?, password = ? WHERE id = ?";
            }else{
                $sql = "UPDATE users SET name = ?, email = ?, role = ?, phone_number = ? WHERE id = ?";
            }
            
            try {
                $stmt = $this->pdo->prepare($sql);
                if($location === 'update'){
                    $stmt->execute([$name, $email, $role, $phone, $hashedPassword, $id]);
                }else{
                    $stmt->execute([$name, $email, $role, $phone, $id]);
                }
                
                if ($stmt->rowCount() > 0) {
                    return true;
                } else {
                    return false;
                }
            }catch (PDOException $e) {
                echo $e;
                return false;
            }
        }
        return false;
    }

    public function delete($id)
    {
        $sql = "DELETE FROM users WHERE id = ?";
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id]);
            if ($stmt->rowCount() > 0) {
                return true; 
            } else {
                return false; 
            }
        } catch (PDOException $e) {
            echo "Database error";
            return false;
        }
    }
}
?>
