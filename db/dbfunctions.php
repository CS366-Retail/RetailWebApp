
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
/*
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
	$sql = "SELECT id FROM $TABLE_Coupons WHERE couponCode = $couponCode";
	$stmt3 = $connection->prepare("INSERT INTO $TABLE_PercentDiscountCoupons (id, percentDiscount)
	VALUES (?, ?)");
	$stmt3 = $connection->bind_param("ii", $id, $percentDiscount);
	$id = $connection->query($sql);
	$stmt3->execute();
}
*//*
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
	$sql = "SELECT id FROM $TABLE_Coupons WHERE couponCode = $couponCode";
	$stmt3 = $connection->prepare("INSERT INTO $TABLE_BuySomeGetSomeCoupons (id, qualifyingQuantity, getQuantity, pricePercentOfGetItems)
	VALUES (?, ?, ?, ?)");
	$stmt3 = $connection->bind_param("iiii", $id, $qualifyingQuantity, $getQuantity, $pricePercentOfGetItems);
	$id = $connection->query($sql);
	$stmt3->execute();
	
}*//*
function isValid($couponCode)
{	
	global $TABLE_Coupons;
	
	$connection = connect();
	$sql = "SELECT expiration FROM $TABLE_Coupons WHERE couponCode = $couponCode";
	$expiration = $connection->query($sql);
	$today = date("Y-m-d H:i:s");
	return $expiration < $today;
	
}*/

function createInventoryItem($name, $price, $quantity, $couponApplicable=NULL)
{
	global $TABLE_Inventory;
	global $TABLE_CouponApplicableItems;
	
	$connection = connect();
	$stmt1 = $connection->prepare("INSERT INTO $TABLE_Inventory (itemName, price, quantity)
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
function updateInventoryItem($UPC, $changeInQuantity, $name=NULL)
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
//$coupons is an int array where the index contains the couponCode for the item purchased at the same index in $inventoryItems
/*function createSale($firstNameCustomer, $lastNameCustomer, $phoneNumberCustomer, $emailAddressCustomer, $employeeId, $inventoryItems, $quantities)
{
	global $TABLE_Sales;
	global $TABLE_Inventory;
	global $TABLE_InventorySales;
	global $TABLE_PercentDiscountCoupons;
	
	$connection = connect();
  
	$saleQuery = $connection->prepare("INSERT INTO $TABLE_Sales (firstNameCustomer, lastNameCustomer, phoneNumberCustomer, emailAddressCustomer, totalPrice, employeeId)
	VALUES (?, ?, ?, ?, ?, ?)");
	$saleQuery->bind_param("ssssdi", $firstNameCustomer, $lastNameCustomer, $phoneNumberCustomer, $emailAddressCustomer, $priceZero, $employeeId);
  $priceZero = 0;
	$saleQuery->execute();
	$insertedId = $connection->insert_id;
  
	$inventorySalesStmt = $connection->prepare("INSERT INTO $TABLE_InventorySales (quantity, price, inventoryUPC, saleId)
	VALUES (?, ?, ?, ?)");
	$inventorySalesStmt->bind_param("idii", $purchaseQuantity, $itemPrice, $upc, $insertedId);
  
	$totalPrice = 0.0;
	//if (count($coupons==0))
	//{
    
  $priceQuery = $connection->prepare("SELECT price FROM $TABLE_Inventory WHERE UPC=?");
  $priceQuery->bind_param("i", $upc);
  $priceQuery->bind_result($itemPrice);
  for ($i = 0; $i < count($inventoryItems); $i++)
  {
    $upc = $inventoryItems[$i];
    $purchaseQuantity = $quantities[$i];
    
    $priceQuery->execute();
    $priceQuery->fetch();
    
    $subTotalPrice = $itemPrice * (int)$purchaseQuantity; 
    
    $totalPrice += $subTotalPrice;
    
    // update inventory quantity
    $q = "UPDATE $TABLE_Inventory SET quantity=quantity - $purchaseQuantity WHERE UPC = $upc";
    $connection->query($q);
    
    $updateQuery = $connection->prepare("UPDATE $TABLE_Inventory SET quantity=? WHERE UPC=?");
    $updateQuery->bind_param("ii", $purchaseQuantity, $upc);
    $updateQuery->execute(); 
    
    
    // commit inventorySale
    $inventorySalesStmt = $connection->prepare("INSERT INTO $TABLE_InventorySales (quantity, price, inventoryUPC, saleId)
    VALUES (?, ?, ?, ?)");
    $inventorySalesStmt->bind_param("idii", $purchaseQuantity, $itemPrice, $upc, $insertedId);
    $inventorySalesStmt->execute();
  }
  $q = "UPDATE $TABLE_Sales SET totalPrice = $totalPrice WHERE id = $insertedId";
  $connection->query($q);
	//}
	else
	{
		for ($i = 0; $i < count($inventoryItems); $i++)
		{
			$upc = $inventoryItems[$i];
			$purchaseQuantity = $quantities[$i];
			$sql = "SELECT price FROM $TABLE_Inventory WHERE UPC=$upc";
			$itemPrice = $connection->query($sql);
			$sql = "SELECT percentDiscount FROM $TABLE_PercentDiscountCoupons WHERE id=$coupons[$i]";
			$discount = $connection->query($sql) / 100;
			$discountedItemPrice = $discount * $itemPrice;
			$price = $discountedItemPrice * $quantities[$i];			
			$totalPrice = $totalPrice + $price;
			$stmt4 = $connection->prepare("UPDATE $TABLE_Inventory SET quantity=? WHERE UPC=$upc");
			$stmt4->bind_param("i", $quantity);
			$sql = "SELECT quantity FROM $TABLE_Inventory WHERE UPC=$upc";
			$inventoryQuantity = $connection->query($sql);
			$inventoryQuantity = $inventoryQuantity - $purchaseQuantity;
			$stmt4->execute();
			$couponId = $coupons[$i];
			$inventorySaleStmt->execute();
		}
	}
	return true;
}*/
function createSale($firstNameCustomer, $lastNameCustomer, $phoneNumberCustomer, $emailAddressCustomer, $employeeId, $inventoryItems, $quantities)
{
  global $TABLE_Sales, $TABLE_InventorySales, $TABLE_Inventory;
  $connection = connect();
  
  $CreateSaleQuery = $connection->prepare("INSERT INTO $TABLE_Sales (firstNameCustomer, lastNameCustomer, phoneNumberCustomer, emailAddressCustomer, totalPrice, employeeId)
  VALUES (?, ?, ?, ?, ?, ?)");
  $CreateSaleQuery->bind_param("ssssdi", $firstNameCustomer, $lastNameCustomer, $phoneNumberCustomer, $emailAddressCustomer, $totalPrice, $employeeId);
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
  $sql = "SELECT * FROM $TABLE_Employees";
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
/*
//$username is string value containing the employee's username
//$pin is the string value containing the employee's pin
function validateEmployeePassword($username, $password)
{
	global $TABLE_Employees;
	
	$connection = connect();
	$sql = "SELECT salt FROM $TABLE_Employees WHERE username=$username";
	$salt = $connection->query($sql);
	if ($salt == false)
	{
		echo "Username $username not found";
		return false;
	}
	else
	{
		$password = $salt . $password;
		$password = hex2bin(hash("sha256", $password));
		$sql = "SELECT passHash FROM $TABLE_Employees WHERE username=$username";
		$hashedPass = $connection->query($sql);
		return $hashedPass == $password;
		echo "We here fam";
	}
}
*//*
//$username is a string value containing the employee's username
//$pin is a string value containing the employee's pin
function validateEmployeePin($username, $pin)
{  
	global $TABLE_Employees;
	
	$connection = connect();
	$sql = "SELECT salt FROM $TABLE_Employees WHERE username=$username";
	$salt = $connection->query($sql);
	if ($salt != false)
	{
		echo "Username $username not found";
		return false;
	}
	else
	{
		$pin = $salt . $pin;
		$pin = hex2bin(hash("sha256", $pin));
		$sql = "SELECT pinHash FROM $TABLE_Employees WHERE username=$username";
		$hashedPin = $connection->query($sql);
		return $hashedPin == $pin;
	}
}*/

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
function validateEmployeePin($a, $b) { return true; }
function validateEmployeePassword($a, $b) {return true;}


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
?>
