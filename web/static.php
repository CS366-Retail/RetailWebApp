<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


function printHead()
{
  echo "<link rel='stylesheet' type='text/css' href='../style/theme.css'>";
  echo "<link rel='stylesheet' href='//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css'>";
  echo "<script src='https://code.jquery.com/jquery-1.12.4.js'></script>";
  echo "<script src='https://code.jquery.com/ui/1.12.1/jquery-ui.js'></script>";
}

function printNav()
{
  
}

function printFooter()
{
  
}

$KEYNAME_PIN = "rwa-cs366-fa16-user-pin";
$KEYNAME_PAS = "rwa-cs366-fa16-user-pas";

function auth_loginViaPin($username)
{
  global $KEYNAME_PIN, $KEYNAME_PAS;
  setcookie(
    $KEYNAME_PIN,
    $username,
    time()+3600
  );
}
function auth_loginViaPassword($username)
{
  global $KEYNAME_PIN, $KEYNAME_PAS;
  setcookie(
    $KEYNAME_PAS,
    $username,
    time()+3600
  );
}

function auth_getAuthMode()
{
  global $KEYNAME_PIN, $KEYNAME_PAS;
  if (isset($_COOKIE[$KEYNAME_PIN]) && !empty($_COOKIE[$KEYNAME_PIN]))
    return "pin";
  elseif (isset($_COOKIE[$KEYNAME_PAS]) && !empty($_COOKIE[$KEYNAME_PAS]))
    return "pas";
  else
    return null;
}

function auth_getUsername()
{
  global $KEYNAME_PIN, $KEYNAME_PAS;
  $mode = getAuthMode();
  if ($mode == "pin")
    return $_COOKIE[$KEYNAME_PIN];
  elseif ($mode == "pas")
    return $_COOKIE[$KEYNAME_PAS];
  else
    return null;
}

function auth_renew()
{
  global $KEYNAME_PIN, $KEYNAME_PAS;
  if (isset($_COOKIE[$KEYNAME_PIN]))
  { setcookie($KEYNAME_PIN, $_COOKIE[$KEYNAME_PIN], time()+3600); }
  elseif (isset($_COOKIE[$KEYNAME_PAS]))
  { setcookie($KEYNAME_PAS, $_COOKIE[$KEYNAME_PAS], time()+3600); }
}

function auth_logout()
{
  global $KEYNAME_PIN, $KEYNAME_PAS;
  if (isset($_COOKIE[$KEYNAME_PIN]))
  { setcookie($KEYNAME_PIN, "", time()-3600); }
  if (isset($_COOKIE[$KEYNAME_PAS]))
  { setcookie($KEYNAME_PAS, "", time()-3600); }
}

?>