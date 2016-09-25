$(document).ready(function(){
	$.ajax({
		url: "api.php",
		method: "GET",
		success: function(data) {
			console.log(data);

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


			var config = {
					type: 'line',
					data: data,
					options: {
							responsive: true,
							title:{
									display:true,
									text:'Chart.js Line Chart'
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
        			}
					}
			};

			$.each(config.data.datasets, function(i, dataset) {
					hex = colorbrewer['Paired'][config.data.datasets.length][i];
					dataset.borderColor = hexToRgba(hex,0.4);
					//dataset.backgroundColor = hexToRgba(hex,0.3);
					dataset.pointBorderColor = hexToRgba(hex,0.9);
					dataset.pointBackgroundColor = hexToRgba(hex,0.5);
					dataset.pointBorderWidth = 1;
			});

			var ctx = $("#mycanvas");
			window.myLine = new Chart(ctx, config);

		},
		error: function(data) {
			console.log(data);
		}
	});
});
