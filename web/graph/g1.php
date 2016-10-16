<!DOCTYPE html>
<html>
	<head>
		<?php include("header.php"); ?>
	</head>
	<body>
		<div style="width: 100%;">
			<div class="flexcontainer">
				<div class="chart-container" style="width: 100%; height: auto; border-width: 0;" id="chart_1">
					<form class="form-inline form-inline-force">
						<div class="form-group">
							<select class="form-control" name="graph">
								<option value="ClustersOccupancy">Clusters</option>
								<option value="ClustersWeekdayOccupancy">Clusters Weekdays</option>
								<option value="ClustersHoursOccupancy">Clusters Hours</option>
								<option value="FilesystemOccupancy">Filesystem</option>
								<option value="QueuesOccupancy">Queues</option>
								<option value="NodeDetails">Nodes</option>
							</select>
						</div>
						<div class="form-group">
							<select class="form-control variable-selector" name="occupancy">
								<option value="avail">Available</option>
							</select>
						</div>
						<div class="form-group">
							<label class="radio-inline">
								<input type="radio" name="valuetype" value="AbsoluteValues"> Absolute
							</label>
							<label class="radio-inline">
								<input type="radio" name="valuetype" value="RelativeValues" checked> Relative
							</label>
						</div>
						<div class="form-group">
							<label class="checkbox-inline">
								<input type="checkbox" name="datapoints" class="datapoint-checkbox"> Show data points
							</label>
						</div>
						<div class="form-group">
							<button type="button" name="resetzoom" class="btn btn-default">Reset Zoom</button>
						</div>
					</form>
					<canvas id="canvas_1"></canvas>
				</div>
			</div>
		</div>

		<?php include("footer.php"); ?>

		<script>
$(document).ready(function(){
	window.chart_1.params = {
		fun 		: 'getGraphData',
		graph		: 'ClustersOccupancy',
		occupancy	: 'avail',
		clusters 	: 'All',
		valuetype 	: 'RelativeValues',

		precision 	: 0.001, // 0.1 %
		scalefactor : 1e-6, // 1e6 kB = 1 GB

		graphType	 : 'line'
	};

	createVariableSelector('ClustersOccupancy', '#chart_1')

	setTimeout (function() {
		ShowGraph('#canvas_1', window.chart_1.params, CreateGraphTitle(window.chart_1.params));
	}, 0);
});
		</script>
	</body>
</html>
