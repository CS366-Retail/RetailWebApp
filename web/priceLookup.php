<?php include 'static.php'; ?>
<?php
  //include 'authentication.php';
  if (auth_getAuthMode() == null)
  {
    http_response_code(401);
    echo "You must be logged in to use this action";
    exit(0);
  }
?>
<?php include '../db/dbfunctions.php'; ?>

<?php
  if (isset($_POST["upc"]) && ctype_digit($_POST["upc"]))
  {
    $nameAndPrice = getPriceAndName($_POST["upc"]);
    if (!empty($nameAndPrice["price"]) && !empty($nameAndPrice["name"]))
    {
      http_response_code(200);
      echo $nameAndPrice["price"] . " " . $nameAndPrice["name"];
    }
    else
    {
      http_response_code(400);
      echo "No match found";
    }
  }
  elseif (isset($_GET["upc"]) && ctype_digit($_GET["upc"]))
  {
    $nameAndPrice = getPriceAndName($_GET["upc"]);
    if (!empty($nameAndPrice["price"]) && !empty($nameAndPrice["name"]))
    {
      http_response_code(200);
      echo $nameAndPrice["price"] . " " . $nameAndPrice["name"];
    }
    else
    {
      http_response_code(400);
      echo "No match found";
    }
  }
  else
  {
    http_response_code(400);
    echo "No UPC specified";
  }
?>
