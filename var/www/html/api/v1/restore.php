<?php

function isStringAllowed($input) {
  $allowed = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890.";
  $chars = str_split($input);
  for($i=0;$i<count($chars);$i++){
    if(strstr($allowed, $chars[$i]) == false ) {
      echo "'" . $chars[$i] . "' not allowed\n";
      return false;
    }
  }
  return true;
}

$user = $_SERVER['PHP_AUTH_USER'];
$pw = $_SERVER['PHP_AUTH_PW'];
$file = $_REQUEST['file'];

// sanitize input
if (!isStringAllowed($file)){ return; }
if (empty($user)) { echo "no user specified.\n"; return; }
if (empty($pw)) { echo "no password specified.\n"; return; }
if (empty($file)) { echo "no file specified.\n"; return; }

// authorize the request
$connection = ssh2_connect('localhost', 22);
if (!(ssh2_auth_password($connection, $user, $pw))) {
 echo "user/password could not be authenticated\n"; return;
}

$readpath = "/mnt/r6/$user/storage/$file";

shell_exec("sudo chown apache:apache /mnt/r6/$user/storage/"); 
shell_exec("sudo chown apache:apache $readpath"); 
$contents = file_get_contents($readpath);
shell_exec("sudo chown $user:targroup $readpath"); 
shell_exec("sudo chown $user:targroup /mnt/r6/$user/storage/"); 

echo $contents;

?>
