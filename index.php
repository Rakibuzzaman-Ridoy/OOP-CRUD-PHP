<?php 
	require_once "crud_oop_class.php";
	$obj = new database();

											//Insert
	// $obj->insert("student",["name"=>"Sakib Al Hasan","age"=>34,"gender"=>"M","course"=>"2","city"=>"2"]);
	//echo "Insert: <br>";
	// echo "<pre>";
	// print_r($obj->getResults());

											//Update
	// $obj->update("student",["name"=>"Mushfiur Rahim","age"=>35,"gender"=>"M","course"=>"4","city"=>"1"],"id=10");
	//echo "Update: <br>";
	// echo "<pre>";
	// print_r($obj->getResults());

											//Delete
	// $obj->delete("student","name='Sakib Al hasan'");
	// echo "Delete: <br>";
	// echo "<pre>";
	// print_r($obj->getResults());

											//Selecting or Read data (RAW QUERY)!
	// $obj->selectQuery("SELECT * FROM student");
	// echo "Selecting data using raw query: ";
	// echo "<pre>";
	// print_r($obj->getResults());


											//Select
	$row   = "student.id,student.name,student.age,student.gender,course.course_name,city.city_name";
	$join  = "course ON student.course = course.course_id JOIN city ON student.city = city.city_id";
	$where = "id <'15'";
	$order = "id desc";
	$limit = 2;
	$obj->select("student",$row,$join,$where,$order,$limit);
	echo "Selecting: ";
	echo "<pre>";
	print_r($obj->getResults());

											//Pagination
	echo $obj->pagination("student",$join,$where,$limit);



?>