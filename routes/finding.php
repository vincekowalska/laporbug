<?php
if (isset($_GET['action'])) {
    $action = $_GET['action'];

    switch ($action) {
        case 'list':
            echo $findings->list();
            break;
        case 'create':
            $token = generateCSRFtoken();
            echo $findings->create($token);
            break;  
        case 'insert':
            $token = $_POST['csrf_token'];
            $verify_token = isValidCSRFToken($token);
            if($verify_token === true){
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $requests = $_POST;    
                    $uploadDirectory = 'poc/';
                    $file = $_FILES['proofOfConcept'];
        
                    $uploadResult = $findings->uploadFile($file, $uploadDirectory);
        
                    if ($uploadResult !== false) {
                        $requests['proofOfConcept'] = $uploadResult;
        
                        $result = $findings->insert($requests);
        
                        if ($result === true) {
                            echo '<script>alert("Successfully Add Finding."); window.location.href = "index.php?route=findings&action=list";</script>';
                        } 
                        else {
                            echo '<script>alert("Failed to Add Finding."); window.location.href = "index.php?route=findings&action=list";</script>';
                        }
                    } 
                    else {
                        echo '<script>alert("Failed to Upload Proof Of Concept."); window.location.href = "index.php?route=findings&action=list";</script>';
                    }
                }
            }else {
                echo "dsadssdsad";
                echo '<script>alert("Failed to Add Finding."); window.location.href = "index.php?route=findings&action=list";</script>';
            }
            break;
        case 'show':
            $token = generateCSRFtoken();
            if (isset($_GET['id'])) {
                $findingId = $_GET['id'];
                echo $findings->show($findingId,$token);
            }
            break;  
        case 'edit':
            $token = $_POST['csrf_token'];
            $verify_token = isValidCSRFToken($token);
            if($verify_token === true){
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $requests = $_POST;    
                    $uploadDirectory = 'poc/';
                    $file = $_FILES['proofOfConcept']['name'];
                    if($file){
                        $file = $_FILES['proofOfConcept'];
                        $uploadResult = $findings->uploadFile($file, $uploadDirectory);
                        if ($uploadResult !== false) {
                            $requests['proofOfConcept'] = $uploadResult;
                            $result = $findings->update($requests);
                            if ($result === true) {
                                echo '<script>alert("Successfully Update Finding."); window.location.href = "index.php?route=findings&action=list";</script>';
                            } else {
                                echo '<script>alert("Failed to Update Finding"); window.location.href = "index.php?route=findings&action=list";</script>';
                            }
                        } else {
                            echo '<script>alert("Failed to Upload Proof Of Concept."); window.location.href = "index.php?route=findings&action=list";</script>';
                        }
                    }
                    else{
                        $requests['proofOfConcept'] = "";
                        $result = $findings->update($requests);
                        if ($result === true) {
                            echo '<script>alert("Successfully Update Finding."); window.location.href = "index.php?route=findings&action=list";</script>';
                        } else {
                            echo '<script>alert("Failed to Update Finding"); window.location.href = "index.php?route=findings&action=list";</script>';
                        }
                    }
                }
            }else{
                echo '<script>alert("Failed to Update Finding"); window.location.href = "index.php?route=findings&action=list";</script>';
            }
            break;
        case 'delete':
            if (isset($_GET['id'])) {
                $findingId = $_GET['id'];
                $delete = $findings->delete($findingId);
                if ($delete === true){
                    echo "<script> alert('Successfully Deleted Finding'); window.location.href ='index.php?route=findings&action=list';</script>";
                }
                else{
                    echo "<script> alert('Failled to Deleted Finding'); window.location.href ='index.php?route=findings&action=list';</script>";
                }
            }
            break;
        case 'approve':
            if (isset($_GET['id'])) {
                $findingId = $_GET['id'];
                $approve = $findings->approveFinding($findingId);
                if ($approve === true) {
                    echo '<script>alert("Approved."); window.location.href = "index.php?route=findings&action=list";</script>';
                }
                else {
                    echo '<script>alert("Failed to Approved."); window.location.href = "index.php?route=findings&action=list";</script>';
                }
            } else {
                echo 'Cannot find the Finding!';
            }
            break;
        case 'reject':
            if (isset($_GET['id'])) {
                $findingId = $_GET['id'];
                $approve = $findings->rejectFinding($findingId);
                if ($approve === true) {
                    echo '<script>alert("Rejected."); window.location.href = "index.php?route=findings&action=list";</script>';
                }
                else {
                    echo '<script>alert("Failed to Rejected."); window.location.href = "index.php?route=findings&action=list";</script>';
                }
                
            } else {
                echo 'Cannot find the Finding';
            }
            break;                  
    }
}
?>