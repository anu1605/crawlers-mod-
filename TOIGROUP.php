<?php
if ($epapercode == "TOI" or $epapercode == "ET" or $epapercode == "MT" or $epapercode == "Mirror" or $epapercode == "ESM") {

    $dateForLinks = date('d/m/Y', strtotime($filenamedate));
    if ($epapercode == "TOI") {
        $cityarray = array("Ahmedabad", "Bangalore", "Bhopal", "Chandigarh", "Delhi", "Goa", "Hyderabad", "Jaipur",  "Lucknow", "Mumbai"); //"Kochi", "Kolkata", "Chennai"
        $citycode = array("toiac", "toibgc", "toibhoc", "toicgct", "cap", "toigo", "toih", "toijc",  "toilc", "toim"); //"toikrko", "toikc","toich"
    }
    if ($epapercode == "ET") {
        $cityarray = array("Bangalore", "Mumbai", "Delhi"); //, "Kolkata"
        $citycode = array("etbg", "etmc", "etdc"); //, "etkc"
    }
    if ($epapercode == "MT") {
        $cityarray = array("Nagpur", "Mumbai", "Pune", "Sambhaji", "Nashik");
        $citycode = array("mtnag", "mtm", "mtpe", "mtag", "mtnk");
    }
    if ($epapercode == "Mirror") {
        $cityarray = array("Bangalore", "Mumbai", "Pune");
        $citycode = array("vkbgmr", "vkmmir", "pcmir");
    }
    if ($epapercode == "ESM") {
        $cityarray = array("Kolkata");
        $citycode = array("esamk");
    }

    if ($cityarray != null) {

        if ($no_of_editions_to_run > 0 and $no_of_editions_to_run < count($cityarray)) $cityarray = array_slice($cityarray, 0, $no_of_editions_to_run);
    }

    crawltoi($cityarray, $dateForLinks, $epapercode, $citycode, $filenamedate, $eol, $conn, $lang, $cities_of_interest, $epapername);
}
