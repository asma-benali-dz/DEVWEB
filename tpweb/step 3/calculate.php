<?php
2 header('Content-Type: application/json');
3 if(isset($_POST['name'], $_POST['weight'], $_POST['height'])) {
4 $name = htmlspecialchars($_POST['name']);
5 $weight = floatval($_POST['weight']);
6 $height = floatval($_POST['height']);
7 if($weight <= 0 || $height <= 0) {
8 echo json_encode([
9 'success' => false,
10 'message' => 'Invalid input values. Weight and height must be greater thanzero.'
11 ]);
12 exit;
13 }
14 $bmi = $weight / ($height * $height);
15 if($bmi < 18.5) {
16 $interpretation = "Underweight";
17 } elseif($bmi < 25) {
18 $interpretation = "Normal weight";
19 } elseif($bmi < 30) {
20 $interpretation = "Overweight";
21 } else {
22 $interpretation = "Obesity";
23 }
24 $message = "Hello, $name. Your BMI is " . number_format($bmi,2) . " ($interpretation).";
25 echo json_encode([
26 'success' => true,
27 'bmi' => $bmi,
28 'message' => $message
29 ]);
30 exit;
31 }
32 echo json_encode([
33 'success' => false,
34 'message' => 'Data not received.'
35 ]);
36 exit;
37 ?>