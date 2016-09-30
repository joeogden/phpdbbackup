<?php

// MySQL details

				$db_host = 'localhost'; // Your database server, e.g. localhost
				$db_user = 'user'; // Your database username
				$db_pass = 'pass'; // Your database password
				$db_name = 'database'; // Your database name

				$username = 'user'; // Your hosting account username, used in the file paths

// Remove server details

				$server = 'backup.server.com'; // Your remote server
				$user_name = 'user'; // Your SSH username
				$user_pass = 'pass'; // Your SSH password
				$remote_port = 22; // e.g. 22
				
// Other configuration

				$key = "TheEncryptionKey"; // Your 16, 24 or 32 bit encryption key, you need to change this and you will need it to decrypt the file
				
		    $backup_path = '/home/'.$username.'/phpdbbackup/'; // The backup script directory, make sure it ends with "/"
		    
				date_default_timezone_set('Asia/Phnom_Penh'); // Your timezone, for the filename timestamps

				error_reporting(0); // Turn errors off

				set_include_path('/home/'.$username.'/phpdbbackup/phpseclib'); // Include path for phpseclib