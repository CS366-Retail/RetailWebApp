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

$firstNameCustomer = $_POST["firstNameCustomer"];
$lastNameCustomer = $_POST["lastNameCustomer"];
$phoneNumberCustomer = $_POST["phoneNumberCustomer"];
$emailAddressCustomer = $_POST["emailAddressCustomer"];
$employeeName = auth_getUsername();
$inventoryItems = $_POST["inventoryItems"];
$quantities = $_POST["quantities"];
$coupons = $_POST["coupons"];

$inventoryItems = explode(";", $inventoryItems);
$quantities = explode(";", $quantities);
$coupons = explode(";", $coupons);

if (createSale($firstNameCustomer, $lastNameCustomer, $phoneNumberCustomer, $emailAddressCustomer, $employeeName, $inventoryItems, $quantities))
{
  http_response_code(201);
  echo "Sale successful";
}
else
{
  http_response_code(400);
  echo "There was an error submitting your request";
}
?>