var randomColorFactor = function() {
	return Math.round(Math.random() * 255);
};
var randomColor = function(opacity) {
	return 'rgba(' + randomColorFactor() + ',' + randomColorFactor() + ',' + randomColorFactor() + ',' + (opacity || '.3') + ')';
};

function hexToRgb(hex) {
	var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
 	return result ? {
		r: parseInt(result[1], 16),
		g: parseInt(result[2], 16),
		b: parseInt(result[3], 16)
	} : null;
}

function hexToRgba(hex,opacity) {
	var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
	return 'rgba(' + parseInt(result[1], 16) + ','
					+ parseInt(result[2], 16) + ','
					+ parseInt(result[3], 16) + ','
				 	+ (opacity || '.3') + ')';
};

function SeqColormap(i, n, system) {
	switch (system) {
		case 'magnesium':
			var colors = ['#a50f15','#ef3b2c','#fc9272'];
			break;
		case 'oxygen':
			var colors = ['#7a0177','#dd3497','#fa9fb5'];
			break;
		case 'sodium':
			var colors = ['#08519c','#4292c6','#9ecae1'];
			break;
		default:

	}

	var colors = chroma.bezier(colors);
	var cs = chroma.scale(colors).mode('lab').correctLightness(),

	hex = cs(i/n).hex();

	return hex;
}

function myColormap(i,n) {
	//cmap = ['#8b0000', '#b61d39', '#d84765', '#ef738b', '#fea0ac', '#ffd1c9', '#ffffe0', '#c7f0ba', '#9edba4', '#7ac696', '#5aaf8c', '#399785','#008080']
	//hex = cmap[i];
	//hex = colorbrewer['Paired'][config.data.datasets.length][i];

	var colors0 = ['darkred', 'deeppink', 'lightyellow'],
		colors1 = ['lightyellow', 'lightgreen', 'teal'];

	// initialize chroma.scale
	var colors0 = chroma.bezier(colors0),
		colors1 = chroma.bezier(colors1);

	var cs0 = chroma.scale(colors0).mode('lab').correctLightness(),
		cs1 = chroma.scale(colors1).mode('lab').correctLightness();

	var cs = function(t) {
		if (t < 0.5) return cs0(t*2);
		return cs1(t*2-1);
	};

	hex = cs(i/n).hex();

	return hex;
}

function destroyGraphs(canvas_id) {
	$.each(['myLine', 'myBar'], function(i, chartType){
		try {
			window[$(canvas_id).attr('id')].chart.destroy();
		} catch (e) {
		}
	});
}

function myLineGraph(data, title_text, canvas_id) {

	destroyGraphs(canvas_id);

	console.log(data);

	var config = {
		type: 'line',
		data: data,
		options: {
			responsive: true,
			title:{
				display:true,
				text: title_text
			},
			tooltips: {
				mode: 'label',
				callbacks: {
				}
			},
			hover: {
				mode: 'label'
			},
			scales: {
				xAxes: [{
					type: 'time'
				}]
			},
			pan: {
				enabled: true,
				mode: 'x'
			},
			zoom: {
				enabled: true,
				//drag: true,
				mode: 'x',
		}
	}
	};

	$.each(config.data.datasets, function(i, dataset) {

		//hex = myColormap(i, config.data.datasets.length);
		hex = SeqColormap(dataset.i, dataset.len, dataset.cluster)

		dataset.borderWidth = 1;
		//dataset.borderDash = [20,10];
		dataset.borderColor = hexToRgba(hex,1);

		//dataset.backgroundColor = hexToRgba(hex,0.3);
		dataset.fill = false;

		//dataset.steppedLine = true;
		dataset.lineTension = 0;

		// if ($("#datapoint_checkbox").is(':checked')){
		if ($(canvas_id).parent().find(".datapoint-checkbox").is(':checked')){
			dataset.pointRadius = 2;

			//dataset.pointStyle = 'rect';

			dataset.pointBorderColor = hexToRgba(hex,1);
			//dataset.pointBackgroundColor = hexToRgba(hex,1);
			dataset.pointBorderWidth = 1;

			//dataset.pointHoverBackgroundColor = hexToRgba(hex,1);
			//dataset.pointHoverBorderColor = 'black';
		} else {
			dataset.pointRadius = 0;
		}
	});

	var ctx = $(canvas_id);
	window[ctx.attr('id')].chart = new Chart(ctx, config);
}

function myBarGraph(data, title_text, canvas_id) {

	destroyGraphs(canvas_id);

	console.log(data);

	var config = {
		type: 'bar',
		data: data,
		options: {
			responsive: true,
			title:{
				display:true,
				text: title_text
			},
			tooltips: {
				mode: 'label',
				callbacks: {
				}
			},
			hover: {
				mode: 'label'
			},
			scales: {
				xAxes: [{
						type: 'category'
				}]
			},
			pan: {
				enabled: false
			},
			zoom: {
				enabled: false
			}
		}
	};

	$.each(config.data.datasets, function(i, dataset) {
		//hex = myColormap(i, config.data.datasets.length);
		hex = SeqColormap(dataset.i, dataset.len, dataset.cluster)

		dataset.borderWidth = 1;
		//dataset.borderDash = [20,10];
		dataset.borderColor = hexToRgba(hex,1);

		dataset.backgroundColor = hexToRgba(hex,0.3);
		//dataset.fill = false;

		//dataset.steppedLine = true;
		//dataset.pointRadius = 1;

		//dataset.pointStyle = 'rect';

		//dataset.pointBorderColor = hexToRgba(hex,1);
		//dataset.pointBackgroundColor = hexToRgba(hex,1);
		//dataset.pointBorderWidth = 1;

		//dataset.pointHoverBackgroundColor = hexToRgba(hex,1);
		//dataset.pointHoverBorderColor = 'black';
	});

	var ctx = $(canvas_id);
	window[ctx.attr('id')].chart = new Chart(ctx, config);
}

function myPieGraph(data, title_text, canvas_id) {

	destroyGraphs(canvas_id);

	console.log(data);

	var config = {
		type: 'doughnut',
		data: data,
		options: {
			responsive: true,
			title: {
				display: true,
				text: title_text
			},
			legend: {
				display: false,
			},
			tooltips: {
				enabled: false,
				custom: function(tooltip) {

					// Tooltip Element
					var tooltipEl = $('#node_info_tooltip');

					// Hide if no tooltip
					if (!tooltip.opacity) {
						tooltipEl.css({
							opacity: 0
						});
						$('.chartjs-wrap canvas')
							.each(function(index, el) {
							$(el).css('cursor', 'default');
							});
						return;
						}

					// Set Text
					if (tooltip.body) {
						var innerHtml = [
							(tooltip.body[0].lines || []).join('\n')
						];
						tooltipEl.html(innerHtml.join('\n').replace(/,/g, ', '));
					}

					// Display
					tooltipEl.css({
						opacity: 1,
					});
				}
				/*enabled: false,*/
				/*tooltipFontSize: 10,
				tooltipTemplate: "<%if (label){%><%=label%>: <%}%><%= value %>hrs",
				percentageInnerCutout : 70*/
				/*mode: 'label',
				callbacks: {
				}*/
			},
			hover: {
				mode: 'label'
			},
			/*scales: {
				xAxes: [{
						type: 'category'
				}]
			},*/
			pan: {
				enabled: false
			},
			zoom: {
				enabled: false
			}
		}
	};

	$.each(config.data.datasets, function(i, dataset) {
		//hex = myColormap(i, config.data.datasets.length);
		hex = SeqColormap(dataset.i, dataset.len, dataset.cluster)

		dataset.borderWidth = 1;
		//dataset.borderDash = [20,10];
		dataset.borderColor = hexToRgba(hex,1);

		var hexArray = [];
		$(dataset.data).each(function(i, data) {
			hexArray.push(SeqColormap(i, dataset.data.length, dataset.cluster));
		});

		dataset.backgroundColor = hexArray;
		//dataset.fill = false;

		//dataset.steppedLine = true;
		//dataset.pointRadius = 1;

		//dataset.pointStyle = 'rect';

		//dataset.pointBorderColor = hexToRgba(hex,1);
		//dataset.pointBackgroundColor = hexToRgba(hex,1);
		//dataset.pointBorderWidth = 1;

		//dataset.pointHoverBackgroundColor = hexToRgba(hex,1);
		//dataset.pointHoverBorderColor = 'black';
	});

	var ctx = $(canvas_id);
	window[ctx.attr('id')].chart = new Chart(ctx, config);
}

function CreateGraphParams(){

	var selected_clusters = [{name : $("#cluster_selector option:selected").val()}];

	var params = {
		fun 		: 'getGraphData',
		graph		: $("#graph_selector option:selected").val(),
		occupancy	: $("#occupancy_selector option:selected").val(),
		clusters 	: selected_clusters,
		valuetype 	: $("#value_type_selector option:selected").val()
	}

	params['precision'] = 0.001; // 0.1 %
	params['scalefactor'] = 1e-6; // 1e6 kB = 1 GB

	switch (params.graph) {
		case 'ClustersWeekdayOccupancy':
		case 'ClustersHoursOccupancy':
			params['graphType'] = 'bar';
			break;
		case 'ClustersOccupancy':
		case 'FilesystemOccupancy':
		case 'QueuesOccupancy':
			params['graphType'] = 'line';
			break;
	}

	return params;
}

function CreateGraphTitle(params){
	switch (params.graph) {
		case 'ClustersWeekdayOccupancy':
		case 'ClustersHoursOccupancy':
			var title_text = 'Average ' + $("#occupancy_selector option:selected").text() + ' cores ' + (params.valuetype == 'RelativeValues' ? 'ratio ' : '') + '- clusters';
			break;
		case 'ClustersOccupancy':
			var title_text = $("#occupancy_selector option:selected").text() + ' cores ' + (params.valuetype == 'RelativeValues' ? 'ratio ' : '') + '- clusters';
			break;
		case 'FilesystemOccupancy':
			var title_text = $("#occupancy_selector option:selected").text() + ' fileystem ' + (params.valuetype == 'RelativeValues' ? 'ratio ' : '') + '- clusters';
			break;
		case 'QueuesOccupancy':
			var title_text = $("#occupancy_selector option:selected").text() + ' cores ' + (params.valuetype == 'RelativeValues' ? 'ratio ' : '') + '- queues';
			break;
		case 'NodeDetails':
			var title_text = params.clusters[0].name;
			break;
	}

	return title_text;
}

function ShowGraph(canvas, params, title_text){

	return $.ajax({
		url: "api.php",
		method: "GET",
		data: params,
		success: function(data) {
			switch (params.graphType){
				case 'bar':
					myBarGraph(data, title_text, canvas)
					break;
				case 'line':
					myLineGraph(data, title_text, canvas)
					break;
				case 'pie':
					myPieGraph(data, title_text, canvas)
					break;
			}
		},
		error: function(data) {
			console.log(data);
		}
	});
}

function createClusterSelector(){
	return $.ajax({
		type 	: 'GET',
		url		: 'api.php',
		data 	: {
			fun : 'getClusters'
			},
		cache: false,
		success	: function(data) {
			var output = '<option value="">All</option>';

			$.each(data, function(key, val){
				output += '<option value="' + val.name + '">' + val.display_name + '</option>';
			});

			$('#cluster_selector').empty().append(output);
		},
		error: function(){
			console.log("Ajax failed");
		}
	});
}

function createVariableSelector(graph, wrapper_div){
	return $.ajax({
		type 	: 'GET',
		url		: 'api.php',
		data 	: {
			fun 	: 'getVariables',
			graph	: graph, //$("#graph_selector option:selected").val(),
			},
		cache: false,
		success	: function(data) {

			// store currently selected option
			// var selected_val = $("#occupancy_selector option:selected").val();
			var selected_val = $(wrapper_div).find("select.variable-selector option:selected").val();

			// construct the options
			var output = '';
			$.each(data, function(key, val){
				output += '<option value="' + key + '">' + val + '</option>';
			});

			//$('#occupancy_selector').empty().append(output);
			$(wrapper_div).find("select.variable-selector").empty().append(output);

			// preserve previously elected option 	if exists
			if (data[selected_val] !== undefined) {
				//$("#occupancy_selector").val(selected_val);
				$(wrapper_div).find("select.variable-selector").val(selected_val);
			}

		},
		error: function(){
			console.log("Ajax failed");
		}
	});
}

$(document).ready(function(){
	//$.when(createVariableSelector(), createClusterSelector()).done(function(a1,a2){
		//ShowGraph();

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


	//});
});

$("select").change(function(e){
	var wrapper_id = $(e.target).closest('.chart-container').attr('id');
	var canvas = $(e.target).closest('.chart-container').children("canvas");

	window[wrapper_id].params[e.target.name] = e.target.value;

	ShowGraph(canvas, window[wrapper_id].params, CreateGraphTitle(window[wrapper_id].params));
});

$("input").change(function(e){
	var wrapper_id = $(e.target).closest('.chart-container').attr('id');
	var canvas = $(e.target).closest('.chart-container').children("canvas");

	window[wrapper_id].params[e.target.name] = e.target.value;

	ShowGraph(canvas, window[wrapper_id].params, CreateGraphTitle(window[wrapper_id].params));
});

$(document).on('change',"select#graph_selector",function(){
	$.when(createVariableSelector()).done(function(a1){
		ShowGraph();
	});
});
$(document).on('change',"select#occupancy_selector",function(){
	ShowGraph();
});
$(document).on('change',"select#cluster_selector",function(){
	ShowGraph();
});
$(document).on('change',"select#value_type_selector",function(){
	ShowGraph();
});
$(document).on('change',"#datapoint_checkbox",function(){
	ShowGraph();
});
$(document).on('click',"button#reset_chart",function(){
	ShowGraph();
});
