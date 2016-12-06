<?php include 'static.php'; ?>
<?php
  //include 'authentication.php';
  if (auth_getAuthMode() != "pas")
  {
    http_response_code(401);
    echo "You must be logged in to use this action";
    exit(0);
  }
?>
<?php include '../db/dbfunctions.php'; ?>

<?php
if (isset($_POST["id"]) && !empty($_POST["id"]))
  updateEmployee($_POST["firstName"], $_POST["lastName"], $_POST["username"]);
// create new employee
elseif (isset($_POST["username"]) && !empty($_POST["username"]))
  createEmployee($_POST["firstName"], $_POST["lastName"], $_POST["username"], $_POST["password"], $_POST["pin"]);
// return employees
else
{
  $employees = getEmployees();
  $result = [];
  foreach($employees as $employee)
    array_push($result, $employee["id"] . ";" . $employee["firstName"] . ";" . $employee["lastName"] . ";" . $employee["username"]);
    
  echo implode("\n", $result);
}
?>