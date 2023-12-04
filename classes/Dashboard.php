<?php
class Dashboard {
    private $twig, $pdo, $auth;
    
    public function __construct($twig, $db, $auth)
    {
        $this->twig = $twig;
        $this->pdo = $db->getConnection();
        $this->auth = $auth;
    }

    public function index()
    {
        $user_count = $this->getCountUsers();
        $finding_count = $this->getCountFindings();
        $findings = $this->getFindings();
        $approved_findings = $this->getApprovedFindings();
        $rejected_findings = $this->getRejectedFindings();
        $submitted_findings = $this->getSubmittedFindings();
        // Presentation //
        // Submitted //
        if($submitted_findings){
            $submitted = ($submitted_findings / $finding_count) * 100;
        }else{
            $submitted = 0;
        }        
        // Approved finding //
        if($approved_findings){
            $approved = ($approved_findings / $finding_count) * 100;
        }else{
            $approved = 0;
        }        
        // Rejected finding //
        if($rejected_findings){
            $rejected = ($rejected_findings / $finding_count) * 100;
        }else{
            $rejected = 0;
        }               
        // $user_info = $this->getUsers();
        return $this->twig->render('dashboard.html', ['allUser' => $user_count, 'allFinding' => $finding_count, 'findings' => $findings, 'approved_findings' => $approved, 
                'rejected_findings' => $rejected, 'submitted_findings' => $submitted]);
    }

    public function getCountUsers()
    {
        // Get all users 
        $sql = "SELECT * FROM users";
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            $total_users = $stmt->rowCount();
            if ($total_users) {
                return $total_users;
            } else {
                return 'User Not Found.';
            }
        } catch (PDOException $e) {
            return 'Database error.';
        }
    }

    public function getCountFindings()
    {
        // Get all users 
        $sql = "SELECT * FROM findings";
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            $total_findings = $stmt->rowCount();
            if ($total_findings) {
                return $total_findings;
            } else {
                return 0;
            }
        } catch (PDOException $e) {
            return 'Database error.';
        }
    }
    public function getFindings()
    {
        // Get all findings 
        $sql = "SELECT * FROM users u JOIN findings f ON f.user_id = u.id ORDER BY f.id DESC";
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            $findings = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if ($findings) {
                return $findings;
            } else {
                return 0;
            }
        } catch (PDOException $e) {
            return 'Database error.';
        }
    }

    public function getApprovedFindings()
    {
        $sql = "SELECT * FROM findings where status = 'approved'"; 
        try{
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            $approved = $stmt->rowCount();
            if($approved){
                return $approved;
            }
            else{
                return 0;
            }
        }catch(PDOException $e){
            return "Database Error";
        }
    }

    public function getRejectedFindings()
    {
        $sql = "SELECT * FROM findings where status = 'rejected'"; 
        try{
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            $rejected = $stmt->rowCount();
            if($rejected){
                return $rejected;
            }
            else{
                return 0;
            }
        }catch(PDOException $e){
            return "Database Error";
        }
    }
    public function getSubmittedFindings()
    {
        $sql = "SELECT * FROM findings where status = 'submitted'"; 
        try{
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            $submitted = $stmt->rowCount();
            if($submitted){
                return $submitted;
            }
            else{
                return 0;
            }
        }catch(PDOException $e){
            return "Database Error";
        }
    }


}
?>