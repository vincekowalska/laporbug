<?php
class Finding
{    
    private $twig, $pdo, $auth;
    
    public function __construct($twig, $db, $auth)
    {
        $this->twig = $twig;
        $this->pdo = $db->getConnection();
        $this->auth = $auth;
    }

    public function list()
    {
        try {
            if ($this->auth->isAdmin() === true) {
                $sql = "SELECT * FROM users u JOIN findings f ON f.user_id = u.id ORDER BY f.id DESC";
                $stmt = $this->pdo->prepare($sql);
            } else {
                $user_id = $this->auth->getUserId();
                $sql = "SELECT * FROM users u JOIN findings f ON f.user_id = u.id WHERE f.user_id = :user_id";
                $stmt = $this->pdo->prepare($sql);
                $stmt->bindParam(':user_id', $user_id);
            }
            $stmt->execute();
            $findings = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $this->twig->render('findings/list.html', ['findings' => $findings]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function show($id,$token)
    {
        $sql = "SELECT * FROM findings WHERE id = ?";
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id]);
            $finding = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($finding) {
                // echo "<script>alert('$id')</script>";
                return $this->twig->render('findings/show.html', ['finding' => $finding,'csrf_token' => $token]);
            } else {
                return 'Finding tidak ditemukan.';
            }
        } catch (PDOException $e) {
            return 'Database error.';
        }
    }

    public function create($token)
    {
        return $this->twig->render('findings/create.html', ['csrf_token' => $token]);
    }

    public function insert($requests)
    {   
        date_default_timezone_set('Asia/Jakarta');
        $title = isset($requests['title']) ? $requests['title'] : null;
        $description = isset($requests['description']) ? $requests['description'] : null;
        $asset_name = isset($requests['asset_name']) ? $requests['asset_name'] : null;
        $severity = isset($requests['severity']) ? $requests['severity'] : null;
        $proofOfConcept = isset($requests['proofOfConcept']) ? $requests['proofOfConcept'] : null;
        $poc_video_url = isset($requests['poc_video_url']) ? $requests['poc_video_url'] : null;        
        $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
        $currentDateTime = date('Y-m-d H:i:s');
        // echo "<script> alert ('$currentDateTime') </script>";
        $status = 'submitted'; #approved dan rejected
        if (!$title or !$description or !$asset_name or !$severity or !$proofOfConcept or !$status) {
            echo "there is a field that I forgot to fill in.";
            exit;
        }
        $sql = "INSERT INTO findings (title, severity, asset_name, user_id, description, proofOfConcept, poc_video_url, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$title, $severity, $asset_name, $user_id, $description, $proofOfConcept, $poc_video_url, $status]);
            if ($stmt->rowCount() > 0) {
                
                $msg = urlencode("There is a new finding, Please check it. 
                \nTitle = $title \nAsset = $asset_name \nSeverity = $severity \nDate = $currentDateTime");
                file_get_contents("https://api.telegram.org/bot6678255948:AAEg3tNnBv1Z1utqdbF741YsTKfcXxtvFEI/sendMessage?chat_id=-4081465850&text=".$msg);
                return true;
            } else {
                throw new Exception("Failed Insert finding");
            }
        } catch (PDOException $e) {
            throw new Exception("PDO Error: " . $e->getMessage());
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function update($requests)
    {   
        $id = $requests['proofOfConcept'];
        $title = isset($requests['title']) ? $requests['title'] : null;
        $description = isset($requests['description']) ? $requests['description'] : null;
        $asset_name = isset($requests['asset_name']) ? $requests['asset_name'] : null;
        $severity = isset($requests['severity']) ? $requests['severity'] : null;
        $proofOfConcept = isset($requests['proofOfConcept']) ? $requests['proofOfConcept'] : null;
        $poc_video_url = isset($requests['input_poc_video_url']) ? $requests['input_poc_video_url'] : null;
        if($proofOfConcept){
            $sql = "UPDATE findings SET title = ?, severity = ?, description = ?, asset_name = ?, proofofconcept = ?, poc_video_url = ? ";
        }else{
            $sql = "UPDATE findings SET title = ?, severity = ?, description = ?, asset_name = ?, poc_video_url = ? ";
        }
        try {
            $stmt = $this->pdo->prepare($sql);
            if($proofOfConcept){
                $stmt->execute([$title, $severity, $description, $asset_name,  $proofOfConcept, $poc_video_url]);
            }else{
                $stmt->execute([$title, $severity, $description, $asset_name, $poc_video_url]);
            }
            if ($stmt->rowCount() > 0) {
                return true;
            } else {
                throw new Exception("Insert finding gagal disimpan!");
            }
        } catch (PDOException $e) {
            throw new Exception("PDO Error: " . $e->getMessage());
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function delete($id)
    {
        $sql = "DELETE FROM findings WHERE id = ?";
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

    public function uploadFile($file, $uploadDirectory)
    {
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

    public function generateReport()
    {
        $sql = "SELECT * FROM findings WHERE status='closed'";
        try {
            $stmt = $this->pdo->query($sql);
            $findings = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $this->twig->render('findings/report.html', ['findings' => $findings]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function approveFinding($id) {
        $sql = "UPDATE findings SET status = ?  WHERE id = ?";
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['approved', $id]);
            if ($stmt->rowCount() > 0) {
                return true;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            return false;
        }
    }

    public function rejectFinding($id) {
        $sql = "UPDATE findings SET status = ?  WHERE id = ?";
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['rejected', $id]);

            if ($stmt->rowCount() > 0) {
                return true;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            return false;
        }
    }

    public function getAll()
    {
        $sql = "SELECT * FROM findings f LEFT JOIN users u ON f.user_id = u.id WHERE f.status='approved'";
        try {
            $stmt = $this->pdo->query($sql);
            $findings = $stmt->fetchAll(PDO::FETCH_ASSOC);
            date_default_timezone_set('Asia/Jakarta');
            $current_date = date('d/m/Y H:i:s');

            return $this->twig->render('findings/report.html', ['findings' => $findings, 'current_date'=>$current_date]);
        } catch (PDOException $e) {
            return false;
        }
    }

}
?>