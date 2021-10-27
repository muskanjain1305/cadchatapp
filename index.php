<?php
session_start();
if(isset($_SESSION['unique_id'])){
  header("location: users.php");
}
?>

<?php include_once "header.php" ?>


</body>
</html>
<body>
  <div class="wrapper">
    <section class="form signup">
      <header>Realtime Chat App</header>
      <form action="#" enctype="multipart/form-data">
        <div class="error-txt"></div>
        <div class="name-details">
          <div class="field">
            <label>First Name</label>
            <input type="text" name="fname" placeholder="First name" required>
          </div>

          <div class="field">
            <label>Last Name</label>
            <input type="text" name="lname" placeholder="Last name" required>
          </div>
        </div>
          
        <div class="field">
          <label>Email Address</label>
          <input type="text" name="email" placeholder="Enter your email" required>
        </div>
        <div class="field">
          <label>Password</label>
          <i class="fas fa-eye"></i>
          <input type="password" name="password" placeholder="Enter new password" required>
        </div>
        <div class="field image">
          <label>Select Image</label>
          <input type="file" name="image" required>
        </div>
        <div class="field button">
          <input type="Submit" value="Continue to Chat">
        </div>
        </form>
        <div class="link">Already signed up? <a href="login.php">Login now</a></div>
      </section>
    </div>
    <script src="javascript/pass-show-hide.js"></script>
    <script src="javascript/signup.js"></script>
    </body> 
</html>
            