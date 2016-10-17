<!DOCTYPE html>
<html>
	<head>
		<?php include("header.php"); ?>
	</head>
	<body>
		<div id="body_wrapper">
			<a href="https://github.com/ondrejtichacek/cluster-reporting" class="github-corner" aria-label="View source on Github">
				<svg width="80" height="80" viewBox="0 0 250 250" style="fill:#151513; color:#fff; position: absolute; top: 0; border: 0; right: 0;" aria-hidden="true">
					<path d="M0,0 L115,115 L130,115 L142,142 L250,250 L250,0 Z"></path>
					<path d="M128.3,109.0 C113.8,99.7 119.0,89.6 119.0,89.6 C122.0,82.7 120.5,78.6 120.5,78.6 C119.2,72.0 123.4,76.3 123.4,76.3 C127.3,80.9 125.5,87.3 125.5,87.3 C122.9,97.6 130.6,101.9 134.4,103.2" fill="currentColor" style="transform-origin: 130px 106px;" class="octo-arm"></path>
					<path d="M115.0,115.0 C114.9,115.1 118.7,116.5 119.8,115.4 L133.7,101.6 C136.9,99.2 139.9,98.4 142.2,98.6 C133.8,88.0 127.5,74.4 143.8,58.0 C148.5,53.4 154.0,51.2 159.7,51.0 C160.3,49.4 163.2,43.6 171.4,40.1 C171.4,40.1 176.1,42.5 178.8,56.2 C183.1,58.6 187.2,61.8 190.9,65.4 C194.5,69.0 197.7,73.2 200.1,77.6 C213.8,80.2 216.3,84.9 216.3,84.9 C212.7,93.1 206.9,96.0 205.4,96.6 C205.1,102.4 203.0,107.8 198.3,112.5 C181.9,128.9 168.3,122.5 157.7,114.1 C157.9,116.9 156.7,120.9 152.7,124.9 L141.0,136.5 C139.8,137.7 141.6,141.9 141.8,141.8 Z" fill="currentColor" class="octo-body"></path>
				</svg>
			</a>
			<style>.github-corner:hover .octo-arm{animation:octocat-wave 560ms ease-in-out}@keyframes octocat-wave{0%,100%{transform:rotate(0)}20%,60%{transform:rotate(-25deg)}40%,80%{transform:rotate(10deg)}}@media (max-width:500px){.github-corner:hover .octo-arm{animation:none}.github-corner .octo-arm{animation:octocat-wave 560ms ease-in-out}}</style>
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
