<?php

function isStringAllowed($input) {
  $allowed = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890";
  $chars = str_split($input);
  for($i=0;$i<count($chars);$i++){
    if(strstr($allowed, $chars[$i]) == false ) {
      echo $chars[$i] . "<br/>";
      return false;
    }
  }
  return true;
}

function getArg($argname) {
  if (empty($_POST[$argname])) {
    echo "no $argname";
    return "";
  } else {
    $arg = $_POST[$argname];
    if (!(isStringAllowed($arg))) {
      echo "illegal character in $argname";
      return "";
    }
  }
  return $_POST[$argname];
}

$name = getArg("name");
if (empty($name)) {return;}
if (strlen($name) > 32) {echo "account name too long"; return;}

if (empty($_POST["password"])) {
  echo "no password";
  return;
} else {
  $password = $_POST["password"];
}


ob_start();
exec("/usr/bin/id $name", $output);
$str = trim(implode($output));
if (strlen($str) <= 0 ) {
 ob_clean(); 
 echo 'no account with that name, please <a href="/addkey.html">try again</a>.';
 return;
}
ob_clean();

$connection = ssh2_connect('localhost', 22);
if (!(ssh2_auth_password($connection, $name, $password))) {
 echo 'Wrong password, please <a href="/addkey.html">try again</a>.';
 return;
}

if ($_FILES["publickey"]["size"] > 1000)
{
 echo 'The uploaded file is too large. It must be less than 1000 bytes. Please <a href="/addkey.html">try again</a>.';
 return;
}

if ($_FILES["publickey"]["error"] > 0)
{
 echo "There was an error. Return Code: " . $_FILES["publickey"]["error"] . '. <a href="/addkey.html">Try again</a>, or contact <a href="http://www.nanch.com">nanch</a> for support.<br />';
 return;
}

$tmp_file = basename($_FILES["publickey"]["tmp_name"]);
if (file_exists("upload/" . $tmp_file))
{
 echo $tmp_file . ' already exists. Please <a href="/addkey.html">try again</a>.';
 return;
}

move_uploaded_file($_FILES["publickey"]["tmp_name"], "uploads/" . $tmp_file);

$keydir = "/usr/local/etc/tarbackup/keys/$name";
$sshdir = $keydir . "/.ssh";

if (!(is_dir($keydir))) {
 shell_exec("sudo mkdir $keydir");
}

if (!(is_dir($sshdir))) {
 shell_exec("sudo mkdir $sshdir"); 
} 

$auth_keys_file = $sshdir . "/authorized_keys";
if (!(file_exists($auth_keys_file))) 
{
 shell_exec("sudo chown apache $sshdir"); 
 $fh = fopen($auth_keys_file, 'w') or die("can't open file");
 fclose($fh);
 shell_exec("sudo chown $name:targroup $sshdir"); 
}

shell_exec("sudo chown apache $auth_keys_file"); 
$newtext = file_get_contents("uploads/" . $tmp_file);
$out_fh = fopen($auth_keys_file, 'a') or die("can't open file");
fwrite($out_fh, $newtext);
fclose($out_fh);
shell_exec("sudo chown $name:targroup $auth_keys_file"); 

unlink("uploads/" . $tmp_file);

echo '<br/><center><b><span style="color: green;">Congratulations! Your public key has been uploaded.</span><br/>
 Read how to use the key-based authentication below!</b></center>';

include("keyauth-howto.html");

?>
