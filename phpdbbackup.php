<?php
		// Back up and encrypt a MySQL database
		// Run this file via cron

		// Include files
		include("phpdbconfig.php");
		include('phpseclib/Net/SFTP.php');
	
		// Function to encrypt the export
		function encryptData($value){
			global $key;
			$text = $value;
			$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB);
			$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
			$crypttext = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $text, MCRYPT_MODE_ECB, $iv);
			return $crypttext;
		}

    // Set the date for filename, this is designed for hourly backups but could also include minutes
   	$date = date("Ymd-H");

		// Check for existing file and continue
    if (!file_exists($backup_path.''.$username.'_'.$date.'_enc'.'.sql')) {

        // Connect to db with mysqli
        $link = mysqli_connect($db_host,$db_user,$db_pass);
        mysqli_set_charset($link,'utf8');
        mysqli_select_db($link,$db_name);

        // Get all of the tables
        $tables = array();
        $result = mysqli_query($link, 'SHOW TABLES');
        while($row = mysqli_fetch_row($result))
        {
            $tables[] = $row[0];
        }

        // Disable foreign keys (to avoid errors)
        $return = 'SET FOREIGN_KEY_CHECKS=0;' . "\r\n";
        $return.= 'SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";' . "\r\n";
        $return.= 'SET AUTOCOMMIT=0;' . "\r\n";
        $return.= 'START TRANSACTION;' . "\r\n";

        // Cycle through
        foreach($tables as $table)
        {
            $result = mysqli_query($link, 'SELECT * FROM '.$table);
            $num_fields = mysqli_num_fields($result);
            $num_rows = mysqli_num_rows($result);
            $i_row = 0;

            //$return.= 'DROP TABLE '.$table.';'; 
            $row2 = mysqli_fetch_row(mysqli_query($link,'SHOW CREATE TABLE '.$table));
            $return.= "\n\n".$row2[1].";\n\n"; 

            if ($num_rows !== 0) {
                $row3 = mysqli_fetch_fields($result);
                $return.= 'INSERT INTO '.$table.'( ';
                foreach ($row3 as $th) 
                { 
                    $return.= '`'.$th->name.'`, '; 
                }
                $return = substr($return, 0, -2);
                $return.= ' ) VALUES';

                for ($i = 0; $i < $num_fields; $i++) 
                {
                    while($row = mysqli_fetch_row($result))
                    {
                        $return.="\n(";
                        for($j=0; $j<$num_fields; $j++) 
                        {
                            $row[$j] = addslashes($row[$j]);
                            $row[$j] = preg_replace("#\n#","\\n",$row[$j]);
                            if (isset($row[$j])) { $return.= '"'.$row[$j].'"' ; } else { $return.= '""'; }
                            if ($j<($num_fields-1)) { $return.= ','; }
                        }
                        if (++$i_row == $num_rows) {
                            $return.= ");"; // last row
                        } else {
                            $return.= "),"; // not last row
                        }   
                    }
                }
            }
            $return.="\n\n\n";
        }

        // Enable foreign keys
        $return .= 'SET FOREIGN_KEY_CHECKS=1;' . "\r\n";
        $return.= 'COMMIT;';

        // Make backup folder if it doesn't exist
        if (!is_dir($backup_path)) {
            mkdir($backup_path, 0755, true);
        }
        
        // Set file paths & names
				$file = ''.$backup_path.''.$username.'_'.$date.'_enc'.'.sql';
				$remote_file = '/home/'.$user_name.'/phpdbbackups/'.$username.'/'.$username.'_'.$date.'_enc'.'.sql';
				$remote_dir = '/home/'.$user_name.'/phpdbbackups/'.$username.'/';
				
        // Encrypt the data
				$EncryptedData = encryptData($return);
        $handle = fopen($backup_path.''.$username.'_'.$date.'_enc'.'.sql','w+');
        fwrite($handle,$EncryptedData);
        
				// Open SFTP connection and send the file
			  $sftp = new Net_SFTP($server,$remote_port);
			  $sftp->login($user_name, $user_pass) or die("Login failed");
	 
	 			$sftp->exec('mkdir /home/'.$user_name.'/phpdbbackups/');
	 			$sftp->exec('mkdir '.$remote_dir.'');
		    $sftp->pwd() . "\r\n";
		    $sftp->put($remote_file, $file, NET_SFTP_LOCAL_FILE);
		    
     		// Delete the local file
     		unlink($file);
    }
    
    
?>