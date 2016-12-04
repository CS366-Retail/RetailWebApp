<?php

include '.dbcfg.php';

# $connection = connect();

$TABLE_BuySomeGetSomeCoupons="BuySomeGetSomeCoupons";
$TABLE_CouponApplicableItems="CouponApplicableItems";
$TABLE_Coupons="Coupons";
$TABLE_Employees="Employees";
$TABLE_Inventory="Inventory";
$TABLE_InventorySales="InventorySales";
$TABLE_PercentDiscountCoupons="PercentDiscountCoupons";
$TABLE_Sales="Sales";

function createPercentCoupon($couponCode, $expiration, $maxQuantity, $percentDiscount, $inventoryItems)
{
  global $TABLE_CouponApplicableItems;
  global $TABLE_Coupons;
  
  $connection = connect();
	$stmt1 = $connection->prepare("INSERT INTO $TABLE_Coupons (couponCode, expiration, maxQuantity, isPercentDiscount)
	VALUES (?, ?, ?, ?)");
	$stmt1->bind_param("ssii", $cc, $exp, $max, $isPercent);
	$cc = $couponCode;
	$exp =$expiration;
	$max = $maxQuantity;
	$isPercent = $percentDiscount;
	$stmt1->execute();
	$stmt2 = $connection->prepare("INSERT INTO $TABLE_CouponApplicableItems (couponCode, inventoryUPC) VALUES (?,?)");
	$stmt2 -> bind_param("ii", $code, $upc);
	$code = $couponCode;
	foreach ($upcs as $value)
	{
		$upc = $value;
		$stmt2->execute();
	}
}

function createBSGSCoupon($couponCode, $expiration, $maxQuantity, $qualifyingQuantity, $getQuantity, $pricePercentOfGetItems, $inventoryItems)
{;}
function expireCoupon($couponCode)
{;}
function createInventoryItem($name, $price, $quantity)
{;}
function addInventoryQuantity($UPC, $quantity)
{	
	global $TABLE_Inventory;
	
	$connection = connect();
	
	$stmt = $connection->prepare("UPDATE $TABLE_Inventory SET quantity=? WHERE UPC=?");
	$stmt->bind_param('ii', $quant, $upc);
	$quant = $quantity;
	$upc = $UPC;
	$stmt->execute();
	
}
function setInventoryPrice($UPC, $price)
{	
	global $TABLE_Inventory;
	
	$connection = connect();
	
	$stmt = $connection->prepare("UPDATE $TABLE_Inventory SET price=? WHERE UPC=?");
	$stmt->bind_param('di', $cost, $upc);
	$cost = $price;
	$upc = $UPC;
	$stmt->execute();
}
function createSale($firstNameCustomer, $lastNameCustomer, $phoneNumberCustomer, $emailAddressCustomer, $employeeId, $inventoryItems, $quantities)
{;}
function createEmployee($firstName, $lastName, $username, $password, $pin)
{
	global $TABLE_Employees;
	
	$connection = connect();
	$stmt = $connection->prepare("INSERT INTO $TABLE_Employees (firstName, lastName, username, passHash, pinHash, salt)
	VALUES (?, ?, ?, unhex(?), unhex(?), unhex(?)");
	$stmt->bind_param('ssssss', $first, $last, $user, $pass, $pinNo, $salt);
	$first = $firstName;
	$last = $lastName;
	$user = $userName;
	$salt = bin2hex(random_bytes(32));
	$password = $salt + $password;
	$pass = hash("sha256", $password);
	$pin = $salt + $pin;
	$pinNo = hash("sha256", $pin);
	$stmt->execute();
}
function validateEmployeePassword($username, $password)
{;}
function validateEmployeePin($username, $pin)
{ return true; }
?>