<?php

    include("aws_credential.php");
    include("connection.php");
    require 'vendor/autoload.php'; 

    use Aws\S3\S3Client;
    use Aws\Exception\AwsException;

    class AWS_S3_Client {
        public function getConnection()
        {
            $accessKeyId = AWS_ACCESS_KEY_ID;
            $secretAccessKey = AWS_SECRET_ACCESS_KEY;
            $sessionToken = AWS_SESSION_TOKEN;

            $s3Client = new S3Client([
                'version' => 'latest',
                'region'  => 'us-east-1',
                'credentials' => [
                    'key'    => $accessKeyId,
                    'secret' => $secretAccessKey,
                    'token'  => $sessionToken,
                ],
                'http'    => [
                    'verify' => 'C:\Program Files\php-8.3.4-nts-Win32-vs16-x64\cacert.pem'
                ]
            ]);

            return $s3Client;
        }
        
    }
    session_start();
    
    include("connection.php");

    $id = $_SESSION['id'];

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        
        $practiceTitle_edit = $_POST['practiceTitle_edit'];
        $practiceDescription = $_POST['practiceDescription_edit'];
        $practiceIdEdit = $_POST['practiceIdEdit'];
        $practiceFileName = $_FILES['practiceFile']['name'];
        $practiceFileTmpName = $_FILES['practiceFile']['tmp_name'];

        $studentClassroomIds = isset($_POST['studentClassroomIds']) ? explode(',', $_POST['studentClassroomIds']) : [];

        $uniqueFileName = uniqid() . '_' . $practiceFileName;

        $bucketName = 'fypzh'; 
        $keyName = 'practice_files/' . $uniqueFileName; 

        try {
            $awsS3Client = new AWS_S3_Client();
            $s3 = $awsS3Client->getConnection();

            $result = $s3->putObject([
                'Bucket' => $bucketName,
                'Key'    => $keyName,
                'Body'   => fopen($practiceFileTmpName, 'rb'),
                'ACL'    => 'public-read', 
            ]);

            $practiceFileUrl = $result['ObjectURL'];

            $id = $_SESSION['id'];
            $practiceTitle = mysqli_real_escape_string($con, $_POST['practiceTitle_edit']);
            $practiceDescription = mysqli_real_escape_string($con, $_POST['practiceDescription_edit']);

            $query = "UPDATE practice_data SET practice_title = '$practiceTitle', practice_description = '$practiceDescription', practice_file = '$practiceFileUrl', upload_time = NOW() WHERE teacher_id = '$id' AND practice_id = '$practiceIdEdit'";
            mysqli_query($con, $query); 

            mysqli_close($con);
            echo '<script>window.history.back();</script>';
            exit;
        } catch (Aws\S3\Exception\S3Exception $e) {
            echo '<script>alert("Error uploading practice: ' . $e->getMessage() . '"); window.location.href = "your-page.php";</script>';
            exit;
        }
    }
?>