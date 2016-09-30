# PHP Encrypted Database Backup Script

A pure PHP script that creates and remotely stores an encrypted .sql export file from a MySQL database. The script is designed to run as an individual user account in a restricted environment with PHP functions including exec and ssh2 disabled.

## Installation

1. Copy the script files to /home/user/phpdbbackup/ outside of the www root.
2. Edit phpdbconfig.php and set your desired encryption key, MySQL database and remote SSH/SFTP login credentials.
3. Set up a cron job for 00 05 * * * php -q /home/user/phpdbbackup/phpdbbackup.php to run the database backup at 05:00am daily.

## Usage

Upon triggering with cron, the script will export the selected database tables to a .sql file, encrypt the file using mcrypt, and connect securely to a remote server to store the encrypted backup file.

Limited error handling. Not tested with huge databases. Use at your own risk.

Decrypt the exported file using phpdbdecrypt.php and setting the encryption key used to encrypt the file originally. The decrypted file can be imported via phpMyAdmin.

## Requirements

PHP 5.4 minimum, with mcrypt and mysqli.

## Tips

- Create a read only database user with SELECT and SHOW permissions to use with the export script.
- You could exclude /phpseclib/ and modify the script to use PHP ssh2 or ftp functions.
- Suited to be run in shared cPanel hosting environments but should run almost anywhere.
- Script can be run hourly or more often for minute by minute database backups.
- Garbage collection needs to be performed on the remote server, the script doesn't handle this automatically.

## Credits

Joe Ogden
https://www.joeogden.com

PHP Secure Communications Library (phpseclib): https://github.com/phpseclib

MySQL export script based on: https://davidwalsh.name/backup-mysql-database-php

## Licence

Open Source MIT Licence