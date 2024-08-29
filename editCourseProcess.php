<?php
    session_start();

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

    $current_abbreviation = $_SESSION['current_abbreviation'];

    $abbreviation = $_POST['abbreviation'];
    $classroom_name = $_POST['classroom_name'];

    $query_abbreviation = "SELECT * FROM classroom_type WHERE abbreviation = '$abbreviation'";
    $query_classroom_name = "SELECT * FROM classroom_type WHERE classroom_name = '$classroom_name'";

    $result_abbreviation = mysqli_query($con, $query_abbreviation);
    $result_classroom_name = mysqli_query($con, $query_classroom_name);

    if (mysqli_num_rows($result_abbreviation) > 0) {
        echo '<script>alert("The abbreviation \'' . $abbreviation . '\' already exists. Please choose a different abbreviation."); window.location.href = "editCourse_input.php?abbreviation='.$current_abbreviation.'";</script>';
        exit;
    }else if (mysqli_num_rows($result_abbreviation) > 0){
        echo '<script>alert("The classroom name \'' . $classroom_name . '\' already exists. Please choose a different name."); window.location.href = "editCourse_input.php?abbreviation='.$current_abbreviation.'";</script>';
        exit;
    }else{
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
            $query = "UPDATE classroom_type SET abbreviation = '$abbreviation', classroom_name = '$classroom_name' WHERE abbreviation = '$current_abbreviation'";

            if (mysqli_query($con, $query)) {
                echo '<script type="text/javascript">
                alert("Course updated successfully.");
                window.location.href = "editCourse.php?";
                </script>';
            } else {
                echo "Error updating course: " . mysqli_error($con);
            }
        } catch (Aws\S3\Exception\S3Exception $e) {
            echo '<script>alert("Error uploading lesson: ' . $e->getMessage() . '"); window.location.href = "your-page.php";</script>';
            exit;
        }
    }

    mysqli_close($con);
?>