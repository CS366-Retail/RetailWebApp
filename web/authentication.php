<?php
  // $authWith = desired auth mode
  if ($authWith == "pas" && auth_getAuthMode() != "pas")
  {
    header('Location: LoginPas.php');
    exit(1);
  }
  elseif (auth_getAuthMode() == null)
  {
    header('Location: LoginPin.php');
    exit(1);
  }
?>
