$(document).ready(function() {

	var margin = {top: 20, right: 20, bottom: 30, left: 50},
	    width = (window.innerWidth * .85) - margin.left - margin.right,
	    height = (window.innerHeight * .65) - margin.top - margin.bottom;

	var parseDate = d3.time.format("%Y-%m-%d %H:%M:%S").parse;

	var x = d3.time.scale()
	    .range([0, width]);

	var y = d3.scale.linear()
	    .range([height, 0]);

	var xAxis = d3.svg.axis()
	    .scale(x)
	    .orient("bottom");

	var yAxis = d3.svg.axis()
	    .scale(y)
	    .orient("left");

	var line = d3.svg.line()
		.interpolate("basis")
	    .x(function(d) { return x(d.date); })
	    .y(function(d) { return y(d.temp); });
	    
	var line2 = d3.svg.line()
		.interpolate("basis")
	    .x(function(d) { return x(d.date); })
	    .y(function(d) { return y(d.outdoorTemp); });

	var svg = d3.select("body").append("svg")
	    .attr("width", width + margin.left + margin.right)
	    .attr("height", height + margin.top + margin.bottom)
	  .append("g")
	    .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

	d3.json("/getTemps/" + PAST_HOURS, function(error, data) {
	  data.forEach(function(d) {
	    d.date = parseDate(d.timestamp);
	    d.temp = +d.tempF;
	    d.outdoorTemp = +d.outdoorTemp;
	  });

	  x.domain(d3.extent(data, function(d) { return d.date; }));
	  y.domain([
	  	d3.min(data, function(d) { return Math.min(d.temp, d.outdoorTemp) } ),
	  	d3.max(data, function(d) { return Math.max(d.temp, d.outdoorTemp) } )
	  ]);

	  svg.append("g")
	      .attr("class", "x axis")
	      .attr("transform", "translate(0," + height + ")")
	      .call(xAxis);

	  svg.append("g")
	      .attr("class", "y axis")
	      .call(yAxis)
	    .append("text")
	      .attr("transform", "rotate(-90)")
	      .attr("y", 6)
	      .attr("dy", ".71em")
	      .style("text-anchor", "end")
	      .text("Temp (F)");

	  svg.append("path")
	      .datum(data)
	      .attr("class", "line")
	      .attr("d", line);
	
	  svg.append("path")
	      .datum(data)
	      .attr("class", "line2")
	      .attr("d", line2);

	});

});