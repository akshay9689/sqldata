<?php

//error_reporting(E_ALL);
//ini_set('display_errors', 1);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

//use Google\Client;
//use Google\Service\Drive;




//$mail->addAttachment('/path-to-backup/db_backup.sql.gz'); // Attach file
//$mail->addAttachment('/home/earningfish-dashboard/htdocs/dashboard.earningfish.com/sqldata/backup/db_backup_' . date("Y-m-d") . '.sql.gz');





// Database credentials
$dbHost = "localhost";
$dbUser = "admindb";
$dbPass = "5lDw0HY0nMQRphsf3XrH";
$dbName = "earningfishdb";

// /home/earningfish-dashboard/htdocs/dashboard.earningfish.com/sqldata

// Backup file path
$backupDir = "/home/earningfish-dashboard/htdocs/dashboard.earningfish.com/sqldata/backup/";
$backupFile = $backupDir . "db_backup_" . date("Y-m-d") . ".sql";
//echo $backupFile."<br>";
// Ensure backup directory exists
if (!file_exists($backupDir)) {
  echo "exist"; die;
    mkdir($backupDir, 0777, true);

}


// Create MySQL backup
$command = "mysqldump -h $dbHost -u $dbUser -p'$dbPass' $dbName > $backupFile";
exec($command);



// Compress the backup file
$zipFile = $backupFile . ".gz";
exec("gzip -c $backupFile > $zipFile");
unlink($backupFile); // Delete uncompressed file

$mail = new PHPMailer(true); 

try {
    // SMTP settings
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'akshayj.appzia@gmail.com';  // Your Gmail address
    $mail->Password   = 'coctvqdztbdnvwst';          // Your App Password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    // Sender & recipient
    $mail->setFrom('akshayj.appzia@gmail.com', 'DATABASE FILE');
    $mail->addAddress('akshayjondhale632@gmail.com'); 
    $mail->addAddress('earningfish.info@gmail.com'); 

    // Backup file path
    $backupDir = "/home/earningfish-dashboard/htdocs/dashboard.earningfish.com/sqldata/backup/";
    $backupFile = $backupDir . "db_backup_" . date("Y-m-d") . ".sql";
    $zipFile = $backupFile . ".gz";

    // Check if backup file exists
    if (file_exists($zipFile)) {
        $mail->addAttachment($zipFile); // Attach the compressed backup file
    } else {
        echo "Backup file not found: $zipFile <br>";
    }

    // Email content
    $mail->Subject = 'Database Backup - ' . date("Y-m-d"). "- Automation Email ";
    $mail->Body    = 'Attached is the latest database backup.';

    // Send email
    if ($mail->send()) {
        echo "Email with backup sent successfully!";
    } else {
        echo "Email sending failed: {$mail->ErrorInfo}";
    }
} catch (Exception $e) {
    echo "Email sending failed: {$mail->ErrorInfo}";
}



// Authenticate with Google Drive API
//$client = new Client();
//$client->setAuthConfig('credentials.json'); // Your Google API credentials
//$client->addScope(Drive::DRIVE_FILE);
//$client->setAccessType('offline');

// Get Google Drive service
//$service = new Drive($client);

// Upload file to Google Drive
//$file = new Drive\DriveFile();
//$file->setName(basename($zipFile));
//$file->setParents(["your_drive_folder_id"]); // Replace with your Google Drive folder ID

//$content = file_get_contents($zipFile);
//$uploadedFile = $service->files->create($file, [
//    'data' => $content,
//    'mimeType' => 'application/gzip',
//    'uploadType' => 'multipart'
//]);

// Get Google Drive file link
//$fileId = $uploadedFile->getId();
//$driveLink = "https://drive.google.com/file/d/$fileId/view?usp=sharing";

// Send email with backup link
//$to = "your_email@example.com";
//$subject = "Database Backup Link - " . date("Y-m-d");
//$message = "Your database backup is available at: " . $driveLink;
//$headers = "From: your_email@example.com\r\nContent-Type: text/plain; charset=UTF-8";

//mail($to, $subject, $message, $headers);

// Delete old backups (older than 7 days)
exec("find $backupDir -type f -name '*.gz' -mtime +7 -delete");

//echo "Backup uploaded & email sent successfully!";





?>
