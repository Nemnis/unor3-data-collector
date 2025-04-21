<!DOCTYPE html>
<html lang="es">
<head>
	<?php include 'web/views/common/head.php' ?>
	<link rel="stylesheet" href="./web/styles/chars.css">
</head>
<body> 
	<div class="fh">
		<!-- Nav -->
		<?php include './web/views/common/nav.php'; ?>

<!-- Title -->
<div class="title_main title_collector p1-rl">
<pre>
________  _________  
\______ \ \_   ___ \ 
 |    |  \/    \  \/ 
 |    `   \     \____
/_______  /\______  /
        \/        \/ 

</pre>
</div>
		<main>
			<!-- Collector -->
			<div class="container-line space-mono-regular">
				<div class="select-wrap">
					<h2>Filtros</h2>

					<div class="show-hum filter">
						<label for="showhum">Humedad </label>
						<input class="styled" onclick="showHum()" type="checkbox" name="showhum" id="showhum" checked>
					</div>
					<div class="show-temp filter">
						<label for="showtemp">Temperatura </label>
						<input class="styled" onclick="showTemp()" type="checkbox" name="showtemp" id="showtemp" checked>
					</div>
					<div class="day-selector filter">
						<label for="data-selector">Días: </label>
						<select id="data-selector">
							<option value="7">7</option>
							<option value="14">14</option>
							<option value="28">28</option>
						</select>
					</div>
				</div>
				<div class="graph-container">
					<div id="char-hum-wrap">
						<h1 class="chart-title">Humedad (Hum %)</h1>
						<svg id="chart-hum" width="600" height="300"></svg>
					</div>

					<div id="char-temp-wrap">
						<h1 class="chart-title">Temperatura (Temp Cº)</h1>
						<svg id="chart-temp" width="600" height="300"></svg>
					</div>
				</div>

				<div class="container-collector-info">	
				<h2>Collector Info</h2>
					<div class="collector-grid">
						<div class="text-content">
							<h3>UNO R3</h3>
							<h4>Microcontroller Board</h4>
							<p>Microcontroller <span>ATmega328P-AU</span></p>
							<p>Operating Voltage <span>5V</span></p>
							<p>Input Voltage (recommended) <span> 7-12V</span></p>
							<p>Input Voltage (limits) <span>6-20V</span></p>
							<p>Digital I/O Pins <span>14 (of which 6 provide PWM output)</span></p>
							<p>Analog Input Pins <span>6</span></p>
							<p>DC Current per I/O Pin <span>40 mA</span></p>
							<p>DC Current for 3.3V Pin <span>50 mA</span></p>
							<p>Flash Memory <span>32 KB of which 0.5 KB used by bootloader</span></p>
							<p>SRAM <span>2 KB</span></p>
							<p>EEPROM <span>1 KB</span></p>
							<p>Clock Speed <span>16 MHz</span></p>
						</div>
						<div class="text-content">
							<h3>DTH 11</h3>
							<h4>Humidity & Temperature Sensor</h4>
							<p>Measurement Range <span>20-90%RH 0-50 ℃</span></p>
							<p>Humidity Accuracy <span>±5％RH</span></p>
							<p>Temperature Accuracy <span> ±2℃</span></p>
							<p>Power Supply (DC) <span>3-5.5V</span></p>
							<p>Current Supply <span>0.5-2.5mA</span></p>
							<p>Sampling period (seconds)<span>1</span></p>
						</div>
					</div>
				</div>

				<div class="container-data">					
				<h3>Ultimos Datos Recogidos</h3>
					<div class="container-data-wrap">
						<div id="datosCsv">
							<div id="dataHum">
								<h5>Humedad %</h1>
							</div>
							<div id="dataTemp">
								<h5>Temperatura Cº</h1>
							</div>
						</div>
					</div>
				</div>
			</div>

			

		</main>
	</div>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/d3/7.9.0/d3.js"></script>
	<script>
		// HTML view element
		const pDataHum = document.getElementById('dataHum');
		const pDataTemp = document.getElementById('dataTemp');
        // config svg
        const margin = { top: 20, right: 30, bottom: 40, left: 40 };
        const width = 350 - margin.left - margin.right;
        const height = 200 - margin.top - margin.bottom;

        // graph creator
        const createBarChart = (data, column, svgId) => {
            const svg = d3.select(svgId)
                .attr("width", width + margin.left + margin.right)
                .attr("height", height + margin.top + margin.bottom)
                .append("g")
                .attr("transform", `translate(${margin.left},${margin.top})`);

            // X
            const x = d3.scaleBand()
                .domain(data.map((d, i) => i)) 
                .range([0, width])
                .padding(0.1);

			// Y
            const y = d3.scaleLinear()
                .domain([0, d3.max(data, d => d[column])]) // Escala basada en la columna
                .nice()
                .range([height, 0]);

            // Axis
            svg.append("g")
                .attr("class", "axis-label")
                .attr("transform", `translate(0,${height})`)
                .call(d3.axisBottom(x).tickFormat(i => `${i + 1}`));

            svg.append("g")
                .attr("class", "axis-label")
                .call(d3.axisLeft(y));

            // Bars
            svg.selectAll(".bar")
                .data(data)
                .enter()
                .append("rect")
                .attr("class", "bar")
                .attr("x", (d, i) => x(i))
                .attr("y", d => y(d[column]))
                .attr("width", x.bandwidth())
                .attr("height", d => height - y(d[column]));
        };

        // load CSV
        d3.csv("./datacollector/data/dataSensors.csv").then(data => {
			console.log(data);

			// data cheker
			data.forEach(d => {
				// Convertir 'Hum' a número
				d.Hum = d.Hum ? parseFloat(d.Hum.trim()) || 0 : 0;
				// Convertir 'Temp' a número
				d.Temp = d.Temp ? parseFloat(d.Temp.trim()) || 0 : 0;

				//console.log("hum: " + d.Hum + " - temp: " + d.Temp);
				
				// Show data on html view
				let newDataHum = document.createElement("p");
				newDataHum.innerText = d.Hum;
				pDataHum.appendChild(newDataHum);

				let newDataTemp = document.createElement("p");
				newDataTemp.innerText = d.Temp;
				pDataTemp.appendChild(newDataTemp);
			});

			// Filter NaN or valids
			const validData = data.filter(d => !isNaN(d.Hum) && !isNaN(d.Temp));

			// refresh graphs with select
			const updateCharts = (numData) => {
				let filteredData;
				if (numData === "all") {
					filteredData = validData;
				} else {
					// Filtra los datos según el valor seleccionado
					filteredData = validData.slice(0, parseInt(numData));
				}

				// Limpiar gráficos existentes
				d3.select("#chart-hum").selectAll("*").remove();
				d3.select("#chart-temp").selectAll("*").remove();

				// Crear los gráficos con los datos filtrados
				createBarChart(filteredData, "Hum", "#chart-hum");
				createBarChart(filteredData, "Temp", "#chart-temp");
			};

			// Gráficos con 7 datos al cargar
			updateCharts("7");

			// Escuchar el cambio en el selector de cantidad de datos
			d3.select("#data-selector").on("change", function() {
				const selectedValue = this.value;
				updateCharts(selectedValue); 
			});
		}).catch(error => {
			console.error("Error al cargar el archivo CSV:", error);
		});
	</script>

	<script>
		function showTemp() {
			if(document.getElementById('showtemp').checked) {
				document.getElementById('char-temp-wrap').classList.remove('hidden');
			} else {
				document.getElementById('char-temp-wrap').classList.add('hidden');
			}
		}

		function showHum(params) {
			if(document.getElementById('showhum').checked) {
				document.getElementById('char-hum-wrap').classList.remove('hidden');
			} else {
				document.getElementById('char-hum-wrap').classList.add('hidden');
			}
		}
	</script>
</body>
</html>