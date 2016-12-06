<?php include 'static.php'; ?>
<?php
  //include 'authentication.php';
  if (auth_getAuthMode() != "pas")
  {
    http_response_code(401);
    echo "You must be logged in to use this action";
    exit(0);
  }
?>
<?php include '../db/dbfunctions.php'; ?>
<?php

if (isset($_POST["upc"]) && !empty($_POST["upc"]))
{
  updateInventoryItem($_POST["upc"], $_POST["changeInQuantity"], $_POST["price"], $_POST["name"]);
}
elseif (isset($_POST["name"]) && !empty($_POST["name"]))
{
  createInventoryItem($_POST["name"], 0, 0);
}
else
{
  $inventory = getInventory();
  $result = [];
  foreach($inventory as $item)
    echo array_push($result, $item["UPC"] . ";" . $item["name"] . ";" . $item["quantity"] . ";" . $item["price"]);
    
  echo implode("\n", $result);
}
?>