function collect()
{
	var autobahn = document.getElementById("plusA");
	var tage = document.getElementById("plusT");
	var klasse;
	if (document.getElementById('kfz').checked) 
	{
		klasse = document.getElementById('kfz').value;
	}
	if (document.getElementById('lkw').checked) 
	{
		klasse = document.getElementById('lkw').value;
	}
	if (document.getElementById('pkw').checked) 
	{
		klasse = document.getElementById('pkw').value;
	}
	zeichnen(autobahn.value,tage.value,klasse);
}

function zeichnen(autobahn,tage,klasse)
{
	d3.selectAll("svg > *").remove();
	d3.csv("02_2016_test.csv",function(daten)
	{		
		var zahlen = [];
		daten.forEach(
			function(d){
				if(d.Autobahn == autobahn && d.klasse == klasse && d.Abschnitt == "gesamt")
				{
					switch(true) {
					case tage == 1:
						zahlen.push(d.MoSo);
					break;
					case tage == 2:
						zahlen.push(d.MoFr);
					break;
					case tage == 3:
						zahlen.push(d.DiDo);
					break;
					case tage == 4:
						zahlen.push(d.Mo);
					break;
					case tage == 5:
						zahlen.push(d.Fr);
					break;
					case tage == 6:
						zahlen.push(d.Sa);
					break;
					case tage == 7:
						zahlen.push(d.SonnFeier);
					break;
					default:
					break;
					}
				}
			}
		);
		console.log("Zahl = " + zahlen);
		zahlen.push(250);   //nur für testzwecke
		
		svg.selectAll("rect")
			.data(zahlen)
			.enter()
			.append("rect")
			.attr("x",0)
			.attr("y",function(d,i){return i*21})
			.attr("width",function(d){return d})
			.attr("height",20)
			.attr("fill","red");
	});
}