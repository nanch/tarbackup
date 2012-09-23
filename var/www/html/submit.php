<?php

function rand_string( $length ) {
  $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";	
	$str = substr( str_shuffle( $chars ), 0, $length );
	return $str;
}

function crypt512($pw) {
  if (CRYPT_SHA512 != 1) {
    throw new Exception('Hashing mechanism not supported.');
  }
  return crypt($pw, '$6$' . rand_string(16) . '$');
}

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

if (empty($_POST["password"])) {
  echo "no password";
  return;
} else {
  $password = $_POST["password"];
}

if (empty($_POST["passwordverify"])) {
  echo "no passwordverify";
  return;
} else {
  $passwordverify = $_POST["passwordverify"];
}



ob_start();
exec("/usr/bin/id $name", $output);
$str = trim(implode($output));
if (strlen($str) > 0 ) { ob_clean(); echo 'account name already taken, please <a href="/">try again</a> with a different acccount name'; return;}
ob_clean();
if (strcmp($password, $passwordverify) != 0) {echo "password and passwordverify do not match, please try again."; return; }


if (empty($_POST["email"])) {
  echo "no email";
  return;
} else {
  $email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL);
}

$myFile = "/usr/local/etc/tarbackup/userstocreate.txt";
$fh = fopen($myFile, 'a') or die("can't open file");
$stringData = $name . ":" . crypt512($password) . ":" . $email . "\n";
fwrite($fh, $stringData);
fclose($fh);

echo "<br/><center><b><span style=\"color: green;\">Congratulations! Your account named '$name' has been registered.</span><br/>
 it may take up to 5 minutes for your account to be created. <a href=\"/howto.html\">Read how to use tarbackup</a> in the meantime!</b></center>";

include("howto.html");

?>


