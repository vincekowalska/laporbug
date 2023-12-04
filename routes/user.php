<?php
if (isset($_GET['action'])) {
    $action = $_GET['action'];

    switch ($action) {
        case 'list':
            echo $users->list();
            break;
        case 'show':
            if (isset($_GET['id'])) {
                $userId = $_GET['id'];
                echo $users->show($userId);
            } else {
                echo 'User ID tidak ditemukan!';
            }
            break;
        case 'create':
            $token =  generateCSRFtoken(); 
            echo $users->create($token);
            break;
        case 'insert':
            $token = $_POST['csrf_token'];
            $verify_token = isValidCSRFToken($token);
            // echo "<script>alert('$token') </script>";
            if($verify_token === true){
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $result = $users->insert($_POST); 
                    if ($result !== false) {
                        echo '<script>alert("User created successfully."); window.location.href = "index.php?route=users&action=list";</script>';
                    } else {
                        echo '<script>alert("Failed to create user."); window.location.href = "index.php?route=users&action=list";</script>';
                    }
                }
            }else{
                echo '<script>alert("Failed to create user."); window.location.href = "index.php?route=users&action=list";</script>';
            }
            break; 
        case 'edit':
            $token =  generateCSRFtoken(); 
            if (isset($_GET['id'])) {
                $userId = $_GET['id'];
                echo $users->edit($userId,$token);
            } else {
                echo 'User ID tidak ditemukan!';
            }
            break;
        case 'update':
            $token = $_POST['csrf_token'];
            $verify_token = isValidCSRFToken($token);
            // echo "<script>alert('$token') </script>";
            if($verify_token === true){
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $location = $_POST['location'];
                    $result = $users->update($_POST, $_POST['id']); 
                    if ($result !== false && $location === "profile") {
                        echo '<script>alert("User updated successfully."); window.location.href = "index.php?route=profile";</script>';
                    }elseif ($result !== false && $location === "update") {
                        echo '<script>alert("User updated successfully."); window.location.href = "index.php?route=users&action=list";</script>';
                    }
                    elseif ($result === false && $location === "profile") {
                        echo '<script>alert("Failed to update user."); window.location.href = "index.php?route=profile";</script>';
                    }
                    elseif($result === false && $location === "update") {
                        echo '<script>alert("Failed to update user."); window.location.href = "index.php?route=users&action=list";</script>';
                    }
                }
            }else{
                // echo "<script>alert('Error di sini')</script>";
                echo '<script>alert("Failed to update user."); window.location.href = "index.php?route=users&action=list";</script>';
            }
            break;
        case 'update-pp':
            $token = $_POST['csrf_token'];
            $verify_token = isValidCSRFToken($token);
            // echo "<script>alert('$token') </script>";
            if($verify_token === true){
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $request = $_POST;
                    $file = $_FILES["input_profil_pict"];
                    $uploadDirectory = 'pp/';
                    if($file){
                        $uploadResult = $users->upload_file($file, $uploadDirectory);
                        if($uploadResult !== false){
                                // echo "<script> alert ('masukk') </script>";
                            $request['profile_image'] = $uploadResult;
                            $db_insert = $users->db_insert_pp($request);
                            if($db_insert === true){
                                echo "<script> alert ('Successfully Insert Image'); window.location.href = 'index.php?route=profile';</script>";
                            }else{
                                echo "<script> alert ('Failed to insert Picture'); window.location.href = 'index.php?route=profile';</script>";
                            }
                        }else{
                            echo "<script> alert ('Failed to insert Picture'); window.location.href = 'index.php?route=profile';</script>";
                        }
                    }else{
                        echo "<script> alert ('Please Insert your Picture'); window.location.href = 'index.php?route=profile';</script>";
                    }
                }
            }else{
                echo "<script> alert ('Failed to Insert Picture'); window.location.href = 'index.php?route=profile';</script>";
            }
            break;
        case 'delete':
            if (isset($_GET['id'])) {
                $userId = $_GET['id'];
                $id = $_SESSION['user_id'];
                // echo "<script> alert ('$id') </script>";
                if($userId == 1 or $userId == $id){
                    echo '<script>alert("Gabisa Cokk!!!");window.location.href = "index.php?route=users&action=list"</script>';
                }
                else {
                    $result = $users->delete($userId);
                    if ($result !== false) {
                        echo '<script>alert("Succesfully deleted user."); window.location.href = "index.php?route=users&action=list";</script>';
                    } else {
                        echo '<script>alert("Failed to delete user"); window.location.href = "index.php?route=users&action=list";</script>';
                    }
                }
            } else {
                echo 'User ID tidak ditemukan!';
            }
            break;
    }
}
?>