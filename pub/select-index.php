<?php
$mysqli = new mysqli("localhost","root","k6Ahzg!Q","plushaddict_stage");

// Check connection
if ($mysqli -> connect_errno) {
  echo "Failed to connect to MySQL: " . $mysqli -> connect_error;
  exit();
} else {
	echo "MySQL Connected"; 
}

$result = $mysqli->query("SELECT * FROM `catalog_product_entity_varchar` WHERE `attribute_id` IN (97,98)");

$productArray = array();
while($row = mysqli_fetch_assoc($result)) {
	if( $row['store_id'] == 0){
		if($row['attribute_id'] == 97){
			$productArray[$row['entity_id']][97] = $row['value'];
		}elseif($row['attribute_id'] == 98){
			$productArray[$row['entity_id']][98] = $row['value'];
		}
	}
}
echo '<pre>';
print_r(count($productArray));
echo '</pre>';
$productArray2 = array();
foreach ($productArray as $key => $product) {
	if(isset($product[98]) && $product[97] != $product[98] && $product[97] != ''){
		$productArray2[$key][97] = $product[97];
		$productArray2[$key][98] = $product[98];
	}
}
echo '<br>';
echo count($productArray2);
echo '<br>';
/*
foreach ($productArray2 as $key => $product) {
	$sql = "UPDATE `catalog_product_entity_varchar` SET `value`='".$product[97]."' WHERE `entity_id` = ".$key." AND `attribute_id` = 98";
	if ($mysqli->query($sql) === TRUE) {
	    echo "Record updated: ".$key."<br>";
	} else {
	    echo "Error updating record: ".$mysqli->error."<br>";
	}
}
*/

	echo '<pre>';
	print_r($productArray2);
	echo '</pre>';
?>
