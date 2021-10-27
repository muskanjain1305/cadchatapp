<?php
    session_start();
    require '../vendor/autoload.php';
    include_once "config.php";
    use Aws\S3\S3Client;
    $fname = mysqli_real_escape_string($conn, $_POST['fname']);
    $lname = mysqli_real_escape_string($conn, $_POST['lname']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    
    if(!empty($fname) && !empty($lname) && !empty($email) && !empty($password)){
        // check whether email exists or not
        if(filter_var($email, FILTER_VALIDATE_EMAIL)){
             $sql = mysqli_query($conn, "SELECT * FROM users WHERE email = '{$email}'");
             if(mysqli_num_rows($sql) > 0){
                 echo "$email - This email already exist!";
             }else{
                 // check whether the file is uploaded or not
                 if(isset($_FILES['image'])){
                     $img_name = $_FILES['image']['name'];
                     $img_type = $_FILES['image']['type'];
                     $tmp_name = $_FILES['image']['tmp_name'];  // used for moving the file
                    
                     $img_explode = explode('.',$img_name); // getting extension of img like .jpeg, .png
                     $img_ext = end($img_explode);   // extension of the image 
    
                     $extensions = ["jpeg", "png", "jpg"];

                     if(in_array($img_ext, $extensions) === true){   // if image matches with any of the ext.
    //                     $types = ["image/jpeg", "image/jpg", "image/png"];
                         $time = time();   //  renaming the file uploaded by user with the time
                         $new_img_name = $time.$img_name;
                         $bucketName="chatappimg";
                         try {
                            $s3Client = new S3Client(
                            array(
                                    'credentials' => array(
                                    'key' => $access_key_id,
                                    'secret' => $secret_access_key
                                    ),
                                    'version' => 'latest',
                                    'region'  => 'ap-south-1'
                                    )
                                );
                            } catch (Exception $e) {
                                
                                die("Error: " . $e->getMessage());
                            }
                         try {
                            $file = $_FILES["image"]['tmp_name'];

                            $result= $s3Client->putObject(
                                array(
                                    'Bucket'=>$bucketName,
                                    'Key' =>  $new_img_name,
                                    'SourceFile' => $file,
                                    'ACL'    => 'public-read',
                                    )
                                );
                        } catch (S3Exception $e) {
                            die('Error:' . $e->getMessage());
                        } catch (Exception $e) {
                            die('Error:' . $e->getMessage());
                        }

                            $ran_id = rand(time(), 100000000);   // creating random id for user
                            $status = "Active now";
                            $path_in_s3=$result->get('ObjectURL');  
                            $sql2 = mysqli_query($conn, "INSERT INTO users (unique_id, fname, lname, email, password, img, status) VALUES ({$ran_id}, '{$fname}','{$lname}', '{$email}', '{$password}', '{$path_in_s3}', '{$status}')");
                            if($sql2){  // if data is inserted
                                 $sql3 = mysqli_query($conn, "SELECT * FROM users WHERE email = '{$email}'");
                                 if(mysqli_num_rows($sql3) > 0){
                                     $result = mysqli_fetch_assoc($sql3); 
                                     $_SESSION['unique_id'] = $result['unique_id'];  // session variable that we use in another page
                                     echo "success";
                                 }
                             }else{
                                 echo "Something went wrong";
                             }
                         
                     }else{
                        echo "Please select an Image file - jpeg, jpg, png!";
                     }
                 }
             }
         }else{
            echo "Please Enter Valid Email";
         }
     }else{
        echo "All field are required";
     }
?>