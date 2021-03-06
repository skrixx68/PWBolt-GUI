<?php
session_start();
    $data = json_decode(file_get_contents('php://input'), true);
    
    //check if account exist in json, also check if guid still exist in cookie
    if( !(isset($data['id']) && isset($data['website']) && isset($data['username']) &&  isset($data['password']) && isset($_SESSION['guid']))) 
     die("Status:ERROR, Params Not Found");
    
     // check if account is empty and also check if guid is valid.
    if ((empty($data["website"]) || empty($data["website"]) || empty($data["username"]) || empty($data["password"]) || empty($_SESSION["guid"])))
    die("Status:ERROR, Params Invalid");
    
    // check if characters are valid.
    if (preg_match("/[^A-Za-z0-9\.\:\/]/", $data["website"]) || preg_match("/[^A-Za-z0-9\@\.]/", $data["username"]))
    die("Status:ERROR, Invalid Characters");

    require_once 'config/config.php';
    $website = trim($data["website"]);
    $username = trim($data["username"]);
    $guid = $_SESSION['guid'];
    $id = $data['id'];
    $userTable = "tbl_userVault".$guid;
    $Cryptor = new \Vendor\Library\Cryptor($_SESSION['pwbolt'].$salt);
    $crypted = $Cryptor->encrypt(trim($data["password"]));

    // Update row using data from POST method
    if ($stmt = $link->prepare("UPDATE {$userTable} SET website=?, username=?,password=? WHERE id = ?")) {
      $stmt->bind_param('sssi', $website,$username,$crypted,$id);
      if ($stmt->execute())
        echo "Status:OK, Account Updated.";
      else
        echo "Status:ERROR, Failed executing query";
      $stmt->close();
    }else
      echo "Status:ERROR, Failed intitiating query";
    mysqli_close($link);
?>
