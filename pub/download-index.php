<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$mysqli = new mysqli("localhost","root","k6Ahzg!Q","plushaddict_stage");

// Check connection
if ($mysqli -> connect_errno) {
 // echo "Failed to connect to MySQL: " . $mysqli -> connect_error;
  exit();
} else {
	//echo "MySQL Connected"; 
}

     $query = "SELECT so.customer_id, so.customer_gender, so.customer_firstname, so.customer_middlename, so.customer_lastname, so.customer_email, so.increment_id, so.created_at, soi.sku, soi.name FROM `sales_order` as so left join sales_order_item as soi on so.entity_id = soi.order_id where soi.sku = 'PA_PUL_FQ'";
     $result = mysqli_query($mysqli,$query);
     $user_arr = array();
     $user_arr[] = array(
     	'Customer ID',
     	'Gender',
     	'First Name',
     	'Middle Name',
     	'Last Name',
     	'Email',
     	'Increment Id',
     	'Created At',
     	'SKU',
     	'Product Name'
     );
     while($row = mysqli_fetch_array($result)){
      $customer_id = $row['customer_id'];
      $customer_gender = $row['customer_gender'];
      $customer_firstname = $row['customer_firstname'];
      $customer_middlename = $row['customer_middlename'];
      $customer_lastname = $row['customer_lastname'];
      $customer_email = $row['customer_email'];
      $increment_id = $row['increment_id'];
      $created_at = $row['created_at'];
      $sku = $row['sku'];
      $name = $row['name'];
      $user_arr[] = array(
      	$customer_id,
      	$customer_gender,
      	$customer_firstname,
      	$customer_middlename,
      	$customer_lastname,
      	$customer_email,
      	$increment_id,
      	$created_at,
      	$sku,
      	$name
      );
    }

$filename = 'PA_PUL_FQ.csv';
$export_data = $user_arr;

// file creation
$file = fopen($filename,"w");

foreach ($export_data as $line){
 fputcsv($file,$line);
}

fclose($file);

// download
header("Content-Description: File Transfer");
header("Content-Disposition: attachment; filename=".$filename);
header("Content-Type: application/csv; "); 

readfile($filename);

// deleting file
unlink($filename);