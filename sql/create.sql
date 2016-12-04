CREATE TABLE RWA_Coupons (
    id INT NOT NULL AUTO_INCREMENT,
    couponCode VARCHAR(10) NOT NULL UNIQUE,
    expiration DATETIME NOT NULL,
    maxQuantity INT NOT NULL,
    isPercentDiscount BIT NOT NULL,
    PRIMARY KEY (id)
);
CREATE TABLE RWA_Employees (
    id INT NOT NULL AUTO_INCREMENT,
    firstName VARCHAR(32) NOT NULL,
    lastName VARCHAR(32) NOT NULL,
    username VARCHAR(32) NOT NULL,
    passHash BLOB(256) NOT NULL,
    pinHash BLOB(256) NOT NULL,
    salt BLOB(256) NOT NULL,
    PRIMARY KEY (id)
);
CREATE TABLE RWA_Inventory (
    UPC INT NOT NULL AUTO_INCREMENT,
    name VARCHAR(128) NOT NULL,
    price FLOAT NOT NULL,
    quantity INT NOT NULL,
    PRIMARY KEY (UPC)
);
CREATE TABLE RWA_BuySomeGetSomeCoupons (
    id INT NOT NULL,
    qualifyingQuantity INT NOT NULL,
    getQuantity INT NOT NULL,
    pricePercentOfGetItems INT NOT NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (id) REFERENCES RWA_Coupons(id)
);
CREATE TABLE RWA_CouponApplicableItems (
    couponId INT NOT NULL,
    inventoryUPC INT NOT NULL,
    PRIMARY KEY (couponId, inventoryUPC),
    FOREIGN KEY (couponId) REFERENCES RWA_Coupons(id),
    FOREIGN KEY (inventoryUPC) REFERENCES RWA_Inventory(UPC)
);
CREATE TABLE RWA_Sales (
    id INT NOT NULL AUTO_INCREMENT,
    firstNameCustomer VARCHAR(32),
    lastNameCustomer VARCHAR(32),
    phoneNumberCustomer VARCHAR(10),
    emailAddressCustomer VARCHAR(64),
    totalPrice FLOAT NOT NULL,
    employeeId INT NOT NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (employeeId) REFERENCES RWA_Employees(id)
);
CREATE TABLE RWA_InventorySales (
    quantity INT NOT NULL,
    price FLOAT NOT NULL,
    inventoryUPC INT NOT NULL,
    saleId INT NOT NULL,
    couponId INT,
    PRIMARY KEY (inventoryUPC, saleId),
    FOREIGN KEY (inventoryUPC) REFERENCES RWA_Inventory(UPC),
    FOREIGN KEY (saleId) REFERENCES RWA_Sales(id),
    FOREIGN KEY (couponId) REFERENCES RWA_Coupons(id)
);
CREATE TABLE RWA_PercentDiscountCoupons (
    id INT NOT NULL,
    percentDiscount INT NOT NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (id) REFERENCES RWA_Coupons(id)
);
