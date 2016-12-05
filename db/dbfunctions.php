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
	$sql = "SELECT id FROM $TABLE_Coupons WHERE couponCode = $couponCode";
	$stmt3 = $connection->prepare("INSERT INTO $TABLE_PercentDiscountCoupons (id, percentDiscount)
	VALUES (?, ?)";
	$stmt3 = connection->bind_param("ii", $id, $percentDiscount);
	$id = $connection->query($sql);
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
	$sql = "SELECT id FROM $TABLE_Coupons WHERE couponCode = $couponCode";
	$stmt3 = $connection->prepare("INSERT INTO $TABLE_BuySomeGetSomeCoupons (id, qualifyingQuantity, getQuantity, pricePercentOfGetItems)
	VALUES (?, ?, ?, ?)";
	$stmt3 = connection->bind_param("iiii", $id, $qualifyingQuantity, $getQuantity, $pricePercentOfGetItems)
	$id = $connection->query($sql);
	$stmt3->execute();
	
}
function isValid($couponCode)
{	
	global $TABLE_Coupons;
	
	$connection = connect();
	$sql = "SELECT expiration FROM $TABLE_Coupons WHERE couponCode = $couponCode";
	$expiration = $connection->query($sql);
	$today = date("Y-m-d H:i:s");
	return $expiration < $today;
	
}
function createInventoryItem($name, $price, $quantity)
{
	global $TABLE_Inventory;
	
	$connection = connect();
	$stmt = $connection->prepare("INSERT INTO $TABLE_Inventory (itemName, price, quantity)
	VALUES (?, ?, ?)");
	$stmt(bind_param("sdi", $name, $price, $quantity);
	$stmt->execute();
}
function addInventoryQuantity($UPC, $quantity)
{	
	global $TABLE_Inventory;
	
	$connection = connect();
	
	$stmt = $connection->prepare("UPDATE $TABLE_Inventory SET quantity=? WHERE UPC=?");
	$stmt->bind_param("ii", $quantity, $UPC);
	$sql = "SELECT quantity FROM $TABLE_Inventory WHERE UPC=$UPC";
	$quantity = $connection->query($sql) + $quantity;
	$stmt->execute();
	
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
function createSale($firstNameCustomer, $lastNameCustomer, $phoneNumberCustomer, $emailAddressCustomer, $employeeId, $inventoryItems, $quantities, $coupons)
{
	global $TABLE_Sales;
	global $TABLE_Inventory;
	global $TABLE_InventorySales;
	
	$connection = connect();
	$stmt1 = $connection->prepare("INSERT INTO $TABLE_Sales (firstNameCustomer, lastNameCustomer, phoneNumberCustomer, emailAddressCustomer, totalPrice, employeeId)
	VALUES (?, ?, ?, ?, ?, ?)");
	$stmt1->bind_param("ssssdi", $firstNameCustomer, $lastNameCustomer, $phoneNumberCustomer, $emailAddressCustomer, $totalPrice, $employeeId);
	$totalPrice = 0.0;
	$stmt1->execute();
	$insertedId = $this->mysqli->insert_id;
	$inventorySalesStmt = $connection->prepare("INSERT INTO $TABLE_InventorySales (quantity, price, inventoryUPC, saleId, couponId)
	VALUES (?, ?, ?, ?, ?");
	$inventorySaleStmt->bind_param("idiii", $purchaseQuantity, $itemPrice, $upc, $saleId, $couponId);
	$saleId = $insertedId;
	if(count($coupons==0)
	{
		for ($i = 0; $i < count($inventoryItems); $i++)
		{
			$upc = $inventoryItems[$i];
			$purchaseQuantity = $quantities[$i];
			$sql = "SELECT price FROM $TABLE_Inventory WHERE UPC=$upc";
			$itemPrice = $connection->query($sql);
			$price = $itemPrice * $purchaseQuantity; 
			$totalPrice = $totalPrice + $price;
			$stmt2 = $connection->prepare("UPDATE $TABLE_Inventory SET quantity=? WHERE UPC=$upc");
			$stmt2->bind_param("i", $inventoryQuantity);
			$sql = "SELECT quantity FROM $TABLE_Inventory WHERE UPC=$upc";
			$inventoryQuantity = $connection->query($sql);
			$inventoryQuantity = $inventoryQuantity- $purchaseQuantity;
			$stmt2->execute();
			$couponId = "NULL";
			$inventorySaleStmt->execute();
		}
		
	}
	else
	{
		for ($i = 0; $i < count($inventoryItems); $i++)
		{
			$upc = $inventoryItems[$i];
			$sql = "SELECT price FROM $TABLE_Inventory WHERE UPC=$upc";
			$price = $connection->query($sql);
			$price = $price * $quantities[$i]; 
			$totalPrice = $totalPrice + $price;
			$stmt4 = $connection->prepare("UPDATE $TABLE_Inventory SET quantity=? WHERE UPC=$upc");
			$stmt4->bind_param("i", $quantity);
			$sql = "SELECT quantity FROM $TABLE_Inventory WHERE UPC=$upc";
			$quantity = $connection->query($sql);
			$quantity = $quantity - $quantities[$i];
			$stmt4->execute();
		}
	}
	
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

//$username is string value containing the employee's username
//$pin is the string value containing the employee's pin
function validateEmployeePassword($username, $password)
{
	global $TABLE_Employees;
	
	$connection = connect();
	$sql = "SELECT salt FROM $TABLE_Employees WHERE username=$username";
	$salt = $connection->query($sql);
	if ($salt->num_rows == 0)
	{
		echo "Username $username not found";
		return false;
	}
	else
	{
		$password = $salt . $password;
		$password = hex2bin(hash("sha256", $password);
		$sql = "SELECT passHash FROM $TABLE_Employees WHERE username=$username";
		$hashedPass = $connection->query($sql);
		return $hashedPass == $password;
	}
}

//$username is a string value containing the employee's username
//$pin is a string value containing the employee's pin
function validateEmployeePin($username, $pin)
{  
	global $TABLE_Employees;
	
	$connection = connect();
	$sql = "SELECT salt FROM $TABLE_Employees WHERE username=$username";
	$salt = $connection->query($sql);
	if ($salt->num_rows == 0)
	{
		echo "Username $username not found";
		return false;
	}
	else
	{
		$pin = $salt . $pin;
		$pin = hex2bin(hash("sha256", $pin);
		$sql = "SELECT pinHash FROM $TABLE_Employees WHERE username=$username";
		$hashedPin = $connection->query($sql);
		return $hashedPin == $pin;
	}
}

//$upc is the upc of the item you want the price and name of
function getPriceAndName($upc)
{
	global $TABLE_Inventory;
	
	$connection = connect();
	$sql = "SELECT price FROM $TABLE_Inventory WHERE UPC=$upc";
	$price = $connection->query($sql);
	$sql = "SELECT itemName FROM $TABLE_Inventory WHERE UPC=$upc";
	$name = $connection->query($sql);
	$priceAndName = array("price"=>$price, "name"=>$name);
	return $priceAndName;
}
?>