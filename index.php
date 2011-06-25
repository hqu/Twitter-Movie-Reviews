<?php

$temp_query=$_POST['query'];
if ($temp_query=="") {
	$temp_query="avatar";
}
echo "<html><head><title>Twitter at the Movies</title><link rel='stylesheet' type='text/css' href='mov_css.css' /><script type='text/javascript' src='movies.js'></script><script type='text/javascript'>var _sf_startpt=(new Date()).getTime()</script></head><body bgcolor='#B3ECEF' onload='document.twSearch.query.focus()'>";
echo "<h2 align='center'>Real-Time Movie Reviews <a href='http://www.twitter.com'><img alt='Powered-by-twitter-sig' src='http://search.twitter.com/images/powered-by-twitter-badge.gif?1220915084' border='0' valign='top' vspace='6'></a></h2>";
echo "<div align='center'><form name='twSearch' id='twSearch' action='index.php' method='post'> <input type='text' name='query' id='query' value='$temp_query' size='38'> <input type='submit' name='SubmitForm' value='Find Movie'></form></div>";


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

$string_array = split(" -\ ", $temp_string);
$string_array_final = split("\. ", $string_array[0]);
$final_string = $string_array_final[1];
$pos2 = strpos($final_string, "2009");
if ($pos2 > -1)
{	
	$final_string = substr($final_string, 0, -7);
}
$movies[] = $final_string;
//echo "<br>".$movies[$counter];

}


//$url_netflix = 'http://www.netflix.com/';
//$netflix_raw_rss = curl_rss($url_netflix);
//echo $netflix_raw_rss;
//$temp_desc = split("dB\(this\)", $netflix_raw_rss);
//echo substr($temp_desc[1], 0, 80);

//$temp_desc = split("\<description>|\<\\description>", $movies_raw_rss);


function get_title($string_whole) {
  //echo $string_whole;
  $splitter = "title>";
  $pos = strpos($string_whole, $splitter);
  $temp_string = substr($string_whole, $pos+6, 160);
  $final_string_array = split("\<", $temp_string);
  //echo $final_string_array[0];
  return $final_string_array[0];
}

function get_author($string_whole) {
  //echo $string_whole;
  $splitter = "uri>";
  $pos = strpos($string_whole, $splitter);
  $temp_string = substr($string_whole, $pos+4, 60);
  $final_string_array = split("\<", $temp_string);
  //echo $final_string_array[0];
  return $final_string_array[0];
}

function get_desc($string_whole) {
  //echo $string_whole;
  $splitter = "description>";
  $pos = strpos($string_whole, $splitter);
  $temp_string = substr($string_whole, $pos+4, 60);
  $final_string_array = split("\<", $temp_string);
  //echo $final_string_array[0];
  return $final_string_array[0];
}



  echo "<table align='center'><tr><td colspan='2'>";
  echo "<div align='center' class='searchText'>";
  for ( $i = 1; $i <= 8; $i += 1) {
	$temp_movie = ereg_replace("39", '',$movies[$i]);
	$temp_movie = ereg_replace("\&", '',$temp_movie);
	$temp_movie = ereg_replace("\;", '',$temp_movie);
	$temp_movie = ereg_replace("\#", '',$temp_movie);
	$temp_movie = ereg_replace("amp", ' ',$temp_movie);
	$temp_movie = ereg_replace(" s ", ' ',$temp_movie);
	$temp_movie = ereg_replace("\.\.", '',$temp_movie);
	$temp_movie = ereg_replace("\: ", ' ',$temp_movie);
    echo "<a href='#' onClick='document.getElementById(\"twSearch\").query.value=\"".$temp_movie."\";document.twSearch.submit();'>".$temp_movie."</a> &nbsp; ";
  }
  echo "</div>";
  echo "</td></tr></table>";


if ($temp_query!=""){
$search_term = $temp_query;
  print_movie_block($search_term);
}


else {
    //srand(time());
    //$random = (rand()%7);
    //echo $random;
    $temp_movie_title="avatar";
    print_movie_block($temp_movie_title);
}

function gen_search_string($title_string) {
//  echo $title_string;
  $movie_string = ereg_replace(":", '',$title_string);
//  $movie_string = ereg_replace("..", '',$title_string);
  $search_string = urlencode($movie_string." watched OR saw OR watching");
//  echo $search_string;
  return $search_string;
}

function print_movie_block($movie_title) {
$search_string = gen_search_string($movie_title);
$search_url = "http://search.twitter.com/search.atom?q=".$search_string."&rpp=26";
//echo $search_url;
$temp_twitter_rss = $search_url;
//echo $temp_twitter_rss."<br>";
$twitter_raw_rss = curl_rss($temp_twitter_rss);
//echo $twitter_raw_rss;
//$temp_title = get_title($twitter_raw_rss);
//$temp_author = get_author($twitter_raw_rss);
//echo $temp_title;
$temp_split = split("<title>", $twitter_raw_rss);
$temp_split_author = split("<uri>", $twitter_raw_rss);

$search_string_yt = urlencode($movie_title." trailer");
$search_url_yt = "http://gdata.youtube.com/feeds/base/videos?q=".$search_string_yt;
$temp_yt_rss = $search_url_yt;
//echo $temp_yt_rss."<br>";
$yt_raw_rss = curl_rss($temp_yt_rss);
//echo $yt_raw_rss;

$temp_split_yt = split("i.ytimg.com\/vi\/", $yt_raw_rss);

  $yt_id = substr($temp_split_yt[1], 0, 11);

  echo "<table cellpadding='2' align='center' border='0'>";
  echo "<tr>";
  echo "<td valign='top'><div class='movieHeader'>Tweets about <b>$movie_title</b></div>";
  //echo "<div class='scroll' id='friendDiv' style='display:none'><div class='reviewBlock' style='background-color:#CCC;' id='reviewDiv'><div class='tweetText'> <a href='http://www.twitter.com/Daughterpick'>@Daughterpick</a> Saw the trailer when we took Grace to see Hotel for Dogs. Chow Yun Fat is in it... and he's the Man!</div> <div class='authorText'><a href='http://twitter.com/patman23'>patman23</a></div></div></div>";
  echo "<div class='scroll' id='tweetsDiv' style='display:block'>";

for ( $k = 1; $k <= 26; $k += 1) {
  $final_string_array = split("\<", $temp_split[$k]);
  $final_string_array_author = split("\<", $temp_split_author[$k]);
  $tweets[$i][]= $final_string_array[0];
  $authors[$i][]= $final_string_array_author[0];
}


for ( $h = 1; $h <=6; $h += 1) {
  if ($tweets[$i][$h]=="") {
	echo "";
  }
  else {
    $pos_at = strpos($tweets[$i][$h], "@");
    if ($pos_at>-1) {
        $name_string = split(" ", $tweets[$i][$h]);
        $tweets[$i][$h]="";
            foreach ($name_string as $value) {
              $pos_at_2 = strpos($value, "@");
              if ($pos_at_2>-1) {
                $value_no_a = $movie_string = ereg_replace("@", '',$value);
                $value = "<a href='http://www.twitter.com/".$value_no_a."'>".$value."</a>";
              }
              $tweets[$i][$h] = $tweets[$i][$h]." ".$value;
            }        
    }
  }
  echo "<div class='reviewBlock'><div class='tweetText'>".$tweets[$i][$h]."</div> <div class='authorText'><a href='".$authors[$i][$h-1]."'>".substr($authors[$i][$h-1], 19)."</a></div></div>";
}


$next_block = "";
for ( $h = 7; $h <=25; $h += 1) {
  if ($tweets[$i][$h]=="") {
	echo "";
  }
  else {
    $pos_at = strpos($tweets[$i][$h], "@");
    if ($pos_at>-1) {
        $name_string = split(" ", $tweets[$i][$h]);
        $tweets[$i][$h]="";
            foreach ($name_string as $value) {
              $pos_at_2 = strpos($value, "@");
              if ($pos_at_2>-1) {
                $value_no_a = $movie_string = ereg_replace("@", '',$value);
                $value = "<a href='http://www.twitter.com/".$value_no_a."'>".$value."</a>";
              }
              $tweets[$i][$h] = $tweets[$i][$h]." ".$value;
            }        
    }
  }
  $next_block=$next_block."<div class='reviewBlock'><div class='tweetText' >".$tweets[$i][$h]."</div> <div class='authorText'><a href='".$authors[$i][$h-1]."'>".substr($authors[$i][$h-1], 19)."</a></div></div>";
}


echo "<div><input id='btn1' name='btnl' style='width:100%;' type='button' value='Show 20 more' onclick='document.getElementById(\"btn1\").style.display=\"none\";p=document.getElementById(\"tweets20\");pclone=p.cloneNode(true);document.getElementById(\"tweetsDiv\").appendChild(pclone);p=document.getElementById(\"moreButton\");pclone=p.cloneNode(true);document.getElementByID(\"tweetsDiv\").appendChild(pclone);document.getElementById(\"btn1\").style.display=\"hide\";' /></div>";

echo "</div>";



//echo "<div id='loginDiv' style='display:block' class='questionBlock movieHeader'>Sign in to see what your friends are saying <br>Username <input type='text' value='' name='username'><br>Password <input type='text' value='' name='password'> <input type='button' onClick='document.getElementById(\"loginDiv\").style.display=\"none\";document.getElementById(\"questionDiv\").style.display=\"block\";document.getElementById(\"tweetsDiv\").style.display=\"none\";document.getElementById(\"friendDiv\").style.display=\"block\";document.loginForm.userInput.focus()' name='login' value='Sign In'></div>";


//echo "<div id='questionDiv' class='questionBlock' style='display:none;'><form name='loginForm'><textarea name='userInput' cols='42' rows='2' wrap='soft' value='".$movie_title."'></textarea><br><input type='submit' name='testpost' value='Post to Twitter'></form></div></td>";
echo "<td valign='top'><div class='playerDiv'><object height='344' width='425'><param name='movie' value='http://www.youtube.com/v/".$yt_id."&hl=en&fs=1'></param><param name='allowFullScreen' value='true'></param><param name='allowscriptaccess' value='always'></param><embed src='http://www.youtube.com/v/".$yt_id."&hl=en&fs=1' type='application/x-shockwave-flash' allowscriptaccess='always' allowfullscreen='true' height='344' width='425'></embed></object></div>";
//echo "<div class='questionBlock movieHeader'><b>$movie_title movie trailer</b><span class='tweetText'></span></div>";
echo "</td>"; 
echo "</tr></table>";

$next_block=$next_block."<div id='btn12'><input id='button2' name='btnl2' style='width:100%;' type='button' value='Show 20 more' onclick='p=document.getElementById(\"tweets20\");pclone=p.cloneNode(true);document.getElementById(\"tweetsDiv\").appendChild(pclone);p=document.getElementById(\"moreButton\");pclone=p.cloneNode(true);document.getElementById(\"tweetsDiv\").appendChild(pclone);' />";

echo "<div style='display:none'><div id='tweets20'>".$next_block."</div></div>";

}

echo "<script type='text/javascript'>var _sf_async_config={uid:4049,domain:'hongqu.com'};";
echo "(function(){function loadChartbeat() {window._sf_endpt=(new Date()).getTime();";
echo "var e = document.createElement('script');e.setAttribute('language', 'javascript');e.setAttribute('type', 'text/javascript');";
echo "e.setAttribute('src', (('https:' == document.location.protocol) ? 'https://s3.amazonaws.com/' : 'http://') +'static.chartbeat.com/js/chartbeat.js');";
echo "document.body.appendChild(e);}var oldonload = window.onload;window.onload = (typeof window.onload != 'function') ? loadChartbeat : function() { oldonload(); loadChartbeat(); };})();</script>";

echo "</body></html>";

?>