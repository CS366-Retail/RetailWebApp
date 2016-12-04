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
	$stmt1 = $connection->prepare("INSERT INTO Coupons (couponCode, expiration, maxQuantity, isPercentDiscount)
	VALUES (?, ?, ?, ?)");
	$stmt1->bind_param("ssii", $cc, $exp, $max, $isPercent);
	$cc = $couponCode;
	$exp =$expiration;
	$max = $maxQuantity;
	$isPercent = $percentDiscount;
	$stmt1->execute();
	$stmt2 = $connection -> prepare("INSERT INTO $TABLE_CouponApplicableItems (couponCode, inventoryUPC)
	VALUES (?,?)";
	$stmt2 -> bind_param("ii", $code, $upc);
	$code = $couponCode;
	foreach ($upcs as $value)
	{
		$upc = $value;
		stmt2->execute();
	}
}

function createBSGSCoupon($couponCode, $expiration, $maxQuantity, $qualifyingQuantity, $getQuantity, $pricePercentOfGetItems, $inventoryItems)
    
function expireCoupon($couponCode)
    
function createInventoryItem($name, $price, $quantity)

function addInventoryQuantity($UPC)

function setInventoryPrice($UPC, $price)

function createSale($firstNameCustomer, $lastNameCustomer, $phoneNumberCustomer, $emailAddressCustomer, $employeeId, $inventoryItems, $quantities)

function createEmployee($firstName, $lastName, $username, $password, $pin)

function validateEmployeePassword($username, $password)

function validateEmployeePin($username, $pin)

?>