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

function ShowGraph(){
	$.ajax({
		url: "api.php",
		method: "GET",
		data: {
			fun : 'getGraphData',
			graph : $("#graph_selector option:selected").val(),
			cluster : $("#cluster_selector option:selected").val()
		},
		success: function(data) {
			try {
				// allways first destroy previous chart
				window.myLine.destroy();
			} catch (e) {
			}

			console.log(data);

			var title_text = {
				clusters: 'Free cores clusters',
				queues: 'Free cores ratio queues'
			};

			var config = {
					type: 'line',
					data: data,
					options: {
							responsive: true,
							title:{
									display:true,
							 		text: title_text['clusters']
							},
							tooltips: {
									mode: 'label',
									callbacks: {
									}
							},
							hover: {
									mode: 'dataset'
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

					hex = cs(i/config.data.datasets.length).hex();

					dataset.borderWidth = 3;
					//dataset.borderDash = [20,10];
					dataset.borderColor = hexToRgba(hex,1);

					//dataset.backgroundColor = hexToRgba(hex,0.3);
					dataset.fill = false;

					dataset.steppedLine = true;
					dataset.pointRadius = 6;

					//dataset.pointStyle = 'rect';

					dataset.pointBorderColor = hexToRgba(hex,1);
					dataset.pointBackgroundColor = hexToRgba(hex,1);
					dataset.pointBorderWidth = 1;

					dataset.pointHoverBackgroundColor = hexToRgba(hex,1);
					dataset.pointHoverBorderColor = 'black';
			});

			var ctx = $("#mycanvas");
			window.myLine = new Chart(ctx, config);

		},
		error: function(data) {
			console.log(data);
		}
	});
}

function createClusterSelector(){
	  $.ajax({
	      type     : 'GET',
	      url      : 'api.php',
				data 		 : {
					fun : 'getClusters'
				},
	      cache: false,
	      success  : function(data) {
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

$(document).ready(function(){
	ShowGraph();
	createClusterSelector();
});
$(document).on('change',"select#graph_selector",function(){
	ShowGraph();
});
$(document).on('change',"select#cluster_selector",function(){
	ShowGraph();
});
$(document).on('click',"button#reset_chart",function(){
	ShowGraph();
});
