<?php
  include 'static.php';
  
  /*
  if (auth_getAuthMode() == "pin")
    header('Location: NewSale.php');
  elseif (auth_getAuthMode() == "pas")
    header('Location: Management.php');
  else
    header('Location: LoginPin.php');
  */
?>

<html>
  <body>
    <?php $username = auth_getUsername(); if ($username != null && !empty($username)) { echo "Hello " . $username . "<br />"; }?>
    <a href="NewSale.php">Checkout</a><br />
    <a href="Management.php">Management</a><br />
    <a href="Logout.php">Logout</a>
  </body>
</html>