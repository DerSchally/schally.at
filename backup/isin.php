<?
error_reporting(0);


$all = explode("," , $_GET[ISIN]);

foreach ($all as $one){

if($one == "BE0003793107"){
	$one = "BE0974293251";
}


	$stro = file_get_contents("http://www.finanzen.at/suchergebnisse?_search=" . $one);
	$str = explode('pricebox">',$stro);
	$str = explode('</th>',$str[1]);
	$str = str_replace("<tr>","",$str[0]);
	$str = str_replace(" ","",$str);
	$str = str_replace("<th>","",$str);
	$str = str_replace("<span>EUR</span>","",$str);
	
	
	$str = trim($str);
	
	echo $one . " \t " . $str . "\n";


}


?>