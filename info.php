<?php

if (!empty($_GET['country'])) {
    $search_term = $_GET['country'];
} else {
    //$search_term = "United States";
    //$search_term = "Fra";
    $search_term = "France";
}

$search_term = rawurlencode($search_term);

//echo $search_term."<br />";
//error_reporting(E_ALL);


function api_search($search_term, $limit, $headers, $api_url) {
    $search_term = urlencode($search_term);
    $curl = curl_init();

    if (!empty($headers)) {
        $headers = array(
            "Accept: */*",
            "Content-Type: application/x-www-form-urlencoded",
            "User-Agent: runscope/0.1",
            "Authorization: Bearer " . $token);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    }

    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_URL, $api_url);
    $result = curl_exec($curl);

    curl_close($curl);
    $result = json_decode($result, true);
    return $result;
}

//to filter
$fields = "fields=name;alpha2Code;alpha3Code;flag;region;subregion;population;languages";

$api_url_by_name = "https://restcountries.eu/rest/v2/name/{$search_term}?{$fields}";
//echo $api_url_by_name;
$api_url_full_name = "https://restcountries.eu/rest/v2/name/{$search_term}?fullText=true&{$fields}";
//echo $api_url_full_name;
$api_url_code = "https://restcountries.eu/rest/v2/alpha/{$search_term}?{$fields}";
$headers = "";


$results = api_search($country, 50, $headers, $api_url_full_name);
//echo $api_url_full_name."<br />";
if ($results["message"] == "Not Found") {
    $results = api_search($country, 50, $headers, $api_url_by_name);
    //echo $api_url_by_name."<br />";
}

if ($results['message'] == "Not Found") {
    $results = api_search($country, 50, $headers, $api_url_code);
    //echo $api_url_code."<br />";
}



foreach ($results as $country => $item) {
    //echo "country: ".$country."<br />";
    $langs = array();
    foreach ($item['languages'] as $lang => $lang_arr) {
        foreach ($lang_arr as $key => $value) {
            if ($key == "name") {
                //echo "lang name: " . $value . "<br />";
                $langs[] = $value;
            }
        }
    }

    $langs = implode(", ", $langs);
    //echo $langs."<br />";

    $results[$country]["languages"] = $langs;
}

foreach ($results as $key => $row) {
    $population[$key] = $row["population"];
    $names[$key] = substr($row["name"],0,1);
}

//array_multisort($population, SORT_DESC, $names, SORT_ASC, $results);
array_multisort($names, SORT_ASC, $population, SORT_DESC, $results);
//echo "<pre>";
//print_r($cleaned_up_results);
//echo "</pre>";
//$json = json_encode($cleaned_up_results,JSON_UNESCAPED_SLASHES);
//echo $json;

?>
