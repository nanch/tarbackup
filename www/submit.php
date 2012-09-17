<?php


// write the username and password to the file
if (empty($_POST["name"])) {
	echo "no name";
	return;
}

if (empty($_POST["password"])) {
	echo "no name";
	return;
}

if (empty($_POST["email"])) {
	echo "no email";
	return;
}

$myFile = "/usr/local/etc/tarbackup/userstocreate.txt";
$fh = fopen($myFile, 'a') or die("can't open file");
$stringData = $_POST["name"] . ":" . $_POST["password"] . ":" . $_POST["email"] . "\n";
fwrite($fh, $stringData);
fclose($fh);

echo "user created";

?>