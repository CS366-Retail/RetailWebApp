<?php

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