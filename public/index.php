<?php

require_once '../vendor/autoload.php'; 

use Aws\DynamoDb\DynamoDbClient;

date_default_timezone_set('EST');

// start silex app
$app = new Silex\Application(); 

// register a views folder
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => '../views',
));

// make a route for grabbing temperatures
$app->get('/getTemps/{pastHours}', function($pastHours) use($app) { 

	// instantiate database client
	$client = DynamoDbClient::factory(array(
	    'key'    => 'AKIAJUXU6QG3WKRUMDLQ',
	    'secret' => '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',
	    'region' => 'us-east-1'
	));
    
	$iterator = $client->getIterator('Query', array(
	    'TableName'     => 'flimshaw_data_logging',
	    'KeyConditions' => array(
	    	'type' => array(
	            'AttributeValueList' => array(
	                array('S' => "temp")
	            ),
	            'ComparisonOperator' => 'EQ'
	        ),
	        'timestamp' => array(
	            'AttributeValueList' => array(
	                array('N' => strtotime(-$pastHours . " hours"))
	            ),
	            'ComparisonOperator' => 'GT'
	        )
	    )
	));

	$json_packet = array();

	foreach($iterator as $item) {
		$json_packet[] = (array('timestamp' => date('Y-m-d H:i:s', $item['timestamp']['N']), 'tempF' => $item['tempF']['N'], 'outdoorTemp' => $item['outdoorTemp']['N'] ));
	}

	function cmp($a, $b) {

		return (strtotime($a['timestamp']) < strtotime($b['timestamp'])) ? -1 : 1;
	}

	//uasort($json_packet, 'cmp');

	return json_encode($json_packet);

}); 

// make a route for grabbing temperatures
$app->get('/', function() use($app) { 

	if($_GET['pastHours'] != "") {
		$pastHours = $_GET['pastHours'];
	} else {
		$pastHours = 24;
	}

	return $app['twig']->render('home.twig', array('pastHours' => $pastHours));
}); 

$app->run(); 

?>
