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
    
end

function createBSGSCoupon($couponCode, $expiration, $maxQuantity, $qualifyingQuantity, $getQuantity, $pricePercentOfGetItems, $inventoryItems)
    
end

function expireCoupon($couponCode)
    
end


function createInventoryItem($name, $price, $quantity)

end

function addInventoryQuantity($UPC)

end

function setInventoryPrice($UPC, $price)

end

function createSale($firstNameCustomer, $lastNameCustomer, $phoneNumberCustomer, $emailAddressCustomer, $employeeId, $inventoryItems, $quantities)

end

function createEmployee($firstName, $lastName, $username, $password, $pin)

end

function validateEmployeePassword($username, $password)

end

function validateEmployeePin($username, $pin)

end


?>