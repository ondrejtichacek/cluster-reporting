<!DOCTYPE html>
<html>
	<head>
		<?php include("header.php"); ?>
	</head>
	<body>
		<div id="body_wrapper">
			<div class="flexcontainer">
				<div style="width: 10%; height: auto">
					<div style="width: 100%; height: auto;" id="chart_a">
						<canvas id="canvas_a" width="10" height="10"></canvas>
					</div>
					<div style="width: 100%; height: auto;" id="chart_b">
						<canvas id="canvas_b" width="100" height="100"></canvas>
					</div>
					<div style="width: 100%; height: auto;" id="chart_c">
						<canvas id="canvas_c" width="100" height="100"></canvas>
					</div>
					<div id="node_info_tooltip"></div>
				</div>
				<div style="width: 90%;">
					<div class="flexcontainer">
						<div class="chart-container" style="width: 50%; height: auto; border-width: 0 0 1px 1px;" id="chart_1">
							<form class="form-inline">
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
						<div class="chart-container" style="width: 50%; height: auto; border-width: 0 0 1px 1px;" id="chart_2">
							<form class="form-inline">
								<div class="form-group">
									<select class="form-control variable-selector" name="occupancy">
										<option value="avail">Available</option>
									</select>
								</div>
								<div class="form-group">
									<select class="form-control" name="graph">
										<option value="ClustersWeekdayOccupancy">Weekdays</option>
										<option value="ClustersHoursOccupancy">Hours</option>
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
									<button type="button" name="resetzoom" class="btn btn-default">Reset Zoom</button>
								</div>
							</form>
							<canvas id="canvas_2"></canvas>
						</div>
					</div>
					<div class="flexcontainer">
						<div class="chart-container" style="width: 50%; height: auto; border-width: 0 0 0 1px;" id="chart_3">
							<form class="form-inline">
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
							<canvas id="canvas_3"></canvas>
						</div>
						<div class="chart-container" style="width: 50%; height: auto; border-width: 0 0 0 1px;" id="chart_4">
							<form class="form-inline">
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
							<canvas id="canvas_4"></canvas>
						</div>
					</div>
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

	window.chart_2.params = {
		fun 		: 'getGraphData',
		graph		: 'ClustersWeekdayOccupancy',
		occupancy	: 'avail',
		clusters 	: 'All',
		valuetype 	: 'RelativeValues',

		precision 	: 0.001, // 0.1 %
		scalefactor : 1e-6, // 1e6 kB = 1 GB

		graphType	 : 'bar'
	};

	createVariableSelector('ClustersWeekdayOccupancy', '#chart_2')

	setTimeout (function() {
		ShowGraph('#canvas_2', window.chart_2.params, CreateGraphTitle(window.chart_2.params));
	}, 1200);

	window.chart_a.params = {
		fun 		: 'getGraphData',
		graph		: 'NodeDetails',
		occupancy	: 'avail',
		clusters 	: [{name: 'magnesium'}],
		valuetype 	: 'RelativeValues',

		precision 	: 0.001, // 0.1 %
		scalefactor : 1e-6, // 1e6 kB = 1 GB

		graphType	 : 'pie'
	};

	setTimeout (function() {
		ShowGraph('#canvas_a', window.chart_a.params, CreateGraphTitle(window.chart_a.params));
	}, 900);

	window.chart_b.params = {
		fun 		: 'getGraphData',
		graph		: 'NodeDetails',
		occupancy	: 'avail',
		clusters 	: [{name: 'oxygen'}],
		valuetype 	: 'RelativeValues',

		precision 	: 0.001, // 0.1 %
		scalefactor : 1e-6, // 1e6 kB = 1 GB

		graphType	 : 'pie'
	};

	setTimeout (function() {
		ShowGraph('#canvas_b', window.chart_b.params, CreateGraphTitle(window.chart_b.params));
	}, 1100);

	window.chart_c.params = {
		fun 		: 'getGraphData',
		graph		: 'NodeDetails',
		occupancy	: 'avail',
		clusters 	: [{name: 'sodium'}],
		valuetype 	: 'RelativeValues',

		precision 	: 0.001, // 0.1 %
		scalefactor : 1e-6, // 1e6 kB = 1 GB

		graphType	 : 'pie'
	};

	setTimeout (function() {
		ShowGraph('#canvas_c', window.chart_c.params, CreateGraphTitle(window.chart_c.params));
	}, 1300);

	window.chart_3.params = {
		fun 		: 'getGraphData',
		graph		: 'QueuesOccupancy',
		occupancy	: 'avail',
		clusters 	: 'All',
		valuetype 	: 'RelativeValues',

		precision 	: 0.001, // 0.1 %
		scalefactor : 1e-6, // 1e6 kB = 1 GB

		graphType	 : 'line'
	};

	createVariableSelector('QueuesOccupancy', '#chart_3')

	setTimeout (function() {
		ShowGraph('#canvas_3', window.chart_3.params, CreateGraphTitle(window.chart_3.params));
	}, 1800);

	window.chart_4.params = {
		fun 		: 'getGraphData',
		graph		: 'FilesystemOccupancy',
		occupancy	: 'avail',
		clusters 	: 'All',
		valuetype 	: 'RelativeValues',

		precision 	: 0.001, // 0.1 %
		scalefactor : 1e-6, // 1e6 kB = 1 GB

		graphType	 : 'line'
	};

	createVariableSelector('FilesystemOccupancy', '#chart_4')

	setTimeout (function() {
		ShowGraph('#canvas_4', window.chart_4.params, CreateGraphTitle(window.chart_4.params));
	}, 600);

});
		</script>
	</body>
</html>
