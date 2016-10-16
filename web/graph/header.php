<title>Cluster Reporting</title>
<script src="node_modules/chart.js/dist/Chart.bundle.js"></script>
<script src="node_modules/hammerjs/hammer.js"></script>
<script src="node_modules/Chart.Zoom.js/Chart.Zoom.js"></script>
<script src="node_modules/chroma-js/chroma.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<style type="text/css">
	canvas{
		-moz-user-select: none;
		-webkit-user-select: none;
		-ms-user-select: none;
	}
	.chart-container {
		/*margin-top: -1px;*/
		/*margin-right: -1px;*/
		border: 0px solid lightgray;
		padding: 10px;
		height: auto;
	}
	.flexcontainer {
		display: -webkit-flex;
		display: flex;
		-webkit-flex-direction: row;
		flex-direction: row;
	}
	#body_wrapper {
		border: 1px solid lightgray;
		margin: 5px;
	}
	#node_info_tooltip {
		padding: 10px;
		-moz-transition: opacity 0.2s ease-out;  /* FF4+ */
		-o-transition: opacity 0.2s ease-out;  /* Opera 10.5+ */
		-webkit-transition: opacity 0.2s ease-out;  /* Saf3.2+, Chrome */
		-ms-transition: opacity 0.2s ease-out;  /* IE10? */
		transition: opacity 0.2s ease-out;
	}
	.form-inline-force .form-group {
		display: inline-block;
		margin-bottom: 0;
		vertical-align: middle;
	}
	.form-inline-force .form-control {
		display: inline-block;
		width: auto;
		vertical-align: middle;
	}
</style>

<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

<!-- Optional theme -->
<!--<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">-->

<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
