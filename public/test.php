<?php

require ('../vendor/autoload.php');

use Aws\DynamoDb\DynamoDbClient;

date_default_timezone_set('UTC');

$client = DynamoDbClient::factory(array(
    'key'    => 'AKIAJUXU6QG3WKRUMDLQ',
    'secret' => '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',
    'region' => 'us-east-1'
));

$iterator = $client->getIterator('Query', array(
    'TableName'     => 'flimshaw_data_logging',
    'HashKeyValue'  => array( 'S' => 'temp'),
    'KeyConditions' => array(
        'timestamp' => array(
            'AttributeValueList' => array(
                array('N' => strtotime("-15 minutes"))
            ),
            'ComparisonOperator' => 'GT'
        )
    )
));

$json_packet = array();

foreach($iterator as $item) {
	$json_packet[] = (array('timestamp' => date('Y-m-d h:i:s', $item['timestamp']['N']), 'tempF' => $item['tempF']['N'] ));
}

echo json_encode($json_packet);

?>