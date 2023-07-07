<?php
// $ch = curl_init();
// curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
// curl_setopt($ch, CURLOPT_URL, "https://sandesh.com/epaper/ahmedabad");
// curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
// curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US) AppleWebKit/525.13 (KHTML, like Gecko) Chrome/0.A.B.C Safari/525.13");
// $data = curl_exec($ch);
// curl_close($ch);



// file_put_contents(dirname(__FILE__) . "/test.txt",   $data);

$cityarray = array("Bhind", "Bhopal", "Gwalior City", "Indore City", "jabalpur", "Ujjain", "Ajmer City", "Chittorgarh", "Jaipur City", "Jodhpur City", "Kota", "Udaipur City", "Bilaspur", "Raigarh", "Raipur City", "Ahmedabad", "Bangalore", "Chennai", "Coimbatore", "Hubli", "Kolkata", "New Delhi", "Surat", "Uttar Pradesh");

$citycode = array("77", "64", "78", "85", "123", "95", "3", "14", "20", "23", "26", "52", "100", "105", "109", "55", "56", "58", "139", "57", "135", "59", "60", "117");
echo "cityarray count=" . count($cityarray) . "  citycode count=" . count($citycode);
