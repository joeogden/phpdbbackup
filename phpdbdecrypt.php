<?php
		// Decrypt a backed up db
		
		// Include files
		include("phpdbconfig.php");
		include('phpseclib/Crypt/Rijndael.php');
		
		header('Content-type: text/html; charset=UTF-8');

		// Set your filename, put the encrypted file in the same directory or add the path
    $filename = 'user_20161018-00_enc.sql';

		// Set the encryption key used to encrypt the file
		$key = "TheEncryptionKey";

		// This sets the suffix on the decrypted filename
		$filename2 = ''.$filename.'.decrypted.sql';
		
		// Function to decrypt the export file, uses phpseclib
		function decryptData($value){
			global $key;
			$cipher = new Crypt_Rijndael(CRYPT_RIJNDAEL_MODE_ECB);
			$cipher->setKey($key);
			return $cipher->decrypt($value);
		} 
		
		$contents = file_get_contents($filename);
				
		$DecryptedData=decryptData($contents);
		
    $handle = fopen($filename2,'w+');
    if(fwrite($handle,$DecryptedData)){ echo "Done."; }else{ echo "Failed to write file."; }

?>