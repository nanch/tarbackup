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
if (empty($name)) {return;}
if (strlen($name) > 32) {echo "account name too long"; return;}

if (empty($_POST["password"])) {
  echo "no password";
  return;
} else {
  $password = $_POST["password"];
}

$file = $_REQUEST['file'];

// sanitize the input
if (empty($name)) { echo "no user specified.\n"; return; }
if (empty($password)) { echo "no password specified.\n"; return; }
if (empty($file)) { echo "no file specified.\n"; return; }

$connection = ssh2_connect('localhost', 22);
if (!(ssh2_auth_password($connection, $name, $password))) {
 echo 'Wrong password, please <a href="/backup.html">try again</a>.';
 return;
}

$readpath = "/mnt/r6/$name/storage/$file";

shell_exec("sudo chown apache:apache /mnt/r6/$name/storage/"); 
shell_exec("sudo chown apache:apache $readpath"); 
$contents = file_get_contents($readpath);
shell_exec("sudo chown $name:targroup $readpath"); 
shell_exec("sudo chown $name:targroup /mnt/r6/$name/storage/"); 

header('Content-Length: ' .  strlen($contents) . "");
header('Content-Type: ' . mime_content_type($file));
header('Content-Disposition: attachment;filename=' . $file . '');

echo $contents;

?>
