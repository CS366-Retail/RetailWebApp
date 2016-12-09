<?php include 'static.php'; ?>
<?php $authWith = "pas"; ?>
<?php include 'authentication.php'; ?>
<html>
  <head>
    <?php printHead(); ?>
    <link rel='stylesheet' type='text/css' href='../style/management.css'>
    <script>
      $( function() { $( "#tabs" ).tabs(); } );
      
      var inventory = [];
      function recalculateInventoryTable(){
        var div = document.getElementById("manageInventory-items_table");
        var content = "<tr><th>Name</th><th>Quantity</th><th>Price</th><th>Save</th></tr>";
        
        for (var key in inventory)
        { if (inventory.hasOwnProperty(key)) {
          content +=
            "<tr id='upc" + key + "'>" +
              "<td><input id='itemName" + key + "' type='text' value='" + inventory[key]["itemName"] + "' /></td>" + 
              "<td><input id='quantity" + key + "' type='text' value='" + inventory[key]["quantity"] + "' /></td>" +
              "<td><input id='price" + key + "' type='text' value='" + parseFloat(inventory[key]["price"]).toFixed(2) + "' /></td>" +
              "<td><button id='saveItem' onclick='saveItem(" + key + ")'>Save</button></td>" +
            "</tr>";
        } }
        
        div.innerHTML = content;
      }
      function populateInventory(){
        $.ajax("listItems.php", {
          type: "POST",
          statusCode: {
            200: function(data) {
              var lines = data.trim().split("\n");
              lines.forEach(function(line) {
                  var obj = line.split(";");
                  var upc = obj[0];
                  inventory[upc] = [];
                  inventory[upc]["itemName"] = obj[1];
                  inventory[upc]["quantity"] = obj[2];
                  inventory[upc]["price"] = obj[3];
              });
              recalculateInventoryTable();
            },
            401: function(data) {
              alert("Error: 401");
            }
          }
        });
        
        $.ajax("totalInventory.php", {
          type: "POST",
          statusCode: {
            200: function(data) {
              var count = parseInt(data.trim());
              document.getElementById("manageInventory-itemcount").innerHTML = "Total inventory: " + count;
            },
            401: function(data) {
              alert("Error: 401");
            }
          }
        });
      }
      function addItem(){
        var itemName = document.getElementById("manageInventory-addItem_Name").value;
        
        $.ajax("listItems.php", {
          type: "POST",
          data: { 'name': itemName },
          statusCode: {
            200: function(data) {
              populateInventory();
            },
            400: function(data) {
              alert("Error: 400");
            }
          }
        });
      }
      function saveItem(upc){
        var name = document.getElementById("itemName" + upc).value;
        var changeInQuantity = parseInt(document.getElementById("quantity" + upc).value) - parseInt(inventory[upc]["quantity"]);
        var price = parseInt(document.getElementById("price" + upc).value);
        
        console.log()
        
        $.ajax("listItems.php", {
          type: "POST",
          data: { 'upc': upc, 'name': name, 'changeInQuantity': changeInQuantity, 'price': price},
          statusCode: {
            400: function(data) {
              alert("Error: Could not update " + inventory[key]["itemName"]);
            }
          }
        });
      }
      
      
      
      var employees = [];
      function recalculateEmployeeTable(){
        var div = document.getElementById("manageEmployees-table");
        var content = "<tr><th>First Name</th><th>Last Name</th><th>Username</th><th>Save</th></tr>";
        
        for (var key in employees)
        { if (employees.hasOwnProperty(key)) {
          content +=
            "<tr id='emp" + key + "'>" +
              "<td><input id='emp" + key + "_firstName' type='text' value='" + employees[key]["firstName"] + "' /></td>" + 
              "<td><input id='emp" + key + "_lastName' type='text' value='" + employees[key]["lastName"] + "' /></td>" +
              "<td><input id='emp" + key + "_username' type='text' value='" + employees[key]["username"] + "' /></td>" +
              "<td><button id='saveEmployee' onclick='saveEmployee(" + key + ")'>Save</button></td>" +
            "</tr>";
        } }
        
        div.innerHTML = content;
      }
      function populateEmployees(){
        $.ajax("employees.php", {
          type: "POST",
          statusCode: {
            200: function(data) {
              var lines = data.trim().split("\n");
              lines.forEach(function(line) {
                  var obj = line.split(";");
                  var id = obj[0];
                  employees[id] = [];
                  employees[id]["firstName"] = obj[1];
                  employees[id]["lastName"] = obj[2];
                  employees[id]["username"] = obj[3];
              });
              recalculateEmployeeTable();
            },
            400: function(data) {
              alert("Error fetching employees");
            }
          }
        });
      }
      function saveEmployee(id){
        var firstName = document.getElementById("emp" + id + "_firstName").value;
        var lastName = document.getElementById("emp" + id + "_lastName").value;
        var username = document.getElementById("emp" + id + "_username").value;
               
        $.ajax("employees.php", {
          type: "POST",
          data: { 'id': id, 'firstName': firstName, 'lastName': lastName, 'username': username},
          statusCode: {
            400: function(data) {
              alert("Error: Could not update " + inventory[key]["itemName"]);
            }
          }
        });
      }
      function addEmployee() {
        var firstName = document.getElementById("manageEmployees-addEmployee_firstName").value;
        var lastName = document.getElementById("manageEmployees-addEmployee_lastName").value;
        var username = document.getElementById("manageEmployees-addEmployee_username").value;
        var password = document.getElementById("manageEmployees-addEmployee_password").value;
        var pin = document.getElementById("manageEmployees-addEmployee_pin").value;
        
        $.ajax("employees.php", {
          type: "POST",
          data: {
            'firstName': firstName,
            'lastName': lastName,
            'username': username,
            'password': password,
            'pin': pin,
          },
          statusCode: {
            200: function(data) {
              populateEmployees();
            },
            400: function(data) {
              alert("Error creating employee");
            }
          }
        });
      }
      
      var sales = [];
      function recalculateSalesTable(){
        var div = document.getElementById("manageSales-table");
        var content = "<tr><th>First Name</th><th>Last Name</th><th>Phone Number</th><th>Email</th><th>Total Price</th><th>Employee</th></tr>";
        
        for (var key in sales)
        { if (sales.hasOwnProperty(key)) {
          content +=
            "<tr id='emp" + key + "'>" +
              "<td><input id='sale" + key + "_firstNameCustomer' type='text' value='" + sales[key]["firstNameCustomer"] + "' /></td>" + 
              "<td><input id='sale" + key + "_lastNameCustomer' type='text' value='" + sales[key]["lastNameCustomer"] + "' /></td>" + 
              "<td><input id='sale" + key + "_phoneNumberCustomer' type='text' value='" + sales[key]["phoneNumberCustomer"] + "' /></td>" + 
              "<td><input id='sale" + key + "_emailAddressCustomer' type='text' value='" + sales[key]["emailAddressCustomer"] + "' /></td>" + 
              "<td><input id='sale" + key + "_totalPrice' type='text' value='" + parseFloat(sales[key]["totalPrice"]).toFixed(2) + "' /></td>" + 
              "<td><input id='sale" + key + "_employeeUsername' type='text' value='" + sales[key]["employeeUsername"] + "' /></td>" + 
            "</tr>";
        } }
        
        div.innerHTML = content;
      }
      function populateSales(){
        $.ajax("sales.php", {
          type: "POST",
          statusCode: {
            200: function(data) {
              var lines = data.trim().split("\n");
              lines.forEach(function(line) {
                  var obj = line.split(";");
                  var id = obj[0];
                  sales[id] = [];
                  sales[id]["firstNameCustomer"] = obj[1];
                  sales[id]["lastNameCustomer"] = obj[2];
                  sales[id]["phoneNumberCustomer"] = obj[3];
                  sales[id]["emailAddressCustomer"] = obj[4];
                  sales[id]["totalPrice"] = obj[5];
                  sales[id]["employeeUsername"] = obj[6];
              });
              recalculateSalesTable();
            },
            400: function(data) {
              alert("Error fetching sales");
            }
          }
        });
      }

      
    </script>
  </head>
  <body onload="populateInventory();populateEmployees();populateSales()">
    <div>
      <div id="tabs">
        <ul>
          <li><a href="#tabs-manageInventory">Inventory</a></li>
          <!--<li><a href="#tabs-manageCoupons">Coupons</a></li>-->
          <li><a href="#tabs-manageEmployees">Employees</a></li>
          <li><a href="#tabs-manageSales">Sales</a></li>
        </ul>
        <div id="tabs-manageInventory">
          <div id="manageInventory-items">
            <table id="manageInventory-items_table">
              
            </table>
            <div id="manageInventory-itemcount"></div>
          </div>
          <div id="manageInventory-addItem">
            <input id="manageInventory-addItem_Name" type="text" />
            <button onclick="addItem()">+</button>
          </div>
        </div>
        <!--<div id="tabs-manageCoupons">Not Implemented</div>-->
        <div id="tabs-manageEmployees">
          <div id="manageEmployees">
            <table id="manageEmployees-table"></table>
          </div>
          <div id="manageEmployees-addEmployee">
            <table>
              <tr><td>First Name</td></tr>
              <tr><td><input id="manageEmployees-addEmployee_firstName" type="text" /></td></tr>
              <tr><td>Last Name</td></tr>
              <tr><td><input id="manageEmployees-addEmployee_lastName" type="text" /></td></tr>
              <tr><td>Username</td></tr>
              <tr><td><input id="manageEmployees-addEmployee_username" type="text" /></td></tr>
              <tr><td>Password</td></tr>
              <tr><td><input id="manageEmployees-addEmployee_password" type="password" /></td></tr>
              <tr><td>Pin</td></tr>
              <tr><td><input id="manageEmployees-addEmployee_pin" type="password" /></td></tr>
            </table>
            <button onclick="addEmployee()">Create Employee</button>
          </div>
          <div class="clear"></div>
        </div>
        <div id="tabs-manageSales">
          <table id="manageSales-table"></table>
        </div>
      </div>
      <?php printFooter(); ?>
    </div>
  </body>
</html>