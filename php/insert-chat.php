<?php
	session_start();

	if (isset($_SESSION['unique_id'])){
		include_once "config.php";

		$outgoing_id=mysqli_real_escape_string($conn,$_POST['outgoing_id']);
		$incoming_id=mysqli_real_escape_string($conn,$_POST['incoming_id']);
		$message=mysqli_real_escape_string($conn,$_POST['message']);



		$present=mysqli_query($conn,"SELECT * FROM keypairs WHERE (user1={$outgoing_id} AND user2={$incoming_id}) 
									 OR (user2={$outgoing_id} AND user1={$incoming_id})");


		if (mysqli_num_rows($present)>0){
			$keyrow=mysqli_fetch_assoc($present);
			$keyenc=$keyrow['keyp'];

		}else{
			$keyenc=rand(1,1000);
			$numkeys=mysqli_query($conn,"SELECT * FROM keypairs WHERE keyp={$keyenc}");

			while(mysqli_num_rows($numkeys)>0){
				$keyenc=rand(1,1000);
				$numkeys=mysqli_query($conn,"SELECT * FROM keypairs WHERE keyp={$keyenc}");
			}
			
			$insertquery= mysqli_query($conn, "INSERT INTO keypairs (user1,user2,keyp) 
											   VALUES ({$incoming_id}, {$outgoing_id}, {$keyenc})");
			echo $insertquery."done";


		}

		$ivlen = openssl_cipher_iv_length($cipher="AES-128-CBC");
		$iv = openssl_random_pseudo_bytes($ivlen);
		$ciphertext_raw = openssl_encrypt($message, $cipher, $keyenc, $options=OPENSSL_RAW_DATA, $iv);
		$hmac = hash_hmac('sha256', $ciphertext_raw, $keyenc, $as_binary=true);
		$ciphertext = base64_encode( $iv.$hmac.$ciphertext_raw );


		if(!empty($message)){
			$sql = mysqli_query($conn, "INSERT INTO messages (incoming_msg_id, outgoing_msg_id, msg)
                                        VALUES ({$incoming_id}, {$outgoing_id}, '{$ciphertext}')") or die();
		}
	}
	else{
		header("../login.php");
	}
?>