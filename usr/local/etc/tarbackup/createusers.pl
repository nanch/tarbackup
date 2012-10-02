use File::Copy;

$dir = "/usr/local/etc/tarbackup/";
$logfilename = $dir . "createdusers.log";
$infilename = $dir . "userstocreate.txt";      # the queue of users that have signed up since last run
copy($infilename, $infilename . ".copy");  # make a copy to make this process as atomic as possible

#delete and recreate the existing file with the same permissions
unlink($infilename); 
open NEWFILE, ">$infilename" or die $!;
close NEWFILE;
chmod 0666, $infilename;

#read the copied file and create the users that have signed up
open INFILE, $infilename . ".copy" or die $!;
open LOGFILE, ">>$logfilename" or die $!;
while (<INFILE>) {
 print LOGFILE $_;
 @parts = split(/:/);
 $user = @parts[0];
 $password = @parts[1];
 $homedir = "/mnt/r6/$user";
 `/usr/sbin/useradd -g targroup -p '$password' -d $homedir -s /bin/false $user`;
 `chown root:root $homedir`;
 `chmod 711 $homedir`;

 `mkdir $homedir/storage`;
 `chown $user $homedir/storage`;
 `chmod 760 $homedir/storage`;

 `/usr/sbin/usermod -d /storage/ $user`;
}
close LOGFILE;
close INFILE;
unlink($infilename . ".copy"); # delete the copy 
