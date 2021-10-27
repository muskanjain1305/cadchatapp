<?php
	session_start();

	if (isset($_SESSION['unique_id'])){
		include_once "config.php";

		$outgoing_id=mysqli_real_escape_string($conn,$_POST['outgoing_id']);
		$incoming_id=mysqli_real_escape_string($conn,$_POST['incoming_id']);

		$output = "";
        $sql = "SELECT * FROM messages WHERE (outgoing_msg_id = {$outgoing_id} AND incoming_msg_id = {$incoming_id})
                OR (outgoing_msg_id = {$incoming_id} AND incoming_msg_id = {$outgoing_id}) ORDER BY msg_id";

        
        $query = mysqli_query($conn, $sql);
        if(mysqli_num_rows($query) > 0){

            $getkey=mysqli_query($conn,"SELECT * FROM keypairs WHERE (user1={$outgoing_id} AND user2={$incoming_id}) 
                                     OR (user2={$outgoing_id} AND user1={$incoming_id}) LIMIT 1");
            $deckey=$getkey['keyp'];


        	while($row=mysqli_fetch_assoc($query)){

                $c = base64_decode($row['msg']);
                $ivlen = openssl_cipher_iv_length($cipher="AES-128-CBC");
                $iv = substr($c, 0, $ivlen);
                $hmac = substr($c, $ivlen, $sha2len=32);
                $ciphertext_raw = substr($c, $ivlen+$sha2len);
                $original_plaintext = openssl_decrypt($ciphertext_raw, $cipher, $deckey, $options=OPENSSL_RAW_DATA, $iv);
                $calcmac = hash_hmac('sha256', $ciphertext_raw, $key, $as_binary=true);
                $original_plaintext="kunal";

        		if($row['outgoing_msg_id'] === $outgoing_id){
                    $output .= '<div class="chat outgoing">
                                <div class="details">
                                    <p>'. $original_plaintext .'</p>
                                </div>
                                </div>';
                }else{
                    $output .= '<div class="chat incoming">
                                <img src="php/images/'.$row['img'].'" alt="">
                                <div class="details">
                                    <p>'. $original_plaintext .'</p>
                                </div>
                                </div>';
                }
        	}
        }

	}
	else{
		header("../login.php");
	}
?>