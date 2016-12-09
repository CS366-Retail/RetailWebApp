<?php
include '.dbcfg.php';

# $connection = connect();

$TABLE_BuySomeGetSomeCoupons="RWA_BuySomeGetSomeCoupons";
$TABLE_CouponApplicableItems="RWA_CouponApplicableItems";
$TABLE_Coupons="RWA_Coupons";
$TABLE_Employees="RWA_Employees";
$TABLE_Inventory="RWA_Inventory";
$TABLE_InventorySales="RWA_InventorySales";
$TABLE_PercentDiscountCoupons="RWA_PercentDiscountCoupons";
$TABLE_Sales="RWA_Sales";

function createPercentCoupon($couponCode, $expiration, $maxQuantity, $percentDiscount, $inventoryItems)
{
	global $TABLE_CouponApplicableItems;
	global $TABLE_Coupons;
	global $TABLE_PercentDiscountCoupons;
	
	$connection = connect();
	$stmt1 = $connection->prepare("INSERT INTO $TABLE_Coupons (couponCode, expiration, maxQuantity, isPercentDiscount)
	VALUES (?, ?, ?, ?)");
	$stmt1->bind_param("ssii", $couponCode, $expiration, $maxQuantity, $isPercentDiscount);
	$isPercentDiscount = 1;
	$stmt1->execute();
	$stmt2 = $connection->prepare("INSERT INTO $TABLE_CouponApplicableItems (couponCode, inventoryUPC) VALUES (?,?)");
	$stmt2 -> bind_param("ii", $couponCode, $upc);
	foreach ($inventoryItems as $value)
	{
		$upc = $value;
		$stmt2->execute();
	}
	$idQuery = $connection->prepare("SELECT id FROM $TABLE_Coupons WHERE couponCode = $couponCode");
	$idQuery->execute();
	$idQueryResult = $idQuery->get_result();
	$stmt3 = $connection->prepare("INSERT INTO $TABLE_PercentDiscountCoupons (id, percentDiscount)
	VALUES (?, ?)");
	$stmt3 = $connection->bind_param("ii", $id, $percentDiscount);
	$id = $idQueryResult->fetch_assoc()["id"];
	$stmt3->execute();
	
}

function createBSGSCoupon($couponCode, $expiration, $maxQuantity, $qualifyingQuantity, $getQuantity, $pricePercentOfGetItems, $inventoryItems)
{
	global $TABLE_CouponApplicableItems;
	global $TABLE_Coupons;
	global $TABLE_BuySomeGetSomeCoupons;
	
	$connection = connect();
	$stmt1 = $connection->prepare("INSERT INTO $TABLE_Coupons (couponCode, expiration, maxQuantity, isPercentDiscount)
	VALUES (?, ?, ?, ?)");
	$stmt1->bind_param("ssii", $couponCode, $expiration, $maxQuantity, $isPercentDiscount);
	$isPercentDiscount = 0;
	$stmt1->execute();
	$stmt2 = $connection->prepare("INSERT INTO $TABLE_CouponApplicableItems (couponCode, inventoryUPC) VALUES (?,?)");
	$stmt2 -> bind_param("ii", $couponCode, $upc);;
	foreach ($inventoryItems as $value)
	{
		$upc = $value;
		$stmt2->execute();
	}
	$idQuery = $connection->prepare("SELECT id FROM $TABLE_Coupons WHERE couponCode = $couponCode");
	$idQuery->execute();
	$idQueryResult = $idQuery->get_result();
	$stmt3 = $connection->prepare("INSERT INTO $TABLE_PercentDiscountCoupons (id, percentDiscount)
	VALUES (?, ?)");
	$stmt3 = $connection->bind_param("ii", $id, $percentDiscount);
	$id = $idQueryResult->fetch_assoc()["id"];
	$stmt3->execute();
	
}

function isValid($couponCode)
{	
	global $TABLE_Coupons;
	
	$connection = connect();
	$expirationQuery = $connection->prepare("SELECT expiration FROM $TABLE_Coupons WHERE couponCode = $couponCode");
	$expirationQuery->execute();
	$expirationQueryResult = $expirationQuery->get_result();
	$expiration = $expirationQueryResult->fetch_assoc()["expiration"];
	$today = date("Y-m-d H:i:s");
	return $expiration < $today;
	
}

function createInventoryItem($name, $price, $quantity, $couponApplicable=NULL)
{
	global $TABLE_Inventory;
	global $TABLE_CouponApplicableItems;
	
	$connection = connect();
	$stmt1 = $connection->prepare("INSERT INTO $TABLE_Inventory ($TABLE_Inventory.name, price, quantity)
	VALUES (?, ?, ?)");
	$stmt1->bind_param("sdi", $name, $price, $quantity);
	$stmt1->execute();
	$insertedUpc = $this->mysqli->insert_id;
	/*if ($couponApplicable!=NULL)
	{
		$stmt2 = $connection->("INSERT INTO $TABLE_CouponApplicableItems (couponId, inventoryUPC)
		VALUES (?, ?)");
		$stmt2->bind_param("ii", $couponId, $insertedUpc);
		$stmt2->execute();
	}*/
}
function updateInventoryItem($UPC, $changeInQuantity, $price, $name=NULL)
{	
	global $TABLE_Inventory;
	
	$connection = connect();
  
  $changeInQuantity = (int)$changeInQuantity;
	
	$updateInventoryItemQuery;
  if ($name == NULL)
  {
    $updateInventoryItemQuery = $connection->prepare("UPDATE $TABLE_Inventory SET quantity = quantity + ?, price=? WHERE UPC=?");
    $updateInventoryItemQuery->bind_param("idi", $changeInQuantity, $price, $UPC);
  }
  else
  {
    $updateInventoryItemQuery = $connection->prepare("UPDATE $TABLE_Inventory SET quantity = quantity + ?, price=?, name=? WHERE UPC=?");
    $updateInventoryItemQuery->bind_param("idsi", $changeInQuantity, $price, $name, $UPC);
  }
	$updateInventoryItemQuery->execute();
	
}
function setInventoryPrice($UPC, $price)
{	
	global $TABLE_Inventory;
	
	$connection = connect();
	
	$stmt = $connection->prepare("UPDATE $TABLE_Inventory SET price=? WHERE UPC=?");
	$stmt->bind_param("di", $price, $UPC);
	$stmt->execute();
}

//$firstNameCustomer is the first name of the customer who made the purchase
//$lastNameCustomer is the last name of the customer who made the purchase
//$emailAddressCustomer is the e-mail address of the customer who made the purchase
//$employeeId is the id of the employee who made the sale
//$inventoryItems is an int array where each index holds the UPC of the item purchased
//$quantities is an int array where the index contains the amount of the item purchased at the same index in $inventoryItems
function createSale($firstNameCustomer, $lastNameCustomer, $phoneNumberCustomer, $emailAddressCustomer, $employeeName, $inventoryItems, $quantities)
{
  global $TABLE_Sales, $TABLE_InventorySales, $TABLE_Inventory, $TABLE_Employees;
  $connection = connect();
  
  $EmployeeLookupQuery = $connection->prepare("SELECT id FROM $TABLE_Employees WHERE username=?");
  $EmployeeLookupQuery->bind_param("s", $employeeName);
  $EmployeeLookupQuery->execute();
  $EmployeeLookupResult = $EmployeeLookupQuery->get_result();
  $employeeId = $EmployeeLookupResult->fetch_assoc()["id"];
  
  $CreateSaleQuery = $connection->prepare("INSERT INTO $TABLE_Sales (firstNameCustomer, lastNameCustomer, phoneNumberCustomer, emailAddressCustomer, totalPrice, employeeId)
  VALUES (?, ?, ?, ?, ?, ?)");
  $CreateSaleQuery->bind_param("ssssdi", $firstNameCustomer, $lastNameCustomer, $phoneNumberCustomer, $emailAddressCustomer, $totalPrice, $employeeId);
  $totalPrice = 0;
  $totalPrice = 0;
  $CreateSaleQuery->execute();
  
  $InsertID = $connection->insert_id;
  
  $CreateInvSaleQuery = $connection->prepare("INSERT INTO $TABLE_InventorySales (quantity, price, inventoryUPC, saleId) VALUES
  (?, ?, ?, ?)");
  $CreateInvSaleQuery->bind_param("idii", $subQuantity, $subPrice, $subUPC, $InsertID);
  
  $PriceLookupQuery = $connection->prepare("SELECT price FROM $TABLE_Inventory WHERE UPC=?");
  $PriceLookupQuery->bind_param("i", $subUPC);
  
  $InventoryUpdateQuery = $connection->prepare("UPDATE $TABLE_Inventory SET quantity=quantity-? WHERE UPC=?");
  $InventoryUpdateQuery->bind_param("ii", $subQuantity, $subUPC);
  
  for($i = 0; $i < count($inventoryItems); $i++)
  {
    $subUPC = $inventoryItems[$i];
    $subQuantity = $quantities[$i];
    
    $PriceLookupQuery->execute();
    $PriceLookupResult = $PriceLookupQuery->get_result();
    
    $subPrice = $PriceLookupResult->fetch_assoc()["price"] * (int)$subQuantity;
    
    $CreateInvSaleQuery->execute();
    
    $InventoryUpdateQuery->execute();
    
    $totalPrice += $subPrice;
  }
  
  $UpdateSaleQuery = $connection->prepare("UPDATE $TABLE_Sales SET totalPrice=? WHERE id=?");
  $UpdateSaleQuery->bind_param("di", $totalPrice, $InsertID);
  $UpdateSaleQuery->execute();
  
  return true;
}

//$firstName is the employee's first name
//$lastName is the employee's last name
//$username is the employee's chosen/assigned username
//$password is the employee's chosen/assigned password
//$pin is the employee's chosen/assigned pin
function createEmployee($firstName, $lastName, $username, $password, $pin)
{
	global $TABLE_Employees;
	
	$connection = connect();
	$stmt = $connection->prepare("INSERT INTO $TABLE_Employees (firstName, lastName, username, passHash, pinHash, salt) 
	VALUES (?, ?, ?, ?, ?, ?)");
	$stmt->bind_param("ssssss", $first, $last, $user, $pass, $pinNo, $salt);
	$first = $firstName;
	$last = $lastName;
	$user = $username;
	$salt = bin2hex(random_bytes(32));
	$password = $salt . $password;
	$pass = hex2bin(hash("sha256", $password));
	$pin = $salt . $pin;
	$pinNo = hex2bin(hash("sha256", $pin));
    $salt = hex2bin($salt);
	$stmt->execute();
}
function getEmployees()
{
  global $TABLE_Employees;
  
  $result = array();
  
  $connection = connect();
  $sql = "SELECT * FROM $TABLE_Employees ORDER BY lastName";
  $queryResult = $connection->query($sql);
  while ($row = $queryResult->fetch_assoc())
    array_push($result, $row);
  
  return $result;
}
function updateEmployee($id, $firstName, $lastName, $username, $pass = null, $pin = null)
{	
	global $TABLE_Employees;
	
	$connection = connect();
  
	$updateEmployeeQuery;
  if ($pass == NULL || $pin == null)
  {
    $updateEmployeeQuery = $connection->prepare("UPDATE $TABLE_Employees SET firstName = ?, lastName = ?, username = ? WHERE id = ?");
    $updateEmployeeQuery->bind_param("sssi", $firstName, $lastName, $username, $id);
  }
  else
  {
    // todo add support for pass/pin change
    $updateEmployeeQuery = $connection->prepare("UPDATE $TABLE_Employees SET firstName = ?, lastName = ?, username = ? WHERE id = ?");
    $updateEmployeeQuery->bind_param("sssi", $firstName, $lastName, $username, $id);
  }
	$updateEmployeeQuery->execute();
}

//$upc is the upc of the item you want the price and name of
function getPriceAndName($upc)
{
	global $TABLE_Inventory;
	
	$connection = connect();
	$sql = "SELECT price, name FROM $TABLE_Inventory WHERE UPC=$upc";
	$result = $connection->query($sql);
  $row = mysqli_fetch_assoc($result);
  $price = $row["price"];
  $name = $row["name"];
  $priceAndName = array("price"=>$price, "name"=>$name);
	return $priceAndName;
}

function validateEmployeePassword($username, $password)
{
	global $TABLE_Employees;
	
	$connection = connect();
	$saltQuery = $connection->prepare("SELECT salt FROM $TABLE_Employees WHERE username= ?");
	$saltQuery->bind_param("s", $username);
	$saltQuery->execute();
	$saltResult = $saltQuery->get_result();
	$salt = bin2hex($saltResult->fetch_assoc()["salt"]);
	if ($salt == "")
	{
		return false;
	}
	else
	{
		$password = $salt . $password;
		$password = hash("sha256", $password);
		$passQuery = $connection->prepare("SELECT passHash FROM $TABLE_Employees WHERE username= ?");
		$passQuery->bind_param("s", $username);
		$passQuery->execute();
		$passResult = $passQuery->get_result();
		$hashedPass = bin2hex($passResult->fetch_assoc()["passHash"]);
		return $hashedPass == $password;
	}
}
//$username is a string value containing the employee's username
//$pin is a string value containing the employee's pin
function validateEmployeePin($username, $pin)
{  
	global $TABLE_Employees;
	
	$connection = connect();
	$saltQuery = $connection->prepare("SELECT salt FROM $TABLE_Employees WHERE username= ?");
	$saltQuery->bind_param("s", $username);
	$saltQuery->execute();
	$saltResult = $saltQuery->get_result();
	$salt = bin2hex($saltResult->fetch_assoc()["salt"]);
	if ($salt == "")
	{
		return false;
	}
	else
	{
		$pin = $salt . $pin;
		$pin = hash("sha256", $pin);
		$pinQuery = $connection->prepare("SELECT pinHash FROM $TABLE_Employees WHERE username= ?");
		$pinQuery->bind_param("s", $username);
		$pinQuery->execute();
		$pinResult = $pinQuery->get_result();
		$hashedPin = bin2hex($pinResult->fetch_assoc()["pinHash"]);
		return $hashedPin == $pin;
	}
}

function totalInventory()
{
	$totalInvQuery = $connection->prepare("SELECT SUM(quantity) AS totalInv FROM $TABLE_Inventory"); 
	$totalInvQuery->execute();
	$totalInvResult = $totalInvQuery->get_result();
	$totalInv = $totalInvQuery->fetch_assoc()["totalInv"];
	return $totalInv;
}

function getInventory()
{
  global $TABLE_Inventory;
  
  $result = array();
  
  $connection = connect();
  $sql = "SELECT * FROM $TABLE_Inventory";
  $queryResult = $connection->query($sql);
  while ($row = $queryResult->fetch_assoc())
    array_push($result, $row);
  
  return $result;
}

function getSales()
{
  global $TABLE_Sales, $TABLE_Employees;
  
  $result = array();
  
  $connection = connect();
  $sql = "SELECT $TABLE_Sales.id, firstNameCustomer, lastNameCustomer, phoneNumberCustomer, emailAddressCustomer, totalPrice, username AS employeeUsername
          FROM $TABLE_Sales
          LEFT JOIN $TABLE_Employees
          ON $TABLE_Sales.employeeId=$TABLE_Employees.id";
  $queryResult = $connection->query($sql);
  while ($row = $queryResult->fetch_assoc())
    array_push($result, $row);
  
  return $result;}
?>
