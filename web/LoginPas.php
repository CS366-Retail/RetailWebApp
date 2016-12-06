<?php include 'static.php'; ?>
<?php include '../db/dbfunctions.php'; ?>
<?php
  auth_logout();
  
  if (isset($_POST["username"]) && isset($_POST["pas"]))
  {
    if (validateEmployeePassword($_POST["username"], $_POST["pas"]))
    {
      auth_loginViaPassword($_POST["username"]);
      header('Location: Management.php');
      exit(0);
    }
  }
?>
<html>
  <head>
    <?php printHead(); ?>
    <link rel='stylesheet' type='text/css' href='../style/login.css'>
    <script>
      function validate()
      {
        document.forms["login"].submit();
      }
    </script>
  </head>
  <body>
    <div class="container logincontainer">
      <form name="login" action="" method="post">
        <table>
          <tr><td>username</td></tr>
          <tr><td><input type="text" name="username" /></td></tr>
          <tr><td>password</td></tr>
          <tr><td><input type="password" name="pas" /></td><tr>
          <td><button class="pasSubmit" onclick="validate()">submit</button></td>
        </table>
      </form>
    </div>
  </body>
</html>