<?php
		// Decrypt a backed up db
		
		header('Content-type: text/html; charset=UTF-8');

		// Set your filename, put the encrypted file in the same directory or add the path
    $filename = 'user_20160930_05_enc.sql';

		// Set the encryption key used to encrypt the file
		$key = "TheEncryptionKey";

		// This sets the suffix on the decrypted filename
		$filename2 = ''.$filename.'.decrypted.sql';
		
		
		// Function to decrypt the export file
		function decryptData($value){
			global $key;
			$crypttext = $value;
			$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB);
			$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
			$decrypttext = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $crypttext, MCRYPT_MODE_ECB, $iv);
			return trim($decrypttext);
		} 

		$contents = file_get_contents($filename);
				
		$DecryptedData=decryptData($contents);
		
    $handle = fopen($filename2,'w+');
    if(fwrite($handle,$DecryptedData)){ echo "Done."; }else{ echo "Failed to write file."; }

?>