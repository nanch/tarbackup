use File::Copy;

$filename = "userstocreate.txt";      # the queue of users that have signed up since last run
copy($filename, $filename . ".copy");  # make a copy to make this process as atomic as possible

#delete and recreate the existing file with the same permissions
unlink($filename); 
open NEWFILE, ">$infilename" or die $!;
close NEWFILE;
chmod 0766, $filename;

#read the copied file and create the users that have signed up
open INFILE, $filename . ".copy" or die $!;
open LOGFILE, ">>createdusers.log" or die $!;
while (<INFILE>) {
 print $_;
 print LOGFILE $_;
 @parts = split(/:/);
 $user = @parts[0];
 $password = @parts[1];
 `echo $password | pw  user add -n $user -g targroup -s /bin/sh -h 0`; #create the user on the system
}
close LOGFILE;
close INFILE;
unlink($filename . ".copy"); # delete the copy 