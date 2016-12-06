<?php
  include 'static.php';
  
  if (auth_getAuthMode() == "pin")
    header('Location: NewSale.php');
  elseif (auth_getAuthMode() == "pas")
    header('Location: Management.php');
  else
    header('Location: LoginPin.php');
?>