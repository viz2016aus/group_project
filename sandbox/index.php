<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Group Project</title>
<link rel="stylesheet" type="text/css" href="css/main.css">
<script src="js/jquery-2.2.1.min.js"></script>
<script src="js/d3.js"></script>
<script type="text/javascript">
var aus_lat_u = 49.2;
var aus_lat_l = 46.3;
var aus_lon_r = 17.2;
var aus_lon_l = 9.4;
var pol_dim = [];
var pol_min_x;
var pol_min_y;
var pol_max_x;
var pol_max_y;
var y_lat;
var x_lon;
var svg_ch;
var svg;
var data_arr = [];
var sel_data_arr = [];
var b_chart_arr = [];
var obj_clicked = 0;
var id_str;
var drag_mode = 0;
var cur_txt_id;
var coordinates = [0,0];
var d_box_x;
var d_box_w;
var d_box_y;
var d_box_h
var x_m;
var y_m;
var qtyMax;

$(document).keyup(function(e) {
	if (e.keyCode == 27 && obj_clicked == 1) {
		esc_pressed();
    }
});

function esc_pressed() {
	svg.selectAll("circle")
		.transition()
			.duration(500)
				.style("fill", "red")
				.style("stroke-width", 0)
				.style("opacity", 0.2);
	svg.select("#hwy_txt")
		.transition()
  			.duration(400)
				.style("opacity", 0);
	svg.select("#hwy_point_txt")
		.transition()
   			.duration(400)
				.style("opacity", 0);
	for (i = 0; i < data_arr.length; i++) {
		svg.select("#" + data_arr[i][0] + "_" + data_arr[i][2]).on('mousedown.drag', null);
	}
	sel_data_arr = [];
	obj_clicked = 0;
}

function bar_chart_close() {
	d3.select("#b_chart_id").remove();
	svg.select("#b_chart_rect").style("visibility", "hidden");
	svg.select("#b_chart_close").style("visibility", "hidden");
	svg.select("#rect_over").style("fill", "none");
	svg.select("#rect_over").style("visibility", "hidden");
	svg.select("#close_line_1").style("visibility", "hidden");
	svg.select("#close_line_2").style("visibility", "hidden");
	svg.select("#hwy_txt").style("opacity", 0);
	svg.select("#hwy_point_txt").style("opacity", 0);
	svg.select("#ch_hwy_txt").style("opacity", 0);
	svg.select("#ch_hwy_point_txt").style("opacity", 0);
	svg.select("#ch_hwy_line").style("stroke-opacity", 0);
	esc_pressed();
}

function init_svg() {
var pol_arr = d3.selectAll("polygon").attr("points").trim().split(" ");
	for (var i = 0; i < pol_arr.length; i++) {
		pol_dim[i] = [pol_arr[i].split(",")[0],pol_arr[i].split(",")[1]];
    }
pol_dim[pol_arr.length] = [pol_dim[0][0],pol_dim[0][1]];
pol_min_x = Math.min.apply(null, pol_dim.map(function (e) { return e[0]}));
pol_min_y = Math.min.apply(null, pol_dim.map(function (e) { return e[1]}));
pol_max_x = Math.max.apply(null, pol_dim.map(function (e) { return e[0]}));
pol_max_y = Math.max.apply(null, pol_dim.map(function (e) { return e[1]}));
y_lat = (pol_max_y - pol_min_y) / (aus_lat_u - aus_lat_l);
x_lon = (pol_max_x - pol_min_x) / (aus_lon_r - aus_lon_l);
svg = d3.select("#svg_div").append("svg")
	.attr("id", "bg_svg_id")
	.attr("class", "bg_svg_class")
    .attr("width", pol_max_x)
    .attr("height", pol_max_y + 420);

svg.append("rect")
	.attr("id", "b_chart_rect")
	.attr("x", pol_min_x + 5)
	.attr("y", pol_max_y + 30)
	.attr("width", pol_max_x - 10)
	.attr("height", 380)
	.style("visibility", "hidden")
	.style("stroke", "red")
	.style("stroke-width", 2)
	.style("fill", "rgb(255,255,255)");

svg.append("rect")
	.attr("id", "rect_over")
	.attr("x", pol_min_x + 5)
	.attr("y", pol_max_y + 30)
	.attr("width", pol_max_x - 10)
	.attr("height", 380)
	.style("visibility", "hidden")
	.style("opacity", 0.07)
	.style("stroke-width", 0)
	.style("fill", "rgb(255,255,255)");

svg.append("rect")
	.attr("id", "b_chart_close")
	.attr("x", svg.select("#b_chart_rect").attr("x")*1 + svg.select("#b_chart_rect").attr("width")*1 - 26)
	.attr("y", svg.select("#b_chart_rect").attr("y")*1 - 26)
	.attr("width", 26)
	.attr("height", 26)
	.style("visibility", "hidden")
	.style("stroke", "red")
	.style("stroke-width", 2)
	.style("fill", "white");
	svg.select("#b_chart_close")
		.on("click", function(d) {
				bar_chart_close();
					}
			);

svg.append("line")
	.attr("x1", svg.select("#b_chart_close").attr("x")*1)
	.attr("y1", svg.select("#b_chart_close").attr("y")*1)
	.attr("x2", svg.select("#b_chart_close").attr("x")*1 + svg.select("#b_chart_close").attr("width")*1)
	.attr("y2", svg.select("#b_chart_close").attr("y")*1 + svg.select("#b_chart_close").attr("height")*1)
	.attr("id", "close_line_1")
	.attr("stroke", "red")
	.style("visibility", "hidden")
	//.attr("stroke-opacity", 0)
	.attr("stroke-width", 2);

svg.append("line")
	.attr("x1", svg.select("#b_chart_close").attr("x")*1)
	.attr("y1", svg.select("#b_chart_close").attr("y")*1 + svg.select("#b_chart_close").attr("height")*1)
	.attr("x2", svg.select("#b_chart_close").attr("x")*1 + svg.select("#b_chart_close").attr("width")*1)
	.attr("y2", svg.select("#b_chart_close").attr("y")*1)
	.attr("id", "close_line_2")
	.attr("stroke", "red")
	.style("visibility", "hidden")
	//.attr("stroke-opacity", 0)
	.attr("stroke-width", 2);

svg.append("text")
	.attr("x", svg.select("#b_chart_rect").attr("x")*1 + 180)
	.attr("y", svg.select("#b_chart_rect").attr("y")*1 - 580)
	.attr("id", "hwy_txt")
	.style("fill", "red")
	.style("opacity", 0)
	.style("font", "55px Helvetica Neue")
	.attr("dy", ".35em");

svg.append("text")
	.attr("id", "hwy_point_txt")
	.attr("x", svg.select("#b_chart_rect").attr("x")*1 + 180)
	.attr("y", svg.select("#b_chart_rect").attr("y")*1 - 530)
	.style("fill", "red")
	.style("opacity", 0)
	.style("font", "40px Helvetica Neue")
	.attr("dy", ".35em");

svg.append("text")
	.attr("x", svg.select("#b_chart_rect").attr("x")*1 + 4)
	.attr("y", svg.select("#b_chart_rect").attr("y")*1 - 66)
	.attr("id", "ch_hwy_txt")
	.style("fill", "red")
	.style("opacity", 0)
	.style("font", "36px Helvetica Neue")
	.attr("dy", ".35em");

svg.append("text")
	.attr("id", "ch_hwy_point_txt")
	.attr("x", svg.select("#b_chart_rect").attr("x")*1 + 4)
	.attr("y", svg.select("#b_chart_rect").attr("y")*1 - 23)
	.style("fill", "red")
	.style("opacity", 0)
	.style("font", "24px Helvetica Neue")
	.attr("dy", ".35em");

svg.append("line")
	.attr("x1", svg.select("#b_chart_rect").attr("x")*1)
	.attr("y1", svg.select("#b_chart_rect").attr("y")*1 - 42)
	.attr("x2", svg.select("#b_chart_rect").attr("x")*1 + 112)
	.attr("y2", svg.select("#b_chart_rect").attr("y")*1 - 42)
	.attr("id", "ch_hwy_line")
	.attr("stroke", "red")
	.attr("stroke-opacity", 0)
	.attr("stroke-width", 2);

get_data();
}

function draw_points(highway, checkpoint, point_num, latitude, longitude, qty) {
lat = y_lat * (aus_lat_u - latitude) + pol_min_y;
lon = x_lon * (longitude - aus_lon_l) + pol_min_x;
var circ_r = qty * 25 / qtyMax + 15;
svg.append("circle")
	.attr("cx", lon)
	.attr("cy", lat)
	.attr("id", highway + "_" + point_num)
	.attr("class", highway)
	.attr("stroke-width", 0)
	.attr("r", circ_r)
	.style("opacity", 0.2)
	.style("fill", "red")
	.style("cursor", "pointer")
	.on("click", 
		function(d) {
			if (drag_mode != 1) {
				id_str = this.id;
				obj_i = id_str.split("_");
				sel_data_arr = [];
				if (d3.event.ctrlKey) {
					for (j = 0; j < data_arr.length; j++) {
						if (obj_i[1] == data_arr[j][2]) {
							push_val = [];
							push_val[0] = data_arr[j][0];
							push_val[1] = data_arr[j][1];
							push_val[2] = data_arr[j][2];
							push_val[3] = data_arr[j][3];
							push_val[4] = data_arr[j][4];
							push_val[5] = data_arr[j][5];
							sel_data_arr.push(push_val);
						} else {
							svg.select("#" + id_str).on('mousedown.drag', null);
							svg.selectAll("#" + data_arr[j][0] + "_" + data_arr[j][2])
								.transition()
    								.duration(300)
										.style("stroke-width", 0)
										.style("fill", "red")
										.style("opacity", 0.2);
						}
					}
					svg.selectAll("#" + id_str)
						.call(drag);
					svg.selectAll("#" + id_str)
						.transition()
    						.duration(300)
								.style("stroke", "red")
								.style("stroke-width", 3)
								.style("fill", "white")
								.style("opacity", 0.6);
					obj_clicked = 1;
				} else {
					for (j = 0; j < data_arr.length; j++) {
						if (obj_i[0] == data_arr[j][0]) {
							push_val = [];
							push_val[0] = data_arr[j][0];
							push_val[1] = data_arr[j][1];
							push_val[2] = data_arr[j][2];
							push_val[3] = data_arr[j][3];
							push_val[4] = data_arr[j][4];
							push_val[5] = data_arr[j][5];
							sel_data_arr.push(push_val);
						}
							svg.select("#" + data_arr[j][0] + "_" + data_arr[j][2])
								.transition()
    								.duration(300)
										.style("stroke-width", 0)
										.style("fill", "red")
										.style("opacity", 0.2);
					}
							svg.selectAll("." + obj_i[0])
								.call(drag);
							svg.selectAll("." + obj_i[0])
								.transition()
    								.duration(300)
										.style("stroke", "red")
										.style("stroke-width", 3)
										.style("fill", "white")
										.style("opacity", 0.6);
							obj_clicked = 1;
				}
			}
		}
	)
	.on("mouseover", 
		function(d) {
			if (drag_mode != 1) {
				id_str = this.id;
				obj_i = id_str.split("_");
				if (d3.event.ctrlKey) {
					svg.select("#" + id_str)
						.transition()
    						.duration(300)
								.style("stroke-width", 3)
								.style("fill", "red")
								.style("opacity", 0.6);
					svg.select("#hwy_txt")
						.text(obj_i[0])
							.transition()
    							.duration(400)
									.style("opacity", 1);
					for (j = 0; j < data_arr.length; j++) {
						if (obj_i[0] == data_arr[j][0] && obj_i[1] == data_arr[j][2]) {
							svg.select("#hwy_point_txt")
								.text(data_arr[j][1])
									.transition()
    									.duration(400)
											.style("opacity", 1);
						}
					}
				} else {
					svg.selectAll("." + obj_i[0])
						.transition()
    						.duration(300)
								.style("stroke-width", 3)
								.style("opacity", 0.6);
					svg.select("#hwy_txt")
						.text(obj_i[0])
							.transition()
    							.duration(400)
									.style("opacity", 1);
				}
			}
		}
	)
	.on("mouseout", 
		function(d) {
			if (drag_mode != 1) {
				id_str = this.id;
				obj_i = id_str.split("_");
				svg.selectAll("." + obj_i[0])
					.transition()
    					.duration(300)
							.style("stroke-width", 0)
							.style("fill", "red")
							.style("opacity", 0.2);
				if (sel_data_arr.length > 0) {
					for (k = 0; k < sel_data_arr.length; k++) {
						svg.select("#" + sel_data_arr[k][0] + "_" + sel_data_arr[k][2])
							.transition()
    							.duration(300)
									.style("stroke", "red")
									.style("stroke-width", 3)
									.style("fill", "white")
									.style("opacity", 0.6);
						if (sel_data_arr.length == 1) {
							svg.select("#hwy_txt")
								.text(sel_data_arr[k][0])
									.transition()
    									.duration(400)
											.style("opacity", 1);
							svg.select("#hwy_point_txt")
								.text(sel_data_arr[k][1])
									.transition()
    									.duration(400)
											.style("opacity", 1);
						} else {
							svg.select("#hwy_txt")
								.text(sel_data_arr[k][0])
									.transition()
    									.duration(400)
											.style("opacity", 1);
						}
					}
				} else {
					svg.select("#hwy_txt")
						.transition()
    							.duration(400)
									.style("opacity", 0);
					svg.select("#hwy_point_txt")
						.transition()
    							.duration(400)
									.style("opacity", 0);
				}
			}
		}
	);
}

function get_data() {
var sel_obj = document.getElementById("date_sel");
var map_date = sel_obj.options[sel_obj.selectedIndex].value;
d3.selectAll("circle").remove();
d3.csv("data/" + map_date + "_s.csv", function(data) {
	qtyMax = d3.max(data, 
	function(d){
		return d.qty++;
	});
	data.forEach(
		function(d){ 
			push_val = [];
			push_val[0] = d.highway;
			push_val[1] = d.checkpoint;
			push_val[2] = d.point_num;
			push_val[3] = d.latitude;
			push_val[4] = d.longitude;
			push_val[5] = d.qty;
			data_arr.push(push_val);
			draw_points(d.highway, d.checkpoint, d.point_num, d.latitude, d.longitude, d.qty);
		}
	);
});
}

var drag = d3.behavior.drag()
	.on("dragstart", draginit)
	.on("drag", dragmove)
	.on("dragend", dragfinish);

function dragmove() {
	//drag_mode = 1;
	coordinates = d3.mouse(this);
	var x_m = coordinates[0];
	var y_m = coordinates[1];
	obj_i = id_str.split("_");
	var p = 1;
	/* sort array */
	//sel_data_arr.sort(function(a, b) {
    //	return b[5] - a[5];
	//});
	for (var h = 0; h < sel_data_arr.length; h++) {
		if (sel_data_arr[h][0] == obj_i[0] && sel_data_arr[h][2] == obj_i[1]) {
			cur_x = d3.event.x;
			cur_y = d3.event.y;
			p = p - 1;
		} else {
			cur_x = d3.event.x + 120 * Math.cos((360 * Math.PI / 180) / (sel_data_arr.length - 1) * (p - 1));
			cur_y = d3.event.y + 120 * Math.sin((360 * Math.PI / 180) / (sel_data_arr.length - 1) * (p - 1));
			tmp = 360 / sel_data_arr.length * (p - 1);
		}
		d3.select("#" + sel_data_arr[h][0] + "_" + sel_data_arr[h][2])
			.attr("cx", cur_x)
			.attr("cy", cur_y);

	d_box_x = svg.select("#b_chart_rect").attr("x");
	d_box_w = svg.select("#b_chart_rect").attr("width");
	d_box_y = svg.select("#b_chart_rect").attr("y");
	d_box_h = svg.select("#b_chart_rect").attr("height");
	
	if (x_m > d_box_x && x_m < (d_box_x + d_box_w) && y_m > d_box_y && y_m < (d_box_y + d_box_h) && svg.select("#rect_over").style("fill") != "red") {
		svg.select("#rect_over").style("fill", "red");
	} else {
		svg.select("#rect_over").style("fill", "none");
	}
		p++;
	}
}

function dragfinish() {
	coordinates = d3.mouse(this);
	var x_m = coordinates[0];
	var y_m = coordinates[1];
	d_box_x = svg.select("#b_chart_rect").attr("x");
	d_box_w = svg.select("#b_chart_rect").attr("width");
	d_box_y = svg.select("#b_chart_rect").attr("y");
	d_box_h = svg.select("#b_chart_rect").attr("height");
	if (x_m > d_box_x && x_m < (d_box_x + d_box_w) && y_m > d_box_y && y_m < (d_box_y + d_box_h)) {
		var x_coord = d_box_x*1 - d_box_w * 1 / sel_data_arr.length;
		for (var h = 0; h < sel_data_arr.length; h++) {
			lat = y_lat * (aus_lat_u - sel_data_arr[h][3]) + pol_min_y;
			lon = x_lon * (sel_data_arr[h][4] - aus_lon_l) + pol_min_x;
			x_coord = x_coord * 1 + (d_box_w * 1 - 70) / sel_data_arr.length;
			y_coord = d_box_y * 1 + d_box_h*1;

			if (d3.select("#b_chart_id") != "") {
				d3.select("#b_chart_id").remove();
			}

			svg.select("#" + sel_data_arr[h][0] + "_" + sel_data_arr[h][2])
				.transition()
					.duration(380)
						.attr("cx", x_coord + 110)
						.attr("cy", y_coord - 50)
				.transition()
					.duration(250)
						.style("opacity", 0);
		}
		setTimeout(function(){
			for (var h = 0; h < sel_data_arr.length; h++) {
				lat = y_lat * (aus_lat_u - sel_data_arr[h][3]) + pol_min_y;
				lon = x_lon * (sel_data_arr[h][4] - aus_lon_l) + pol_min_x;
				svg.select("#" + sel_data_arr[h][0] + "_" + sel_data_arr[h][2])
					.attr("cx", lon)
					.attr("cy", lat);
			}
			for (var h = 0; h < sel_data_arr.length; h++) {
				svg.select("#" + sel_data_arr[h][0] + "_" + sel_data_arr[h][2])
					.transition()
						.duration(500)
							.style("opacity", 1);
			}
			b_chart_arr = sel_data_arr;
			init_b_chart();
		}, 640);

	} else {
		for (var h = 0; h < sel_data_arr.length; h++) {
			lat = y_lat * (aus_lat_u - sel_data_arr[h][3]) + pol_min_y;
			lon = x_lon * (sel_data_arr[h][4] - aus_lon_l) + pol_min_x;
			svg.select("#" + sel_data_arr[h][0] + "_" + sel_data_arr[h][2])
				.transition()
					.duration(500)
						.attr("cx", lon)
						.attr("cy", lat);
			
		}
	}
	setTimeout(function(){
		drag_mode = 0;
		if (d3.select("#b_chart_id") != "") {
			svg.select("#b_chart_rect").style("visibility", "visible");
			svg.select("#b_chart_close").style("visibility", "visible");
			svg.select("#rect_over").style("visibility", "visible");
			svg.select("#close_line_1").style("visibility", "visible");
			svg.select("#close_line_2").style("visibility", "visible");
			svg.select("#ch_hwy_txt")
				.text(svg.select("#hwy_txt").text());
			svg.select("#ch_hwy_txt")
				.transition()
   					.duration(400)
						.style("opacity", 1);
			svg.select("#ch_hwy_point_txt")
				.text(svg.select("#hwy_point_txt").text());
			if (b_chart_arr.length == 1) {
				svg.select("#ch_hwy_point_txt")
					.transition()
   						.duration(400)
							.style("opacity", 1);
				svg.select("#ch_hwy_line")
					.attr("x2", svg.select("#ch_hwy_point_txt").node().getComputedTextLength()*1 + 14)
				svg.select("#ch_hwy_line")
					.transition()
   						.duration(400)
							.style("stroke-opacity", 1);
			} else {
				svg.select("#ch_hwy_point_txt")
					.transition()
   						.duration(400)
							.style("opacity", 0);
				svg.select("#ch_hwy_line")
					.attr("x2", svg.select("#ch_hwy_point_txt").node().getComputedTextLength()*1 + 14)
				svg.select("#ch_hwy_line")
					.transition()
   						.duration(400)
							.style("stroke-opacity", 0);
			}
		} else {
			svg.select("#b_chart_rect").style("visibility", "hidden");
			svg.select("#b_chart_close").style("visibility", "hidden");
			svg.select("#rect_over").style("fill", "none");
			svg.select("#rect_over").style("visibility", "hidden");
		}
	}, 1000);
}


function draginit() {
	drag_mode = 1;
	svg.select("#b_chart_rect").style("visibility", "visible");
	svg.select("#rect_over").style("visibility", "visible");
	myTrans();
/*	coordinates = d3.mouse(this);
	var x_m = coordinates[0];
	var y_m = coordinates[1];
	obj_i = id_str.split("_");
	for (var h = 0; h < sel_data_arr.length; h++) {
		//if (id_str == sel_data_arr[h][0] + "_" + sel_data_arr[h][2]) {
		if (sel_data_arr[h][0] == obj_i[0] && sel_data_arr[h][2] == obj_i[1]) {
			cur_x = d3.event.x;
			cur_y = d3.event.y;
		} else {
			//document.getElementById("info_div_1").innerHTML = document.getElementById("info_div_1").innerHTML + "<br>" + id_str + "; " + sel_data_arr[h][0] + "_" + sel_data_arr[h][2];
			cur_x = d3.event.x + 200 * Math.cos(360 / sel_data_arr.length * (h - 1));
			cur_y = d3.event.y + 200 * Math.sin(360 / sel_data_arr.length * (h - 1));			
		}
		document.getElementById("info_div_1").innerHTML = cur_x + "; " + cur_y;
		d3.select("#" + sel_data_arr[h][0] + "_" + sel_data_arr[h][2])
			.attr("cx", cur_x)
			.attr("cy", cur_y);
	}
*/}

function myTrans() {
	if (drag_mode == 1) {
		svg.select("#b_chart_rect")
			.transition()
				.duration(555)
					.style("opacity", 0)
      					.each("end", function() {
       						d3.select(this)
								.transition()
									.duration(555)
										.style("opacity", 1)
          									.each("end", function() { myTrans(); });
      					});
	}
}

function init_b_chart() {
	var margin = {top: 10, right: 10, bottom: 35, left: 65},
    	width = d_box_w - margin.left - margin.right,
    	height = d_box_h - margin.top - margin.bottom;

	var svg_ch = d3.select("#svg_div").append("svg")
		.attr("id", "b_chart_id")
		.attr("class", "b_chart_class")
		.attr("width", width + margin.left + margin.right)
		.attr("height", height + margin.top + margin.bottom)
		.style("fill", "red")
		.style("left", d_box_x + "px")
		.style("top", d_box_y + "px")
		.style("position", "absolute");

	var xScale = d3.scale.ordinal()
		.domain(sel_data_arr.map(function(d, i) { return i + 1; }))
		.rangeRoundBands([0, width], .1);
		
	var sel_arr_max = Math.max.apply(null, sel_data_arr.map(function (e) { return e[5]}));

	var yScale = d3.scale.linear()
		//.domain([0, qtyMax])
		.domain([0, sel_arr_max])
		.range([height, 0]);

	var xAxis = d3.svg.axis()
		.scale(xScale)
		.orient("bottom");

	var yAxis = d3.svg.axis()
		.scale(yScale)
		.orient("left")
		.tickSize(-width, 0, 0)
		//.tickFormat("")
		//.innerTickSize(-width)
		.ticks(5);

	var chart = d3.select("#b_chart_id")
		.append("g")
		.attr("transform", "translate(" + margin.left + "," + margin.top + ")");

	var barWidth = width / sel_data_arr.length;
		barWidth = 0.67 * barWidth;

	chart.append("g")
		.attr("class", "x axis")
		.attr("transform", "translate(0," + height + ")")
		.call(xAxis);

	chart.append("g")
		.attr("class", "y axis")
		.call(yAxis);

	chart.append("g")
		.attr("class", "y axis")
		.call(yAxis)
		.append("text")
		.attr("id", "y_txt")
		.attr("transform", "rotate(-90)")
		.attr("y", 4)
		.attr("dy", ".71em")
		.style("text-anchor", "end")
		.style("font-size", "14px")
		.text("average traffic");

	chart.selectAll(".b_chart_bars")
		.data(sel_data_arr)
		.enter().append("rect")
		.attr("id", function(d) {
						return "bar_" + d[0] + "_" + d[2];
					}
				)
		.attr("class", "b_chart_bars")
		.attr("x", function(d, i) {
						return xScale(i + 1);
					}
				)
		.attr("y", function(d) { 
						return height; 
					}
				)

		.attr("height", function(d) { 
			return 0; 
		})
		.attr("width", xScale.rangeBand())
		.on("click", function(d) {
							alert(d[5]);
						}
					)
		.on("mouseover", function(d) {
							d3.select("#" + this.id)
								.transition()
    					 			.duration(220)
										.style("opacity", 0.2);
						}
					)
		.on("mouseout", function(d) {
							d3.select("#" + this.id)
								.transition()
    					 			.duration(220)
										.style("opacity", 1);
						}
					);

	d3.selectAll(".b_chart_bars")
		.transition()
			.duration(500)
				.attr("y", function(d) { 
							return yScale(d[5]); 
						})
				.attr("height", function(d) { 
							return height - yScale(d[5]); 
						})
				.style("opacity", 1);
	esc_pressed();
}
</script>
</head>

<body onload="init_svg()">
<div id="debug_div"></div>
<div id="dates_div">
<table border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td></td>
    <td><b>Beta 1.2</b></td>
    <td width="25"></td>
    <td><a href="summary.html">Summary</a></td>
    <td width="25"></td>
    <td>
<select id="date_sel" onchange="get_data()">
	<option value="Februar_2016">Februar 2016</option>
	<option value="Januar_2016">Januar 2016</option>
</select>
	</td>
    <td></td>
  </tr>
  <tr>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
</table>
</div>
<div id="logo_div"><img src="bilder/logo_2.gif" id="logo_img"></div>
<div id="svg_div"></div>
<div id="map_div"><?php include ('bilder/map.svg'); ?></div>
<!--<div id="austria_map"><img src="bilder/map_with_coord.jpg"></div>-->
<div><img id="map_tmp" src="bilder/tmp.jpg"><div>
<input id="map_but" type="button" style="visibility:hidden" value="Map data" onclick="get_data()">
</body>
</html>
