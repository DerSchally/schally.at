<?

/*
$devisen = file_get_contents("http://www.finanzen.at/devisen/");
$devisen = explode('<h2>Alle Devisenpaare</h2>',$devisen);
$devisen  = explode('<table class="news_table">',$devisen[1]);


$devisen  = explode("</table>",$devisen[1]);
echo "<table>" . $devisen[0] . "</table>";
*/

$USD = file_get_contents("http://www.finanzen.at/devisen/dollarkurs");
$USD = explode('pricebox">',$USD);
$USD = explode('</table>',$USD[1]);

echo( "<table><tr><td>" . $USD[0]  . "</td></tr>");


$USD = file_get_contents("http://www.finanzen.at/devisen/bitcoin-euro-kurs");
$USD = explode('pricebox">',$USD);
$USD = explode('</table>',$USD[1]);

echo( "<tr><td>" . $USD[0]  . "</td></tr>");

$USD = file_get_contents("http://www.finanzen.at/devisen/bitcoin-cash-euro-kurs");
$USD = explode('pricebox">',$USD);
$USD = explode('</table>',$USD[1]);

echo( "<tr><td>" . $USD[0]  . "</td></tr>");

$USD = file_get_contents("http://www.finanzen.at/devisen/ethereum-euro-kurs");
$USD = explode('pricebox">',$USD);
$USD = explode('</table>',$USD[1]);

echo( "<tr><td>" . $USD[0]  . "</td></tr></table>");




$silber = file_get_contents("http://www.gold.de/kurse/silberpreis/");
$silber = explode('<section class="preistable">',$silber);
$silber = explode('</section>',$silber[1]);
echo $silber[0];


$silber = file_get_contents("http://www.gold.de/kurse/goldpreis/");
$silber = explode('<section class="preistable">',$silber);
$silber = explode('</section>',$silber[1]);
echo $silber[0];

?>