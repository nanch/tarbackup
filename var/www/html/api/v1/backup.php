<?php

function isStringAllowed($input) {
  $allowed = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890.";
  $chars = str_split($input);
  for($i=0;$i<count($chars);$i++){
    if(strstr($allowed, $chars[$i]) == false ) {
      echo "'" . $chars[$i] . "' not allowed. input was $input\n";
      return false;
    }
  }
  return true;
}

$user = $_SERVER['PHP_AUTH_USER'];
$pw = $_SERVER['PHP_AUTH_PW'];
$saveto = $_POST['saveto'];

// sanitize the input
if (!empty($saveto)) {
 if (!isStringAllowed($saveto)){ echo "failing on saveto"; return; }
}
if (empty($user)) { echo "no user specified.\n"; return; }
if (empty($pw)) { echo "no password specified.\n"; return; }

// authorize this request
$connection = ssh2_connect('localhost', 22);
if (!(ssh2_auth_password($connection, $user, $pw))) {
 echo "user/password could not be authenticated\n"; return;
}


// compute the save path from $saveto
if (!empty($saveto)) {
 $savepath = "/mnt/r6/$user/storage/" . $saveto;
} else {
 $filename = $_FILES["file"]["name"];
 if (!isStringAllowed($filename)){ echo "failing on filename"; return; }
 $savepath = "/mnt/r6/$user/storage/" . $filename;
}

shell_exec("sudo chown apache /mnt/r6/$user/storage/"); 
move_uploaded_file($_FILES["file"]["tmp_name"], $savepath);
shell_exec("sudo chown $user:targroup /mnt/r6/$user/storage/"); 
shell_exec("sudo chown $user:targroup $savepath"); 

echo "success\n";

?>
