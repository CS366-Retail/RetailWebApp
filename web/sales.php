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
  $sales = getSales();
  $result = [];
  foreach($sales as $sale)
    array_push($result, $sale["id"] . ";" . $sale["firstNameCustomer"] . ";" .$sale["lastNameCustomer"] . ";" . $sale["phoneNumberCustomer"] . ";" . $sale["emailAddressCustomer"] . ";" . $sale["totalPrice"] . ";" . $sale["employeeUsername"]);
  echo implode("\n", $result);
?>