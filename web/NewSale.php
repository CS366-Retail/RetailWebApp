<?php include 'static.php'; ?>
<?php $authWith = 'pin'; ?>
<?php include 'authentication.php'; ?>
<html>
  <head>
    <?php printHead(); ?>
    <script>
      var pendingPriceLookup = 0;
      var failedPriceLookup = 0;
      var pendingSubmitSale = false;
      
      var FIELDNAME_QUANTITY = 'quantity';
      var FIELDNAME_ITEMNAME = 'item';
      var FIELDNAME_PRICE = 'price';
      var FIELDNAME_TOTALPRICE = 'totalPrice';
      
      var saleItemCount = 0;
      var saleItems = [];
      
      function initialize() {
        saleItemCount = 0;
        saleItems = [];
        var pendingPriceLookup = 0;
        var failedPriceLookup = 0;
        
        document.forms["employeeEntry"].reset();
        document.forms["customerForm"].reset();
        
        document.getElementById("saleBreakdownTable").innerHTML =
          "<tr><th>Quantity</th><th class='saleItemHead'>Item</th><th>Price</th><th>Total Price</th></tr>";
      }
      function getPrice(upc){
        $.ajax("priceLookup.php", {
          type: "POST",
          data: { 'upc' : upc },
          statusCode: {
            200: function(data) {
              var delimiterPos = data.indexOf(" ");
              
              var price = data.substring(0, delimiterPos);
              var name = data.substring(delimiterPos);
              
              saleItems[upc][FIELDNAME_PRICE] = parseFloat(price);
              saleItems[upc][FIELDNAME_ITEMNAME] = name;
              
              pendingPriceLookup--;
              recalculateField(upc);
            },
            400: function(data) {
              saleItems[upc] = null;
              var upcElement = document.getElementById('upc');
              if (upcElement != null)
                upcElement.outerHTML = "";
              pendingPriceLookup--;
              failedPriceLookup++;
              alert("Error with loading UPC " + upc + "\n-> " + data);
            }
          }
        });
      }
      function addSaleItem(upc){
        if (pendingSubmitSale)
          return;
        if (saleItems[upc] == null)
          appendNewSaleItem(upc);
        else
        {
          saleItems[upc][FIELDNAME_QUANTITY]++;
          saleItems[upc][FIELDNAME_TOTALPRICE] += saleItems[upc][FIELDNAME_PRICE];
          recalculateField(upc);
        }
      }
      function appendNewSaleItem(upc){
        saleItemCount++;
        document.getElementById("saleBreakdownTable").innerHTML +=
          "<tr id='" + upc + "' class='breakdownRow breakdownRow" + (saleItemCount % 2) + "'></tr>";
        saleItems[upc] = new Object();
        
        getPrice(upc);
        
        saleItems[upc][FIELDNAME_QUANTITY] = 1;
        saleItems[upc][FIELDNAME_ITEMNAME] = "Pending...";
        saleItems[upc][FIELDNAME_PRICE] = 0;
        saleItems[upc][FIELDNAME_TOTALPRICE] = 0;
        
        recalculateField(upc);
      }
      function recalculateField(upc) {
        document.getElementById(upc).innerHTML =
          "<td class='" + FIELDNAME_QUANTITY + "'>" + saleItems[upc][FIELDNAME_QUANTITY] + "x</td>" +
          "<td class='" + FIELDNAME_ITEMNAME + "'>" + saleItems[upc][FIELDNAME_ITEMNAME] + "</td>" +
          "<td class='" + FIELDNAME_PRICE + "'>$" + saleItems[upc][FIELDNAME_PRICE].toFixed(2) + "</td>" +
          "<td class='" + FIELDNAME_TOTALPRICE + "'>$" + (saleItems[upc][FIELDNAME_QUANTITY] * saleItems[upc][FIELDNAME_PRICE]).toFixed(2) + "</td>";
          
        var grandTotalPrice = 0;
        for (var key in saleItems)
        {
          if (saleItems.hasOwnProperty(key)) {
            grandTotalPrice += (saleItems[key][FIELDNAME_QUANTITY] * saleItems[key][FIELDNAME_PRICE]);
          }
        }
        document.getElementById("grandTotalPrice").innerHTML = "Total: $" + grandTotalPrice.toFixed(2);
      }
      
      function submitUPC(){
        var form = document.forms["employeeEntry"];
        var upc = form.elements["upc"];
        addSaleItem(upc.value);
      }
      
      coupons = [];
      function submitCoupon(){
        document.forms["employeeEntry"].elements["coupon"].value;
      }

      
      function finishSale(){
        if (pendingSubmitSale)
          return;
        if (pendingPriceLookup > 0)
        {
          return;
        }
        if (failedPriceLookup > 0)
        {}
        
        // get customer data form
        var customerForm = document.forms["customerForm"];
        
        // get customer name data
        var nameCustomer = customerForm.elements["nameCustomer"].value;
        var firstNameCustomer;
        var lastNameCustomer;
        var customerNameDelimPos = nameCustomer.indexOf(" ");
        if (customerNameDelimPos > -1)
        {
          firstNameCustomer = nameCustomer.substring(0, customerNameDelimPos)
          lastNameCustomer = nameCustomer.substring(customerNameDelimPos);
        }
        
        // get customer phone and email
        var phoneNumberCustomer = customerForm.elements["phoneNumberCustomer"].value;
        var emailAddressCustomer = customerForm.elements["emailAddressCustomer"].value;
        
        // build arrays of UPCs and quantities
        var inventoryItems = [];
        var quantities = [];
        for (var key in saleItems)
        {
          if (saleItems.hasOwnProperty(key)) {
            inventoryItems.push(key);
            quantities.push(saleItems[key][FIELDNAME_QUANTITY]);
          }
        }
        // make arrays strings delimited by semicolons (the php page requires this format)
        inventoryItems = inventoryItems.join(";");
        quantities = quantities.join(";");
        
        // get coupons
        var coupons = [];
        // todo collection coupons
        coupons = coupons.join(";");
        
        $.ajax("submitSale.php", {
          type: "POST",
          data: {
            'firstNameCustomer' : firstNameCustomer,
            'lastNameCustomer' : lastNameCustomer,
            'phoneNumberCustomer' : phoneNumberCustomer,
            'emailAddressCustomer' : emailAddressCustomer,
            'inventoryItems' : inventoryItems,
            'quantities' : quantities,
            'coupons' : coupons,
          },
          statusCode: {
            201: function(data) {
              initialize();
              pendingSubmitSale = false;
              alert("Sale complete");
            },
            400: function(data) {
              pendingSubmitSale = false;
              alert("Sale failed");
            }
          }
        });
      }
    </script>
  </head>
  <body onload="initialize()">
    <div class="checkoutcontainer">
      <h1>Retail Web App - Checkout</h1>
      <div style='float:left' id="saleBreakdown">
        <table id="saleBreakdownTable">
        </table>
        <div id="grandTotalPrice">$0.00</div>
      </div>
      <div style='float:right'>
        <div id="employeeEntryContainer">
          <form name="employeeEntry" onsubmit="return false;">
            <table>
              <tr><td>UPC</td><td><input name="upc" type="text"></td><td><button id="addUPCButton" onclick="submitUPC()">Add</button></td></tr>
              <tr><td>Coupon</td><td><input name="coupon" type="text"></td><td><button id="applyCouponButton" onclick="submitCoupon()">Apply</button></td></tr>
            </table>
            <button id="checkOutButton" onclick="finishSale()">Check out</button>
          </form>
        </div>
        <div id="customerFormContainer">
          <form name="customerForm" onsubmit="return false;">
            Customer Name:<br />
            <input name="nameCustomer" type="text"><br />
            Customer Phone Number:<br />
            <input name="phoneNumberCustomer" type="text"><br />
            Customer Email Address:<br />
            <input name="emailAddressCustomer" type="text"><br />
          </form>
        </div>
      </div>
      <?php printFooter(); ?>
    </div>
  </body>
</html>