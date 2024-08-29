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
    if (!$con) {
        die(mysqli_connect_error());
    }

    $classroom_name = $_POST['classroom_name'];
    $abbreviation = $_POST['abbreviation'];
    $query_abbreviation  = "SELECT * FROM classroom_type WHERE abbreviation = '$abbreviation'";
    $query_classroom_name = "SELECT * FROM classroom_type WHERE classroom_name = '$classroom_name'";

    $result_abbreviation = mysqli_query($con, $query_abbreviation);
    $result_classroom_name = mysqli_query($con, $query_classroom_name);

    if (mysqli_num_rows($result_abbreviation) > 0) {
        echo '<script>alert("The abbreviation \'' . $abbreviation . '\' already exists. Please choose a different abbreviation."); window.location.href = "createCourse.php";</script>';
        exit;
    } elseif (mysqli_num_rows($result_classroom_name) > 0) {
        echo '<script>alert("The classroom name \'' . $classroom_name . '\' already exists. Please choose a different name."); window.location.href = "createCourse.php";</script>';
        exit;
    } else {
        $image = $_FILES['image']['tmp_name'];
        $imageFileName = uniqid() . '_' . $_FILES['image']['name'];

        $bucketName = 'fypzh'; 
        $keyName = 'images/' . $imageFileName; 

        try {
            $awsS3Client = new AWS_S3_Client();
            $s3 = $awsS3Client->getConnection();
            $fileContent = fopen($image, 'r');

            $result = $s3->putObject([
                'Bucket' => $bucketName,
                'Key'    => $keyName,
                'Body'   => $fileContent,
                'ACL'    => 'public-read', 
            ]);

            fclose($fileContent);

            $imageURL = $result['ObjectURL'];

            $query = "INSERT INTO classroom_type (classroom_name, abbreviation, classroom_img) VALUES ('$classroom_name', '$abbreviation', '$imageURL')";
            mysqli_query($con, $query);
            echo '<script>alert("New course created successfully"); window.location.href = "createCourse.php";</script>';
            mysqli_close($con);
            exit;
        } catch (Aws\S3\Exception\S3Exception $e) {
            echo '<script>alert("Error uploading lesson: ' . $e->getMessage() . '"); window.location.href = "your-page.php";</script>';
            exit;
        }
    }
?>