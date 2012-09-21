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

$password = getArg("password");
if (empty($password)) {return;}

$passwordverify = getArg("passwordverify");
if (empty($passwordverify)) {return;}

if (strcmp($password, $passwordverify) != 0) {echo "password and passwordverify do not match, please try again."; return; }

if (empty($_POST["email"])) {
  echo "no email";
  return;
} else {
  $email = $_POST["email"];
}

$myFile = "/usr/local/etc/tarbackup/userstocreate.txt";
$fh = fopen($myFile, 'a') or die("can't open file");
$stringData = $name . ":" . $password . ":" . $email . "\n";
fwrite($fh, $stringData);
fclose($fh);

echo "user '$name' created.";

?>