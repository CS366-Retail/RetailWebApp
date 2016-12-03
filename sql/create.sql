CREATE TABLE BuySomeGetSomeCoupons (
    id INT NOT NULL,
    qualifyingQuantity INT NOT NULL,
    getQuantity INT NOT NULL,
    pricePercentOfGetItems INT NOT NULL,
    PRIMARY KEY (id),
    FOREIGN KEY id REFERENCES (Coupons)id
);
CREATE TABLE CouponApplicableItems (
    couponId INT NOT NULL,
    inventoryUPC INT NOT NULL,
    PRIMARY KEY (couponId, inventoryUPC),
    FOREIGN KEY couponId REFERENCES (Coupons)id,
    FOREIGN KEY inventoryUPC REFERENCES (Inventory)UPC
);
CREATE TABLE Coupons (
    id INT NOT NULL AUTO_INCREMENT,
    couponCode VARCHAR(10) NOT NULL,
    expiration DATETIME NOT NULL,
    maxQuantity INT NOT NULL,
    isPercentDiscount BIT NOT NULL,
    PRIMARY KEY (id)
);
CREATE TABLE Employees (
    id INT NOT NULL AUTO_INCREMENT,
    firstName VARCHAR(32) NOT NULL,
    lastName VARCHAR(32) NOT NULL,
    username VARCHAR(32) NOT NULL,
    passHash BINARY(128) NOT NULL,
    pinHash BINARY(128) NOT NULL,
    salt BINARY(128) NOT NULL
);
CREATE TABLE Inventory (
    UPC INT NOT NULL AUTO_INCREMENT,
);
CREATE TABLE InventorySales (
    quantity INT NOT NULL,
    price FLOAT NOT NULL,
    inventoryUPC INT NOT NULL,
    saleId INT NOT NULL,
    couponId INT,
    FOREIGN KEY inventoryUPC REFERENCES (Inventory)UPC,
    FOREIGN KEY saleId REFERENCES (Sales)id,
    FOREIGN KEY couponId REFERENCES (Coupons)id,
    PRIMARY KEY (inventoryUPC, saleId)
);
CREATE TABLE PercentDiscountCoupons (
    id INT NOT NULL,
    percentDiscount INT NOT NULL,
    PRIMARY KEY (id),
    FOREIGN KEY id REFERENCES (Coupons)id
);
CREATE TABLE Sales (
    id INT NOT NULL AUTO_INCREMENT,
    firstNameCustomer VARCHAR(32),
    lastNameCustomer VARCHAR(32),
    phoneNumberCustomer VARCHAR(10),
    emailAddressCustomer VARCHAR(64),
    totalPrice FLOAT NOT NULL,
    employeeId INT NOT NULL,
    FOREIGN KEY employeeId REFERENCES (Employees)id
);