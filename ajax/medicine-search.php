<?php include __DIR__.'/../bootstrap/init.php';


header("content-type: application/json");
$objMedicine = new Medicine;

// Search
if( isset($_POST['key']) && $_POST['key'] === "search_in_select2") {

  $data = escape_data($_POST['search_key']);
  $result = $objMedicine->search($data);

  $response = mysqli_fetch_all($result, MYSQLI_ASSOC);
  echo json_encode($response);
}