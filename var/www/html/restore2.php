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
if (empty($name)) { echo "no name specified. <a href=\"/restore.html\">login here</a>"; return;}
if (strlen($name) > 32) {echo "account name too long"; return;}

if (empty($_POST["password"])) {
  echo "no password <a href=\"/restore.html\">login here</a>";
  return;
} else {
  $password = $_POST["password"];
}

// sanitize the input
if (empty($name)) { echo "no user specified.\n"; return; }
if (empty($password)) { echo "no password specified.\n"; return; }

$connection = ssh2_connect('localhost', 22);
if (!(ssh2_auth_password($connection, $name, $password))) {
 echo 'Wrong password, please <a href="/backup.html">try again</a>.';
 return;
}

$files = array();

shell_exec("sudo chown apache /mnt/r6/$name/storage/");

if ($handle = opendir("/mnt/r6/$name/storage/")) {
    while (false !== ($entry = readdir($handle))) {
	if ($entry != "." && $entry != "..") {
            array_push($files, $entry);
        }
    }
    closedir($handle);
} else {
   echo "there was a problem reading the files"; return;
}

shell_exec("sudo chown $name:targroup /mnt/r6/$user/storage/");

?>



<html>
   <head>
      <title>tarbackup - restore a tar</title>
      <meta name="description" content="backup a tar">
	<meta name="viewport" content="width=500px, height=600px, user-scalable=yes" />
      <style>
         input { width:220px; }
         * { font-family: "PT Serif", "Open Sans", "Lucida Sans Unicode", "Lucida Grande", sans-serif; color: #222;}
         /*body { background-clip: border-box; background-color: transparent; background-image: url(http://tarbackup.com/bg-texture-01.jpg);*/
         }
      </style>
      <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,400italic,600,600italic" rel="stylesheet" type="text/css" />
      <link href="https://fonts.googleapis.com/css?family=PT+Serif" rel="stylesheet" type="text/css" />      


   </head>
   <body style="margin: 0px; padding: 0px; padding-top: 20px;">
      <div style="margin: auto; padding: 0px; text-align: center;">
         <h1><a href="/" style="text-decoration:none;"><span style="color: green;">tar</span>backup</a></h1>
        <h2>online tar backup provided by <a href="http://www.nanch.com" style="color: blue; text-decoration:none;">nanch</a></h2>
      </div>
      <form action="backup.php" method="POST" enctype="multipart/form-data" style="border:1px solid gray; width: 400px; margin: auto; padding: 10px; padding-left: 30px;">
         <h2 style="margin: 0px; margin-bottom: 10px">restore a tar file</h2>

	<ul>

<?php
foreach ($files as $file) {

	echo "<li>";
	echo '<form action="/restore3.php" method="POST" enctype="multipart/form-data" >';
	echo '<input type="hidden" name="name" value="' . $name . '">';
	echo '<input type="hidden" name="password" value="' . $password . '">';
	echo '<input type="hidden" name="file" value="' . $file . '">';
	echo '<input type="submit" value="' . $file . '">';
	echo '</form>';
	echo "</li>";
}
?>
	</ul>

<!--         <table>
	    <tr>
               <td>account name: </td>
               <td><input type="text" name="name" /></td>
            </tr>
            <tr>
               <td>password:</td>
               <td><input type="password" name="password" /></td>
            </tr>
            <tr>
               <td>tar file: </td>
               <td><input type="file" name="file" id="file" /></td>
            </tr>
            <tr>
               <td colspan="2"><input type="submit" value="backup a tar" style="padding: 4px; font-size: 18px;" /></td>
            </tr>
         </table>
-->
      </form>
      <br />
      <div style="margin: auto; padding: 0px; text-align: center;">
         <h1 style="display:inline;"><a href="/keyauth-howto.html" style="color: blue; text-decoration:none;">
            <!--how to use key-based auth -->
            </a> 
         </h1>
      </div>
   </body>
</html>



