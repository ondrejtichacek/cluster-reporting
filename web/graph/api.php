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

function returnFilesystem($param, $mysqli) {
	$data = array();

	$allowed_param_occupancy = ['avail', 'used'];

	foreach ($param['clusters'] as $key => $cluster) {

		$dataset = array();
		$dataset['label'] = $cluster['name'];
		$dataset['cluster'] = $cluster['name'];
		$dataset['i'] = 0;
		$dataset['len'] = 1;

		if (in_array($param['occupancy'], $allowed_param_occupancy)) {
			$occupancyval = $param['occupancy'];
		}

		switch ($param['valuetype']) {
			case 'RelativeValues':
				$occupancyval .= '/(used+avail)';
				$param['scalefactor'] = 1;
				break;
			case 'AbsoluteValues':
				break;
		}

		$query = sprintf(
			"SELECT %s AS occupancy, recorded
				FROM fs
				WHERE system = '%s'
				ORDER BY recorded",
					$occupancyval,
					$cluster['name']);

		//execute query
		$result = $mysqli->query($query);

		//loop through the returned data
		foreach ($result as $key => $row) {
			if ($key > 2) {
				$last_key = key( array_slice( $dataset['data'], -1, 1, TRUE ) );
			}

			$y = doubleval($row['occupancy'])*$param['scalefactor'];

			if ($key > 2 &&
					(($dataset['data'][$last_key]['y'] - $y)/$y < $param['precision'] &&
					 ($dataset['data'][$last_key -1]['y'] - $y)/$y < $param['precision']
				)) {
				$dataset['data'][$last_key]['x'] = $row['recorded'];
			} else {
				$dataset['data'][] = [
					'x' => $row['recorded'],
					'y' => $y
				];
			}
		}

		$data['datasets'][] = $dataset;

		//free memory associated with result
		$result->close();
	}

	return $data;

}

function returnClusters($param, $mysqli) {
	$data = array();

	$allowed_param_occupancy = ['avail', 'used', 'res', 'total', 'aoacds', 'cdsue'];

	foreach ($param['clusters'] as $key => $cluster) {

		$dataset = array();
		$dataset['label'] = $cluster['name'];
		$dataset['cluster'] = $cluster['name'];
		$dataset['i'] = 0;
		$dataset['len'] = 1;

		if (in_array($param['occupancy'], $allowed_param_occupancy)) {
				$occupancyval = 'q_' . $param['occupancy'];
		}

		switch ($param['valuetype']) {
			case 'RelativeValues':
				$occupancyval .= '/q_total';
				break;
			case 'AbsoluteValues':
				break;
		}

		$query = sprintf(
			"SELECT %s AS occupancy, recorded
				FROM c_recent
				WHERE system = '%s'
				ORDER BY recorded",
					$occupancyval,
					$cluster['name']);

		//execute query
		$result = $mysqli->query($query);

		//loop through the returned data
		foreach ($result as $key => $row) {
			if ($key > 2) {
				$last_key = key( array_slice( $dataset['data'], -1, 1, TRUE ) );
			}
			if ($key > 2 &&
					($dataset['data'][$last_key]['y'] == doubleval($row['occupancy']) &&
					 $dataset['data'][$last_key -1]['y'] == doubleval($row['occupancy'])
				)) {
				$dataset['data'][$last_key]['x'] = $row['recorded'];
			} else {
				$dataset['data'][] = [
					'x' => $row['recorded'],
					'y' => doubleval($row['occupancy'])];
			}
		}

		$data['datasets'][] = $dataset;

		//free memory associated with result
		$result->close();
	}

	return $data;

}

function returnNodeDetails($param, $mysqli) {
	$data = array();

	$allowed_param_occupancy = ['avail', 'used', 'res', 'total', 'aoacds', 'cdsue'];

	foreach ($param['clusters'] as $key => $cluster) {

		$dataset = array();
		$dataset['label'] = $cluster['name'];
		$dataset['cluster'] = $cluster['name'];
		$dataset['i'] = 0;
		$dataset['len'] = 1;

		if (in_array($param['occupancy'], $allowed_param_occupancy)) {
				$occupancyval = 'q_' . $param['occupancy'];
		}

		switch ($param['valuetype']) {
			case 'RelativeValues':
				$occupancyval .= '/q_total';
				break;
			case 'AbsoluteValues':
				break;
		}

		$query = sprintf(
			"SELECT num_proc, mem_total, swap_total, COUNT(*) as count
				FROM node_details
				WHERE system = '%s'
				GROUP BY num_proc, mem_total, swap_total",
					$cluster['name']);

		//execute query
		$result = $mysqli->query($query);

		//loop through the returned data
		foreach ($result as $key => $row) {
			$dataset['data'][] = doubleval($row['count']);
			$data['labels'][] = '<strong>cores</strong>: '. $row['num_proc'] . '<br>' .
								'<strong>mem</strong>: '. $row['mem_total'] . '<br>' .
								'<strong>swap</strong>: '. $row['swap_total'] . '<br>' .
								'<strong>count</strong>';
		}

		$data['datasets'][] = $dataset;

		//free memory associated with result
		$result->close();
	}

	return $data;

}

function returnQueues($param, $mysqli) {
	$data = array();

	$allowed_param_occupancy = ['avail', 'used', 'res', 'total', 'aoacds', 'cdsue'];

	foreach ($param['clusters'] as $cluster) {

		$query = sprintf("SELECT name FROM queue_details WHERE system = '%s'", $cluster['name']);
		$result = $mysqli->query($query);

		$queues = array();
		foreach ($result as $row) {
			$queues[] = $row;
		}
		$result->close();

		foreach ($queues as $qkey => $queue) {
			$dataset = array();
			$dataset['cluster'] = $cluster['name'];
			$dataset['i'] = $qkey;
			$dataset['len'] = count($queues);
			$dataset['label'] = $queue['name'];

			// $query = sprintf("SET @a = 0;
			//	 SELECT used_p, recorded
			//	FROM q
			//	WHERE system = '%s' AND queue = '%s' AND (@a := @a + 1) % 20 = 0
			//	ORDER BY recorded", $cluster['name'], $queue['name'] );

			if (in_array($param['occupancy'], $allowed_param_occupancy)) {
					$occupancyval = $param['occupancy'];
			}

			switch ($param['valuetype']) {
				case 'RelativeValues':
					$occupancyval .= '/total';
					break;
				case 'AbsoluteValues':
					break;
			}

			$query = sprintf(
				"SELECT %s AS occupancy, recorded
					FROM q_recent
					WHERE system = '%s' AND queue = '%s'
					ORDER BY recorded",
						$occupancyval,
						$cluster['name'],
						$queue['name'] );

			//execute query
			$result = $mysqli->query($query);

			//loop through the returned data
			foreach ($result as $key => $row) {
				if ($key > 2) {
					$last_key = key( array_slice( $dataset['data'], -1, 1, TRUE ) );
				}
				if ($key > 2 &&
						($dataset['data'][$last_key]['y'] == doubleval($row['occupancy']) &&
						 $dataset['data'][$last_key -1]['y'] == doubleval($row['occupancy'])
						)) {
					$dataset['data'][$last_key]['x'] = $row['recorded'];
				} else {
					$dataset['data'][] = [
						'x' => $row['recorded'],
						'y'=> doubleval($row['occupancy'])];
				}
			}

			$data['datasets'][] = $dataset;

			//free memory associated with result
			$result->close();
		}
	}

	return $data;
}

function returnClustersWeekdayOccupancy($param, $mysqli) {
	$data = array();

	$allowed_param_occupancy = ['avail', 'used', 'res', 'total', 'aoacds', 'cdsue'];

	foreach ($param['clusters'] as $key => $cluster) {

		if (in_array($param['occupancy'], $allowed_param_occupancy)) {
				$occupancyval = $param['occupancy'];
		}

		switch ($param['valuetype']) {
			case 'RelativeValues':
				$occupancyval .= '/total';
				break;
			case 'AbsoluteValues':
				break;
		}

		$dataset = array();
		$dataset['label'] = $cluster['name'];
		$dataset['cluster'] = $cluster['name'];
		$dataset['i'] = 0;
		$dataset['len'] = 1;

		$query = sprintf(
			"SELECT %s as occupancy, weekday
				FROM c_occupancy_weekdays
				WHERE system = '%s'
				ORDER BY wdno",
					$occupancyval,
					$cluster['name']);
		//echo($query);

		//execute query
		$result = $mysqli->query($query);

		//loop through the returned data
		foreach ($result as $key => $row) {
			$dataset['data'][] = doubleval($row['occupancy']);
		}

		$data['datasets'][] = $dataset;

		//free memory associated with result
		$result->close();
	}

	$data['labels'] = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];

	return $data;

}

function returnClustersHoursOccupancy($param, $mysqli) {
	$data = array();

	$allowed_param_occupancy = ['avail', 'used', 'res', 'total', 'aoacds', 'cdsue'];

	foreach ($param['clusters'] as $key => $cluster) {

		if (in_array($param['occupancy'], $allowed_param_occupancy)) {
				$occupancyval = $param['occupancy'];
		}

		switch ($param['valuetype']) {
			case 'RelativeValues':
				$occupancyval .= '/total';
				break;
			case 'AbsoluteValues':
				break;
		}

		$dataset = array();
		$dataset['label'] = $cluster['name'];
		$dataset['cluster'] = $cluster['name'];
		$dataset['i'] = 0;
		$dataset['len'] = 1;

		$query = sprintf(
			"SELECT %s as occupancy, hour
				FROM c_occupancy_hours
				WHERE system = '%s'
				ORDER BY hour",
					$occupancyval,
					$cluster['name']);
		//echo($query);

		//execute query
		$result = $mysqli->query($query);

		//loop through the returned data
		foreach ($result as $key => $row) {
			$dataset['data'][] = doubleval($row['occupancy']);
		}

		$data['datasets'][] = $dataset;

		//free memory associated with result
		$result->close();
	}

	//$data['labels'] = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
	$data['labels'] = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23'];

	return $data;

}


//database
include_once('secret.php');

//get connection
$mysqli = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

if(!$mysqli){
	die("Connection failed: " . $mysqli->error);
}


$param = $_GET;


switch ($param['fun']) {
	case 'getClusters':
		$data = getClusters($mysqli);
		break;

	case 'getVariables':
		switch ($param['graph']) {
			case 'ClustersOccupancy':
			case 'QueuesOccupancy':
			case 'ClustersWeekdayOccupancy':
			case 'ClustersHoursOccupancy':
				$allowed_values = ['avail', 'used', 'res', 'total', 'aoacds', 'cdsue'];
				$allowed_names = ['Available', 'Used', 'Reserved', 'Total', 'aoACDS', 'cdsuE'];
				break;
			case 'FilesystemOccupancy':
				$allowed_values = ['avail', 'used'];
				$allowed_names = ['Available', 'Used'];
				break;
		}

		$data = array_combine($allowed_values, $allowed_names);

		break;

	case 'getGraphData':

		if (empty($param['clusters']) || empty($param['clusters'][0]['name']) ) {
			$param['clusters'] = getClusters($mysqli);
		}

		switch ($param['graph']) {
			case 'FilesystemOccupancy':
				$data = returnFilesystem($param, $mysqli);
				break;

			case 'ClustersOccupancy':
				$data = returnClusters($param, $mysqli);
				break;

			case 'QueuesOccupancy':
				$data = returnQueues($param, $mysqli);
				break;

			case 'ClustersWeekdayOccupancy':
				$data = returnClustersWeekdayOccupancy($param, $mysqli);
				break;

			case 'ClustersHoursOccupancy':
				$data = returnClustersHoursOccupancy($param, $mysqli);
				break;

			case 'NodeDetails':
				$data = returnNodeDetails($param, $mysqli);
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
