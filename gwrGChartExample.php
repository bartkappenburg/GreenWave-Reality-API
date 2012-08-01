<?

include_once('gwrClasses.php');

# fill in your details
$config['username'] = 'email@domain.com'; #login name at energy company
$config['password'] = md5('password'); #md5 hash of your password
$config['endpoint'] = "https://nuon.greenwavereality.com/gwr/gop.php"; #endpoint (change subdomain for other reseller)


# init the class
$gwr = new GWR($config);

$options = array('period' => 'day', "feed" => 'data', "datatype" => 'el');
$data = $gwr->getData("UserGetchart", $options);

$data = json_decode($data, true);



foreach ($data['chart']['data'] as $value) {
	
	$timestamp = date("d-m-Y H:i:s", $value['timestamp']);
	$value = $value['value'];
	
	$result[] = "['$timestamp',$value]";
}

	$result = implode(",\n", $result);



?>

<html>
  <head>
    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">
      google.load("visualization", "1", {packages:["corechart"]});
      google.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = google.visualization.arrayToDataTable([
          ['Date', 'Usage (watts)'],
          <? echo $result; ?>
        ]);

        var options = {
          title: 'Electricity usage',
          curveType: "none",
          vAxis: {minValue: 0}
        };

        var chart = new google.visualization.LineChart(document.getElementById('chart_div'));
        chart.draw(data, options);
      }
    </script>
  </head>
  <body>
    <div id="chart_div" style="width: 900px; height: 500px;"></div>
  </body>
</html>
