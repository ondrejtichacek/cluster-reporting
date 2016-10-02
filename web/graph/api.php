<?php

//setting header to json
header('Content-Type: application/json');

function getClusters($mysqli) {
	$query = sprintf("SELECT name, display_name FROM cluster");
	$result = $mysqli->query($query);

	$clusters = array();
	foreach ($result as $row) {
		$clusters[] = $row;
	}
	$result->close();

	return $clusters;
}

function returnClusters($clusters, $mysqli) {
	$data = array();
	foreach ($clusters as $cluster) {

		$dataset = array();
	  $dataset['label'] = $cluster['name'];

		$query = sprintf("SELECT q_used, recorded FROM c WHERE system = '%s' ORDER BY recorded", $cluster['name']);
		//echo($query);

		//execute query
		$result = $mysqli->query($query);

		//loop through the returned data
		foreach ($result as $key => $row) {
		  //$dataset['data'][] = ['x' => $row['recorded'], 'y'=> intval($row['q_used'])];
			if ($key > 2) {
				$last_key = key( array_slice( $dataset['data'], -1, 1, TRUE ) );
			}
		 	if ($key > 2 &&
		 			($dataset['data'][$last_key]['y'] == intval($row['q_used']) &&
		 			 $dataset['data'][$last_key -1]['y'] == intval($row['q_used'])
		 		  )) {
			  $dataset['data'][$last_key]['x'] = $row['recorded'];
			} else {
				$dataset['data'][] = ['x' => $row['recorded'],
															'y'=> intval($row['q_used'])];
			}
		}

		$data['datasets'][] = $dataset;

		//free memory associated with result
		$result->close();
	}

	return $data;

}

function returnQueues($clusters, $mysqli) {
	$data = array();

	foreach ($clusters as $cluster) {

		$query = sprintf("SELECT name FROM queue_details WHERE system = '%s'", $cluster['name']);
		$result = $mysqli->query($query);

		$queues = array();
		foreach ($result as $row) {
			$queues[] = $row;
		}
		$result->close();

		foreach ($queues as $queue) {
			$dataset = array();
		  $dataset['label'] = $queue['name'];

			// $query = sprintf("SET @a = 0;
			//   SELECT used_p, recorded
			// 	FROM q
			// 	WHERE system = '%s' AND queue = '%s' AND (@a := @a + 1) % 20 = 0
			// 	ORDER BY recorded", $cluster['name'], $queue['name'] );

			$query = sprintf("SELECT used_p, recorded
				FROM q
				WHERE system = '%s' AND queue = '%s'
				ORDER BY recorded", $cluster['name'], $queue['name'] );

			//execute query
			$result = $mysqli->query($query);

			//loop through the returned data
			foreach ($result as $key => $row) {
				if ($key > 2) {
					$last_key = key( array_slice( $dataset['data'], -1, 1, TRUE ) );
				}
			 	if ($key > 2 &&
			 			($dataset['data'][$last_key]['y'] == doubleval($row['used_p']) &&
			 			 $dataset['data'][$last_key -1]['y'] == doubleval($row['used_p'])
			 		  )) {
				  $dataset['data'][$last_key]['x'] = $row['recorded'];
				} else {
					$dataset['data'][] = ['x' => $row['recorded'],
																'y'=> doubleval($row['used_p'])];
				}
			}

			$data['datasets'][] = $dataset;

			//free memory associated with result
			$result->close();
		}
	}

	return $data;
}

//database
include_once('secret.php');

//get connection
$mysqli = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

if(!$mysqli){
	die("Connection failed: " . $mysqli->error);
}

$fun = htmlspecialchars($_GET['fun']);

switch ($fun) {
	case 'getClusters':
		$data = getClusters($mysqli);
		break;
	case 'getGraphData':

		if (empty($_GET['cluster'])) {
			$clusters = getClusters($mysqli);
		} else {
			$clusters[] = ['name' => htmlspecialchars($_GET['cluster'])];
		}

		switch (htmlspecialchars($_GET['graph'])) {
			case 'clusters':
				$data = returnClusters($clusters, $mysqli);
				break;

			case 'queues':
				$data = returnQueues($clusters, $mysqli);
				break;

			default:
				# code...
				break;
		}

	default:
		# code...
		break;
}


//close connection
$mysqli->close();

//now print the data
print json_encode($data);

?>
