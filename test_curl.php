<?php
//include './classes/wordcloud.class.php';
echo "<html><title>Twitter at the Movies</title> <link rel='stylesheet' href='./css/wordcloud.css' type='text/css'><body onload='document.searchForm.q.focus()'>";

//echo $_POST['query'];

 
//$array_search_words = split_words($search_term);

echo "<h2>Twitter at the Movies</h2>";

echo "<b>Reviews for: </b> <form name='searchForm' target='test_curl.php' method='post'><input type='text' name='query' id='query' method='post'><input type='submit' value='Find Movie'></form>";



$url_box_office_yahoo = 'http://rss.ent.yahoo.com/movies/boxoffice.xml';

function curl_rss($url_string) {

$ch = curl_init();
$timeout = 5; // set to zero for no timeout
curl_setopt ($ch, CURLOPT_URL, $url_string);
curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
$file_contents = curl_exec($ch);
curl_close($ch);

// display file
//echo $file_contents;
return $file_contents;
}


$movies_raw_rss = curl_rss($url_box_office_yahoo);


//echo $movies_raw_rss;

for ( $counter = 0; $counter <= 9; $counter += 1) {

$splitter = "title>".$counter.".";
$pos = strpos($movies_raw_rss, $splitter);
$pos=$pos+2;
$temp_string = substr($movies_raw_rss, $pos, 80);

$string_array = split("-", $temp_string);
$string_array_final = split("\. ", $string_array[0]);
$final_string = $string_array_final[1];
$pos2 = strpos($final_string, "2009");
if ($pos2 > -1)
{	
	$final_string = substr($final_string, 0, -8);
}

$movies[] = $final_string;
//echo "<br>".$movies[$counter];

}




function get_title($string_whole) {
  //echo $string_whole;
  $splitter = "title>";
  $pos = strpos($string_whole, $splitter);
  $temp_string = substr($string_whole, $pos+6, 160);
  $final_string_array = split("\<", $temp_string);
  //echo $final_string_array[0];
  return $final_string_array[0];
}

function split_words($entire_string) {
  $words_array = split("\ ", $entire_string);
  return $words_array;
}


if ($_POST['query']!=""){


$search_term = $_POST['query'];
echo "What are people saying about <b>$search_term</b>?<br><br>";
//echo "<br>".$movies[$i];
//echo urlencode($movies[$i]);
$search_url = urlencode($search_term)."+saw";
//$temp_twitter_rss = "http://search.twitter.com/search.atom?q=".$search_url;
$search_string = urlencode($search_term)."++saw";
$search_url = "http://search.twitter.com/search.atom?q=".$search_string;
//echo $search_url;

$temp_twitter_rss = $search_url;
//echo $temp_twitter_rss."<br>";
$twitter_raw_rss = curl_rss($temp_twitter_rss);
//echo $twitter_raw_rss;



$temp_title = get_title($twitter_raw_rss);
//echo $temp_title;
$temp_split = split("<title>", $twitter_raw_rss);


for ( $k = 1; $k <= 9; $k += 1) {
$final_string_array = split("\<", $temp_split[$k]);
$tweets[$i][]= $final_string_array[0];
}


for ( $h = 1; $h <=8; $h += 1) {
  echo "<li>".$tweets[$i][$h]."<br>";
  //split_words($tweets[$h]);
  //foreach ($array_search_words as $value) {
      //$stripped_tweet = ereg_replace($value, "", $stripped_tweet);
  //}
  $stripped_tweet = ereg_replace(" in |saw |Saw |I |i |you |and |a |with |to |is | movie| not| as| it|it |Just | just|just| was| my ", " ", $stripped_tweet);
  $stripped_tweet = preg_replace('/[^a-zA-Z0-9-\s]/', ' ', $stripped_tweet); 
  $stripped_tweet = ereg_replace(" {2,}", ' ',$stripped_tweet);
  $stripped_tweet = ereg_replace("the ", ' ',$stripped_tweet);
  $stripped_tweet = ereg_replace("saw ", ' ',$stripped_tweet);
  $stripped_tweet = ereg_replace("Saw ", ' ',$stripped_tweet);
  //echo  $stripped_tweet;
  $string_for_cloud=$string_for_cloud." ".$stripped_tweet;
  

}
echo "<p><hr>";



}






else {
echo "<h3>Top 10 movies this week</h3>";
for ( $i = 1; $i <= 11; $i += 1) {
echo "What are people saying about <b>$movies[$i]</b>?<br><br>";
//echo "<br>".$movies[$i];
//echo urlencode($movies[$i]);
$search_url = urlencode($movies[$i])."saw";
//$temp_twitter_rss = "http://search.twitter.com/search.atom?q=".$search_url;
$search_string = urlencode($movies[$i])."+saw";
$search_url = "http://search.twitter.com/search.atom?q=".$search_string;
//echo $search_url;

$temp_twitter_rss = $search_url;
//echo $temp_twitter_rss."<br>";
$twitter_raw_rss = curl_rss($temp_twitter_rss);
//echo $twitter_raw_rss;



$temp_title = get_title($twitter_raw_rss);
//echo $temp_title;
$temp_split = split("<title>", $twitter_raw_rss);


for ( $k = 1; $k <= 9; $k += 1) {
$final_string_array = split("\<", $temp_split[$k]);
$tweets[$i][]= $final_string_array[0];
}


for ( $h = 1; $h <=8; $h += 1) {
  echo "<li>".$tweets[$i][$h]."<br>";
  //split_words($tweets[$h]);
  //foreach ($array_search_words as $value) {
      //$stripped_tweet = ereg_replace($value, "", $stripped_tweet);
  //}
  $stripped_tweet = ereg_replace(" in |saw |Saw |I |i |you |and |a |with |to |is | movie| not| as| it|it |Just | just|just| was| my ", " ", $stripped_tweet);
  $stripped_tweet = preg_replace('/[^a-zA-Z0-9-\s]/', ' ', $stripped_tweet); 
  $stripped_tweet = ereg_replace(" {2,}", ' ',$stripped_tweet);
  $stripped_tweet = ereg_replace("the ", ' ',$stripped_tweet);
  $stripped_tweet = ereg_replace("saw ", ' ',$stripped_tweet);
  $stripped_tweet = ereg_replace("Saw ", ' ',$stripped_tweet);
  //echo  $stripped_tweet;
  $string_for_cloud=$string_for_cloud." ".$stripped_tweet;
  

}

echo "<p><hr>";
}


}



//$cloud = new wordCloud();
//echo $string_for_cloud;
//$cloud->addString($string_for_cloud);
//echo $cloud->showCloud();






echo "</body></html>";

?>