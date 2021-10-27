<?php
error_reporting(E_ERROR | E_PARSE);
while($row=mysqli_fetch_assoc($sql)){
            $sql2= "SELECT * FROM messages WHERE (incoming_msg_id= {$row['unique_id']}
                    OR outgoing_msg_id = {$row['unique_id']}) AND (outgoing_msg_id = {$outgoing_id}
                    OR incoming_msg_id = {$outgoing_id}) ORDER BY msg_id DESC LIMIT 1";



            $query2= mysqli_query($conn,$sql2);
            $row2= mysqli_fetch_assoc($query2); 
            $you=""; 
            $getkey=mysqli_query($conn,"SELECT * FROM keypairs WHERE (user1={$outgoing_id} AND user2={$row['unique_id']}) 
                                     OR (user2={$outgoing_id} AND user1={$row['unique_id']}) LIMIT 1");

            $fetchkey=mysqli_fetch_assoc($getkey);

            $deckey=$fetchkey['keyp'];
            if(mysqli_num_rows($query2)>0){
                $result=$row2['msg'];
                $c = base64_decode($result);
                $ivlen = openssl_cipher_iv_length($cipher="AES-128-CBC");
                $iv = substr($c, 0, $ivlen);
                $hmac = substr($c, $ivlen, $sha2len=32);
                $ciphertext_raw = substr($c, $ivlen+$sha2len);
                $original_plaintext = openssl_decrypt($ciphertext_raw, $cipher, $deckey, $options=OPENSSL_RAW_DATA, $iv);
                $calcmac = hash_hmac('sha256', $ciphertext_raw, $deckey, $as_binary=true);
                $result=$original_plaintext;
                ($outgoing_id==$row2['outgoing_msg_id']) ? $you= "You: " : $you="";
            }else{
                $result="No messages available";
            }

            //trimming message if length is greater than 24
            (strlen($result)>24) ? $msg = substr($result,0,24).'...' : $msg=$result;

            ($row['status']=="Offline now") ?$offline="offline" : $offline="";

            
            
			$output .= '<a href="chat.php?user_id='.$row['unique_id'].'">
                    <div class="content">
                    <img src="'. $row['img'] .'" alt="">
                    <div class="details">
                        <span>'. $row['fname']. " " . $row['lname'] .'</span>
                        <p>'.$you . $msg.'</p>
                    </div>  
                    </div>
                    <div class="status-dot '.$offline.'"><i class="fas fa-circle"></i></div>
                </a>';
		}
?>