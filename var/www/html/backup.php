<?php

function isStringAllowed($input) {
  $allowed = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890.-";
  $chars = str_split($input);
  for($i=0;$i<count($chars);$i++){
    if(strstr($allowed, $chars[$i]) == false ) {
      echo "'" . $chars[$i] . "' not allowed. input was $input\n";
      return false;
    }
  }
  return true;
}


$name = $_POST['name'];
$password = $_POST["password"];

// sanitize the input
if (empty($name)) { echo "no user specified.\n"; return; }
if (strlen($name) > 32) {echo "account name too long"; return;}
if (empty($password)) { echo "no password specified.\n"; return; }

$connection = ssh2_connect('localhost', 22);
if (!(ssh2_auth_password($connection, $name, $password))) {
 echo 'Unable to authenticate, please <a href="/backup.html">try again</a>.';
 return;
}

// compute the save path from $saveto
$filename = $_FILES["file"]["name"];
if (!isStringAllowed($filename)){ return; }
$savepath = "/mnt/r6/$name/storage/" . $filename;

shell_exec("sudo chown apache /mnt/r6/$name/storage/");
move_uploaded_file($_FILES["file"]["tmp_name"], $savepath);
shell_exec("sudo chown $name:targroup /mnt/r6/$user/storage/");
shell_exec("sudo chown $name:targroup $savepath");

echo "success\n";

?>
