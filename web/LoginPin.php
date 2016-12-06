<?php include 'static.php'; ?>
<?php include '../db/dbfunctions.php'; ?>
<?php
  auth_logout();
  
  if (isset($_POST["username"]) && isset($_POST["pin"]))
  {
    if (validateEmployeePin($_POST["username"], $_POST["pin"]))
    {
      auth_loginViaPin($_POST["username"]);
      header('Location: NewSale.php');
      exit(0);
    }
  }
?>
<html>
  <head>
    <?php printHead(); ?>
    <link rel='stylesheet' type='text/css' href='../style/login.css'>
    <script>
      function pinPad(n)
      {
        document.forms["login"].elements["pin"].value += n;
      }
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
          <tr><td>pin</td></tr>
          <tr><td><input type="password" name="pin" /></td><tr>
          <tr><td>
            <table class="pinPad">
              <tr>
                <td><button class="pinNumber" onclick="pinPad(7)">7</button></td>
                <td><button class="pinNumber" onclick="pinPad(8)">8</button></td>
                <td><button class="pinNumber" onclick="pinPad(9)">9</button></td>
              </tr>
              <tr>
                <td><button class="pinNumber" onclick="pinPad(4)">4</button></td>
                <td><button class="pinNumber" onclick="pinPad(5)">5</button></td>
                <td><button class="pinNumber" onclick="pinPad(6)">6</button></td>
              </tr>
              <tr>
                <td><button class="pinNumber" onclick="pinPad(1)">1</button></td>
                <td><button class="pinNumber" onclick="pinPad(2)">2</button></td>
                <td><button class="pinNumber" onclick="pinPad(3)">3</button></td>
              </tr>
              <tr>
                <td><button class="pinNumber pinNumber0" onclick="pinPad(0)">0</button</td>
                <td colspan="2"><button class="pinSubmit" onclick="validate()">submit</button></td>
              </tr>
            </table>
          </td></tr>
        </table>
      </form>
    </div>
  </body>
</html>