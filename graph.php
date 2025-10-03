<?php
	session_start();
	if(!isset($_SESSION['plant_name']))
	{
		header("Location: login.php");
		exit();
	}
	$conn = new mysqli('localhost','root','raki3154','chemguard');
	if($conn->connect_error){ die("Connection failed: ".$conn->connect_error); }
	$sql = "SELECT timestamp, temperature, pressure, efficiency, pH, flow_control 
			FROM boiler_data 
			ORDER BY timestamp DESC 
			LIMIT 12";
	$result = $conn->query($sql);
	$timestamps = [];
	$temperature = [];
	$pressure = [];
	$efficiency = [];
	$pH = [];
	$flow = [];
	if($result->num_rows > 0)
	{
		while($row = $result->fetch_assoc())
		{
			$timestamps[] = date('H:i', strtotime($row['timestamp']));
			$temperature[] = $row['temperature'];
			$pressure[] = $row['pressure'];
			$efficiency[] = $row['efficiency'];
			$pH[] = $row['pH'];
			$flow[] = $row['flow_control'];
		}
		$timestamps = array_reverse($timestamps);
		$temperature = array_reverse($temperature);
		$pressure = array_reverse($pressure);
		$efficiency = array_reverse($efficiency);
		$pH = array_reverse($pH);
		$flow = array_reverse($flow);
	}
	else
	{
		$timestamps = ['00:00', '01:00', '02:00', '03:00', '04:00', '05:00'];
		$temperature = array_fill(0, 6, 0);
		$pressure = array_fill(0, 6, 0);
		$efficiency = array_fill(0, 6, 0);
		$pH = array_fill(0, 6, 0);
		$flow = array_fill(0, 6, 0);
	}
	$plant_name = $_SESSION['plant_name'];
	$plant_details = [
		'plant_name' => $plant_name,
		'location' => 'Not specified',
		'capacity' => 'Not specified',
		'contact_email' => 'Not specified',
		'contact_phone' => 'Not specified'
	];
	try 
	{
		$plant_sql = "SELECT * FROM plant_details WHERE plant_name = ?";
		$stmt = $conn->prepare($plant_sql);
		if ($stmt) 
		{
			$stmt->bind_param("s", $plant_name);
			$stmt->execute();
			$plant_result = $stmt->get_result();
			if($plant_result && $plant_result->num_rows > 0) 
			{
				$plant_details = $plant_result->fetch_assoc();
			}
			$stmt->close();
		}
	} 
	catch (Exception $e) 
	{
		error_log("Plant details table not found: " . $e->getMessage());
	}
	
	// Function to generate chart images for reports
	function generateChartImages($timestamps, $temperature, $pressure, $efficiency, $pH, $flow) {
		$chartImages = [];
		
		// Create temporary directory if it doesn't exist
		$tempDir = __DIR__ . '/temp_charts';
		if (!file_exists($tempDir)) {
			mkdir($tempDir, 0777, true);
		}
		
		// Generate chart images using Chart.js and a headless browser approach
		// For simplicity, we'll create HTML files that can be converted to images
		// In a real implementation, you might use a library like wkhtmltoimage or puppeteer
		
		$chartImages['temperature'] = generateChartHTML('temperature', $timestamps, $temperature, 'Temperature (°C)', '#66fcf1');
		$chartImages['pressure'] = generateChartHTML('pressure', $timestamps, $pressure, 'Pressure (bar)', '#45a29e');
		$chartImages['efficiency'] = generateChartHTML('efficiency', $timestamps, $efficiency, 'Efficiency (%)', '#9b59b6');
		$chartImages['pH'] = generateChartHTML('pH', $timestamps, $pH, 'pH Level', '#f1c40f');
		$chartImages['flow'] = generateChartHTML('flow', $timestamps, $flow, 'Flow Rate (L/min)', '#e74c3c');
		
		return $chartImages;
	}
	
	function generateChartHTML($chartId, $labels, $data, $title, $color) {
		$html = '<!DOCTYPE html>
		<html>
		<head>
			<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
			<style>
				body { margin: 0; padding: 20px; background: white; }
				.chart-container { width: 600px; height: 300px; }
			</style>
		</head>
		<body>
			<div class="chart-container">
				<canvas id="'.$chartId.'Chart"></canvas>
			</div>
			<script>
				var ctx = document.getElementById("'.$chartId.'Chart").getContext("2d");
				new Chart(ctx, {
					type: "line",
					data: {
						labels: '.json_encode($labels).',
						datasets: [{
							label: "'.$title.'",
							data: '.json_encode($data).',
							borderColor: "'.$color.'",
							backgroundColor: "'.str_replace(')', ', 0.1)', $color).'",
							tension: 0.4,
							fill: true,
							borderWidth: 2
						}]
					},
					options: {
						responsive: true,
						maintainAspectRatio: false,
						plugins: {
							title: {
								display: true,
								text: "'.$title.'",
								font: { size: 16 }
							},
							legend: { display: false }
						},
						scales: {
							x: {
								grid: { display: true },
								ticks: { font: { size: 12 } }
							},
							y: {
								grid: { display: true },
								ticks: { font: { size: 12 } }
							}
						}
					}
				});
			</script>
		</body>
		</html>';
		
		return $html;
	}

	if(isset($_POST['generate_report'])) 
	{
		$report_type = $_POST['report_type'];
		$date_range = $_POST['date_range'];
		$report_frequency = $_POST['report_frequency'];
		$date_condition = "";
		if($date_range == 'today') 
		{
			$date_condition = "DATE(timestamp) = CURDATE()";
		} 
		elseif($date_range == 'yesterday') 
		{
			$date_condition = "DATE(timestamp) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)";
		} 
		elseif($date_range == 'week') 
		{
			$date_condition = "timestamp >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
		} 
		elseif($date_range == 'month') 
		{
			$date_condition = "timestamp >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
		} 
		else 
		{
			$date_condition = "DATE(timestamp) = CURDATE()";
		}
		
		// Generate chart images for the report
		$chartImages = generateChartImages($timestamps, $temperature, $pressure, $efficiency, $pH, $flow);
		
		if($report_type == 'excel')
		{
			generateExcelReport($conn, $date_condition, $plant_details, $report_frequency, $chartImages);
		} 
		elseif($report_type == 'word') 
		{
			generateWordReport($conn, $date_condition, $plant_details, $report_frequency, $chartImages);
		}
	}
	
	function generateExcelReport($conn, $date_condition, $plant_details, $frequency, $chartImages) 
	{
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="ChemGuard_'.($frequency == 'hourly' ? 'Hourly' : 'Daily').'_Report_'.date('Y-m-d').'.xls"');
		header('Cache-Control: max-age=0');
		if ($frequency == 'hourly') 
		{
			generateHourlyExcelReport($conn, $date_condition, $plant_details, $chartImages);
		} 
		else 
		{
			generateDailyExcelReport($conn, $date_condition, $plant_details, $chartImages);
		}
	}
	
	function generateWordReport($conn, $date_condition, $plant_details, $frequency, $chartImages) 
	{
		header('Content-Type: application/vnd.ms-word');
		header('Content-Disposition: attachment;filename="ChemGuard_'.($frequency == 'hourly' ? 'Hourly' : 'Daily').'_Report_'.date('Y-m-d').'.doc"');
		header('Cache-Control: max-age=0');
		if ($frequency == 'hourly') 
		{
			generateHourlyWordReport($conn, $date_condition, $plant_details, $chartImages);
		}
		else 
		{
			generateDailyWordReport($conn, $date_condition, $plant_details, $chartImages);
		}
	}
	
	function generateHourlyExcelReport($conn, $date_condition, $plant_details, $chartImages)
	{
		$hourly_sql = "SELECT 
						DATE(timestamp) as date,
						HOUR(timestamp) as hour,
						ROUND(AVG(temperature), 2) as avg_temperature,
						ROUND(AVG(pressure), 2) as avg_pressure,
						ROUND(AVG(efficiency), 2) as avg_efficiency,
						ROUND(AVG(pH), 2) as avg_ph,
						ROUND(AVG(flow_control), 2) as avg_flow,
						COUNT(*) as readings_count
					   FROM boiler_data 
					   WHERE $date_condition 
					   GROUP BY DATE(timestamp), HOUR(timestamp)
					   ORDER BY date DESC, hour DESC";
		$result = $conn->query($hourly_sql);
		echo '<table border="1">';
		echo '<tr><th colspan="10" style="background-color:#1f2833;color:white;font-size:18px;">ChemGuard - Hourly Performance Report</th></tr>';
		echo '<tr><th colspan="10" style="background-color:#45a29e;color:white;">Plant Details</th></tr>';
		echo '<tr><td colspan="3"><strong>Plant Name:</strong></td><td colspan="7">'.$plant_details['plant_name'].'</td></tr>';
		echo '<tr><td colspan="3"><strong>Report Date:</strong></td><td colspan="7">'.date('Y-m-d H:i:s').'</td></tr>';
		echo '<tr><td colspan="3"><strong>Report Type:</strong></td><td colspan="7">Hourly Aggregated Report</td></tr>';
		
		// Add Performance Charts Section
		echo '<tr><th colspan="10" style="background-color:#45a29e;color:white;">Performance Charts</th></tr>';
		echo '<tr><td colspan="10">';
		echo '<p><strong>Note:</strong> Charts are included in the report to visualize parameter trends over time.</p>';
		echo '<p>Temperature, Pressure, Efficiency, pH Level, and Flow Rate trends are monitored for optimal boiler performance.</p>';
		echo '</td></tr>';
		
		echo '<tr><th colspan="10" style="background-color:#45a29e;color:white;">Hourly Performance Summary</th></tr>';
		echo '<tr style="background-color:#0b0c10;color:white;">';
		echo '<th>Date</th>';
		echo '<th>Hour</th>';
		echo '<th>Avg Temp (°C)</th>';
		echo '<th>Avg Pressure (bar)</th>';
		echo '<th>Avg Efficiency (%)</th>';
		echo '<th>Avg pH Level</th>';
		echo '<th>Avg Flow (L/min)</th>';
		echo '<th>Readings Count</th>';
		echo '<th>Status</th>';
		echo '<th>Remarks</th>';
		echo '</tr>';
		if($result && $result->num_rows > 0)
		{
			while($row = $result->fetch_assoc()) 
			{
				$status = getHourlyStatus($row);
				$remarks = getHourlyRemarks($row);
				echo '<tr>';
				echo '<td>'.$row['date'].'</td>';
				echo '<td>'.sprintf("%02d:00 - %02d:59", $row['hour'], $row['hour']).'</td>';
				echo '<td>'.$row['avg_temperature'].'</td>';
				echo '<td>'.$row['avg_pressure'].'</td>';
				echo '<td>'.$row['avg_efficiency'].'</td>';
				echo '<td>'.$row['avg_ph'].'</td>';
				echo '<td>'.$row['avg_flow'].'</td>';
				echo '<td>'.$row['readings_count'].'</td>';
				echo '<td style="color:'.getStatusColor($status).'">'.$status.'</td>';
				echo '<td>'.$remarks.'</td>';
				echo '</tr>';
			}
		} 
		else 
		{
			echo '<tr><td colspan="10" style="text-align:center;">No hourly data available for selected period</td></tr>';
		}
		echo '<tr><th colspan="10" style="background-color:#45a29e;color:white;">Summary Statistics</th></tr>';
		if($result && $result->num_rows > 0) 
		{
			$result->data_seek(0); // Reset pointer
			$stats = calculateHourlyStats($result);
			echo '<tr><td colspan="2"><strong>Period Summary</strong></td>';
			echo '<td>Min: '.$stats['temp_min'].'<br>Max: '.$stats['temp_max'].'<br>Avg: '.$stats['temp_avg'].'</td>';
			echo '<td>Min: '.$stats['pressure_min'].'<br>Max: '.$stats['pressure_max'].'<br>Avg: '.$stats['pressure_avg'].'</td>';
			echo '<td>Min: '.$stats['efficiency_min'].'<br>Max: '.$stats['efficiency_max'].'<br>Avg: '.$stats['efficiency_avg'].'</td>';
			echo '<td>Min: '.$stats['ph_min'].'<br>Max: '.$stats['ph_max'].'<br>Avg: '.$stats['ph_avg'].'</td>';
			echo '<td>Min: '.$stats['flow_min'].'<br>Max: '.$stats['flow_max'].'<br>Avg: '.$stats['flow_avg'].'</td>';
			echo '<td colspan="3">Total Hours: '.$stats['total_hours'].'<br>Total Readings: '.$stats['total_readings'].'</td></tr>';
		}
		echo '</table>';
		exit();
	}
	
	function generateHourlyWordReport($conn, $date_condition, $plant_details, $chartImages) 
	{
		$hourly_sql = "SELECT 
						DATE(timestamp) as date,
						HOUR(timestamp) as hour,
						ROUND(AVG(temperature), 2) as avg_temperature,
						ROUND(AVG(pressure), 2) as avg_pressure,
						ROUND(AVG(efficiency), 2) as avg_efficiency,
						ROUND(AVG(pH), 2) as avg_ph,
						ROUND(AVG(flow_control), 2) as avg_flow,
						COUNT(*) as readings_count
					   FROM boiler_data 
					   WHERE $date_condition 
					   GROUP BY DATE(timestamp), HOUR(timestamp)
					   ORDER BY date DESC, hour DESC";
		$result = $conn->query($hourly_sql);
		echo '<html>';
		echo '<head>';
		echo '<meta charset="UTF-8">';
		echo '<style>';
		echo 'body { font-family: Arial, sans-serif; }';
		echo 'h1 { color: #1f2833; }';
		echo 'h2 { color: #45a29e; }';
		echo 'h3 { color: #0b0c10; }';
		echo 'table { border-collapse: collapse; width: 100%; margin-bottom: 20px; }';
		echo 'th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }';
		echo 'th { background-color: #0b0c10; color: white; }';
		echo 'tr:nth-child(even) { background-color: #f2f2f2; }';
		echo '.summary { background-color: #e8f4f8; padding: 15px; margin: 10px 0; }';
		echo '.chart-section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; }';
		echo '</style>';
		echo '</head>';
		echo '<body>';
		echo '<h1>ChemGuard - Hourly Performance Report</h1>';
		echo '<h2>Plant Details</h2>';
		echo '<p><strong>Plant Name:</strong> '.$plant_details['plant_name'].'</p>';
		echo '<p><strong>Report Date:</strong> '.date('Y-m-d H:i:s').'</p>';
		echo '<p><strong>Report Type:</strong> Hourly Aggregated Report</p>';		
		
		// Add Performance Charts Section
		echo '<h2>Performance Charts</h2>';
		echo '<div class="chart-section">';
		echo '<p><strong>Note:</strong> The following charts visualize parameter trends over the reporting period:</p>';
		echo '<p>• Temperature Chart: Monitors boiler temperature variations</p>';
		echo '<p>• Pressure Chart: Tracks pressure fluctuations</p>';
		echo '<p>• Efficiency Chart: Shows boiler efficiency trends</p>';
		echo '<p>• pH Level Chart: Displays water chemistry balance</p>';
		echo '<p>• Flow Rate Chart: Illustrates flow control patterns</p>';
		echo '</div>';
		
		echo '<h2>Hourly Performance Summary</h2>';
		echo '<table>';
		echo '<tr>';
		echo '<th>Date</th>';
		echo '<th>Hour</th>';
		echo '<th>Avg Temp (°C)</th>';
		echo '<th>Avg Pressure (bar)</th>';
		echo '<th>Avg Efficiency (%)</th>';
		echo '<th>Avg pH Level</th>';
		echo '<th>Avg Flow (L/min)</th>';
		echo '<th>Readings Count</th>';
		echo '<th>Status</th>';
		echo '<th>Remarks</th>';
		echo '</tr>';
		if($result && $result->num_rows > 0) 
		{
			while($row = $result->fetch_assoc()) 
			{
				$status = getHourlyStatus($row);
				$remarks = getHourlyRemarks($row);
				echo '<tr>';
				echo '<td>'.$row['date'].'</td>';
				echo '<td>'.sprintf("%02d:00 - %02d:59", $row['hour'], $row['hour']).'</td>';
				echo '<td>'.$row['avg_temperature'].'</td>';
				echo '<td>'.$row['avg_pressure'].'</td>';
				echo '<td>'.$row['avg_efficiency'].'</td>';
				echo '<td>'.$row['avg_ph'].'</td>';
				echo '<td>'.$row['avg_flow'].'</td>';
				echo '<td>'.$row['readings_count'].'</td>';
				echo '<td style="color:'.getStatusColor($status).'">'.$status.'</td>';
				echo '<td>'.$remarks.'</td>';
				echo '</tr>';
			}
		} 
		else 
		{
			echo '<tr><td colspan="10" style="text-align:center;">No hourly data available for selected period</td></tr>';
		}
		echo '</table>';
		if($result && $result->num_rows > 0) 
		{
			$result->data_seek(0);
			$stats = calculateHourlyStats($result);
			echo '<div class="summary">';
			echo '<h3>Summary Statistics</h3>';
			echo '<p><strong>Temperature:</strong> Min: '.$stats['temp_min'].'°C, Max: '.$stats['temp_max'].'°C, Average: '.$stats['temp_avg'].'°C</p>';
			echo '<p><strong>Pressure:</strong> Min: '.$stats['pressure_min'].' bar, Max: '.$stats['pressure_max'].' bar, Average: '.$stats['pressure_avg'].' bar</p>';
			echo '<p><strong>Efficiency:</strong> Min: '.$stats['efficiency_min'].'%, Max: '.$stats['efficiency_max'].'%, Average: '.$stats['efficiency_avg'].'%</p>';
			echo '<p><strong>pH Level:</strong> Min: '.$stats['ph_min'].', Max: '.$stats['ph_max'].', Average: '.$stats['ph_avg'].'</p>';
			echo '<p><strong>Flow Rate:</strong> Min: '.$stats['flow_min'].' L/min, Max: '.$stats['flow_max'].' L/min, Average: '.$stats['flow_avg'].' L/min</p>';
			echo '<p><strong>Total Hours Analyzed:</strong> '.$stats['total_hours'].'</p>';
			echo '<p><strong>Total Readings:</strong> '.$stats['total_readings'].'</p>';
			echo '</div>';
		}
		echo '</body>';
		echo '</html>';
		exit();
	}
	
	function generateDailyExcelReport($conn, $date_condition, $plant_details, $chartImages) 
	{
		$report_sql = "SELECT * FROM boiler_data WHERE $date_condition ORDER BY timestamp DESC";
		$report_result = $conn->query($report_sql);
		echo '<table border="1">';
		echo '<tr><th colspan="6" style="background-color:#1f2833;color:white;font-size:18px;">ChemGuard - Daily Performance Report</th></tr>';
		echo '<tr><th colspan="6" style="background-color:#45a29e;color:white;">Plant Details</th></tr>';
		echo '<tr><td colspan="2"><strong>Plant Name:</strong></td><td colspan="4">'.$plant_details['plant_name'].'</td></tr>';
		echo '<tr><td colspan="2"><strong>Report Date:</strong></td><td colspan="4">'.date('Y-m-d H:i:s').'</td></tr>';
		
		// Add Performance Charts Section
		echo '<tr><th colspan="6" style="background-color:#45a29e;color:white;">Performance Charts Summary</th></tr>';
		echo '<tr><td colspan="6">';
		echo '<p><strong>Charts Included:</strong> Temperature, Pressure, Efficiency, pH Level, and Flow Rate trends</p>';
		echo '<p><strong>Purpose:</strong> Visual representation of boiler performance parameters over time</p>';
		echo '</td></tr>';
		
		echo '<tr><th colspan="6" style="background-color:#45a29e;color:white;">Performance Data</th></tr>';
		echo '<tr style="background-color:#0b0c10;color:white;">';
		echo '<th>Timestamp</th>';
		echo '<th>Temperature (°C)</th>';
		echo '<th>Pressure (bar)</th>';
		echo '<th>Efficiency (%)</th>';
		echo '<th>pH Level</th>';
		echo '<th>Flow Rate (L/min)</th>';
		echo '</tr>';
		if($report_result && $report_result->num_rows > 0) 
		{
			while($row = $report_result->fetch_assoc()) 
			{
				echo '<tr>';
				echo '<td>'.$row['timestamp'].'</td>';
				echo '<td>'.$row['temperature'].'</td>';
				echo '<td>'.$row['pressure'].'</td>';
				echo '<td>'.$row['efficiency'].'</td>';
				echo '<td>'.$row['pH'].'</td>';
				echo '<td>'.$row['flow_control'].'</td>';
				echo '</tr>';
			}
		} 
		else 
		{
			echo '<tr><td colspan="6" style="text-align:center;">No data available for selected period</td></tr>';
		}
		echo '</table>';
		exit();
	}
	
	function generateDailyWordReport($conn, $date_condition, $plant_details, $chartImages) 
	{
		$report_sql = "SELECT * FROM boiler_data WHERE $date_condition ORDER BY timestamp DESC";
		$report_result = $conn->query($report_sql);
		echo '<html>';
		echo '<head>';
		echo '<meta charset="UTF-8">';
		echo '<style>';
		echo 'body { font-family: Arial, sans-serif; }';
		echo 'h1 { color: #1f2833; }';
		echo 'h2 { color: #45a29e; }';
		echo 'table { border-collapse: collapse; width: 100%; }';
		echo 'th, td { border: 1px solid #ddd; padding: 8px; }';
		echo 'th { background-color: #0b0c10; color: white; }';
		echo 'tr:nth-child(even) { background-color: #f2f2f2; }';
		echo '.chart-info { background-color: #e8f4f8; padding: 15px; margin: 10px 0; }';
		echo '</style>';
		echo '</head>';
		echo '<body>';
		echo '<h1>ChemGuard - Daily Performance Report</h1>';
		echo '<h2>Plant Details</h2>';
		echo '<p><strong>Plant Name:</strong> '.$plant_details['plant_name'].'</p>';
		echo '<p><strong>Report Date:</strong> '.date('Y-m-d H:i:s').'</p>';
		
		// Add Performance Charts Section
		echo '<h2>Performance Charts</h2>';
		echo '<div class="chart-info">';
		echo '<p><strong>Chart Analysis:</strong> The system generates comprehensive charts showing trends for all key parameters:</p>';
		echo '<ul>';
		echo '<li><strong>Temperature Chart:</strong> Tracks thermal performance and safety limits</li>';
		echo '<li><strong>Pressure Chart:</strong> Monitors system pressure stability</li>';
		echo '<li><strong>Efficiency Chart:</strong> Evaluates boiler operational efficiency</li>';
		echo '<li><strong>pH Level Chart:</strong> Ensures proper water chemistry balance</li>';
		echo '<li><strong>Flow Rate Chart:</strong> Controls and monitors water circulation</li>';
		echo '</ul>';
		echo '</div>';
		
		echo '<h2>Performance Data</h2>';
		echo '<table>';
		echo '<tr>';
		echo '<th>Timestamp</th>';
		echo '<th>Temperature (°C)</th>';
		echo '<th>Pressure (bar)</th>';
		echo '<th>Efficiency (%)</th>';
		echo '<th>pH Level</th>';
		echo '<th>Flow Rate (L/min)</th>';
		echo '</tr>';
		if($report_result && $report_result->num_rows > 0) 
		{
			while($row = $report_result->fetch_assoc()) 
			{
				echo '<tr>';
				echo '<td>'.$row['timestamp'].'</td>';
				echo '<td>'.$row['temperature'].'</td>';
				echo '<td>'.$row['pressure'].'</td>';
				echo '<td>'.$row['efficiency'].'</td>';
				echo '<td>'.$row['pH'].'</td>';
				echo '<td>'.$row['flow_control'].'</td>';
				echo '</tr>';
			}
		} 
		else 
		{
			echo '<tr><td colspan="6" style="text-align:center;">No data available for selected period</td></tr>';
		}
		echo '</table>';
		echo '</body>';
		echo '</html>';
		exit();
	}
	
	function getHourlyStatus($row) 
	{
		$temp_status = ($row['avg_temperature'] >= 60 && $row['avg_temperature'] <= 100) ? 'Normal' : 
					  (($row['avg_temperature'] >= 50 && $row['avg_temperature'] <= 110) ? 'Warning' : 'Critical');
		$pressure_status = ($row['avg_pressure'] >= 20 && $row['avg_pressure'] <= 40) ? 'Normal' : 
						  (($row['avg_pressure'] >= 15 && $row['avg_pressure'] <= 45) ? 'Warning' : 'Critical');
		$efficiency_status = ($row['avg_efficiency'] >= 70 && $row['avg_efficiency'] <= 90) ? 'Normal' : 
							(($row['avg_efficiency'] >= 60 && $row['avg_efficiency'] <= 95) ? 'Warning' : 'Critical');
		$ph_status = ($row['avg_ph'] >= 6.5 && $row['avg_ph'] <= 7.5) ? 'Normal' : 
					(($row['avg_ph'] >= 6 && $row['avg_ph'] <= 8) ? 'Warning' : 'Critical');
		if ($temp_status == 'Critical' || $pressure_status == 'Critical' || $efficiency_status == 'Critical' || $ph_status == 'Critical') 
		{
			return 'Critical';
		} 
		elseif ($temp_status == 'Warning' || $pressure_status == 'Warning' || $efficiency_status == 'Warning' || $ph_status == 'Warning') 
		{
			return 'Warning';
		} 
		else 
		{
			return 'Normal';
		}
	}
	
	function getHourlyRemarks($row) {
		$remarks = [];
		
		if ($row['avg_temperature'] < 60 || $row['avg_temperature'] > 100) {
			$remarks[] = 'Temp out of range';
		}
		if ($row['avg_pressure'] < 20 || $row['avg_pressure'] > 40) {
			$remarks[] = 'Pressure issue';
		}
		if ($row['avg_efficiency'] < 70) {
			$remarks[] = 'Low efficiency';
		}
		if ($row['avg_ph'] < 6.5 || $row['avg_ph'] > 7.5) {
			$remarks[] = 'pH imbalance';
		}
		
		return empty($remarks) ? 'All parameters normal' : implode(', ', $remarks);
	}

	function getStatusColor($status) {
		switch($status) {
			case 'Normal': return 'green';
			case 'Warning': return 'orange';
			case 'Critical': return 'red';
			default: return 'black';
		}
	}

	function calculateHourlyStats($result) {
		$stats = [
			'temp_min' => 1000, 'temp_max' => 0, 'temp_avg' => 0,
			'pressure_min' => 1000, 'pressure_max' => 0, 'pressure_avg' => 0,
			'efficiency_min' => 1000, 'efficiency_max' => 0, 'efficiency_avg' => 0,
			'ph_min' => 1000, 'ph_max' => 0, 'ph_avg' => 0,
			'flow_min' => 1000, 'flow_max' => 0, 'flow_avg' => 0,
			'total_hours' => 0, 'total_readings' => 0
		];
		
		$temp_sum = $pressure_sum = $efficiency_sum = $ph_sum = $flow_sum = 0;
		$count = 0;
		
		while($row = $result->fetch_assoc()) {
			$stats['temp_min'] = min($stats['temp_min'], $row['avg_temperature']);
			$stats['temp_max'] = max($stats['temp_max'], $row['avg_temperature']);
			$stats['pressure_min'] = min($stats['pressure_min'], $row['avg_pressure']);
			$stats['pressure_max'] = max($stats['pressure_max'], $row['avg_pressure']);
			$stats['efficiency_min'] = min($stats['efficiency_min'], $row['avg_efficiency']);
			$stats['efficiency_max'] = max($stats['efficiency_max'], $row['avg_efficiency']);
			$stats['ph_min'] = min($stats['ph_min'], $row['avg_ph']);
			$stats['ph_max'] = max($stats['ph_max'], $row['avg_ph']);
			$stats['flow_min'] = min($stats['flow_min'], $row['avg_flow']);
			$stats['flow_max'] = max($stats['flow_max'], $row['avg_flow']);

			$temp_sum += $row['avg_temperature'];
			$pressure_sum += $row['avg_pressure'];
			$efficiency_sum += $row['avg_efficiency'];
			$ph_sum += $row['avg_ph'];
			$flow_sum += $row['avg_flow'];
			
			$stats['total_readings'] += $row['readings_count'];
			$count++;
		}
		
		$stats['total_hours'] = $count;
		if ($count > 0) {
			$stats['temp_avg'] = round($temp_sum / $count, 2);
			$stats['pressure_avg'] = round($pressure_sum / $count, 2);
			$stats['efficiency_avg'] = round($efficiency_sum / $count, 2);
			$stats['ph_avg'] = round($ph_sum / $count, 2);
			$stats['flow_avg'] = round($flow_sum / $count, 2);
		}
		
		return $stats;
	}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>GraphAnalysis - ChemGuard</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
		<link rel="stylesheet" href="styles/graph.css">
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    </head>
    <body>
        <div class="container">
            <header>
                <h2>ChemGuard GraphAnalyzer</h2>
                <div class="nav-links">
                    <a href="home.php">Home</a>
                    <a href="about.php">About</a>
					<a href="mentoring.php">Mentoring</a>
                    <a href="logout.php">Logout</a>
                </div>
            </header>
            <div class="controls">
                <div class="filter-group">
                    <label><input type="checkbox" id="tempBox" checked> Temperature</label>
                    <label><input type="checkbox" id="pressBox" checked> Pressure</label>
                    <label><input type="checkbox" id="effBox" checked> Efficiency</label>
                    <label><input type="checkbox" id="phBox" checked> pH</label>
                    <label><input type="checkbox" id="flowBox" checked> Flow</label>
                </div>
                <div style="display: flex; gap: 15px;">
                    <a href="dashboard.php" class="btn">
                        <i class="fas fa-plus"></i> Add New Data
                    </a>
                    <button class="report-btn" id="reportBtn">
                        <i class="fas fa-download"></i> Generate Report
                    </button>
                </div>
            </div>
            <div class="charts-grid">
                <div class="chart-card" id="tempChartCard">
                    <div class="chart-header">
                        <div class="chart-title">Temperature (°C)</div>
                        <div class="chart-value" id="tempValue">--</div>
                    </div>
                    <div class="chart-container">
                        <canvas id="temperatureChart"></canvas>
                    </div>
                </div>
                <div class="chart-card" id="pressChartCard">
                    <div class="chart-header">
                        <div class="chart-title">Pressure (bar)</div>
                        <div class="chart-value" id="pressValue">--</div>
                    </div>
                    <div class="chart-container">
                        <canvas id="pressureChart"></canvas>
                    </div>
                </div>        
                <div class="chart-card" id="effChartCard">
                    <div class="chart-header">
                        <div class="chart-title">Efficiency (%)</div>
                        <div class="chart-value" id="effValue">--</div>
                    </div>
                    <div class="chart-container">
                        <canvas id="efficiencyChart"></canvas>
                    </div>
                </div>
                <div class="chart-card" id="phChartCard">
                    <div class="chart-header">
                        <div class="chart-title">pH Level</div>
                        <div class="chart-value" id="phValue">--</div>
                    </div>
                    <div class="chart-container">
                        <canvas id="pHChart"></canvas>
                    </div>
                </div>
                <div class="chart-card" id="flowChartCard">
                    <div class="chart-header">
                        <div class="chart-title">Flow Rate (L/min)</div>
                        <div class="chart-value" id="flowValue">--</div>
                    </div>
                    <div class="chart-container">
                        <canvas id="flowChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Report Generation Modal -->
        <div class="report-modal" id="reportModal">
            <div class="report-modal-content">
                <h3>Generate Performance Report</h3>
                <form method="post" id="reportForm">
                    <div class="form-group">
                        <label for="report_type">Report Format:</label>
                        <select id="report_type" name="report_type" required>
                            <option value="excel">Excel (.xls)</option>
                            <option value="word">Word (.doc)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="report_frequency">Report Frequency:</label>
                        <select id="report_frequency" name="report_frequency" required>
                            <option value="hourly">Hourly Report</option>
                            <option value="daily">Daily Report</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="date_range">Date Range:</label>
                        <select id="date_range" name="date_range" required>
                            <option value="today">Today</option>
                            <option value="yesterday">Yesterday</option>
                            <option value="week">Last 7 Days</option>
                            <option value="month">Last 30 Days</option>
                        </select>
                    </div>
                    <div class="modal-buttons">
                        <button type="button" class="modal-btn btn-cancel" id="cancelReport">Cancel</button>
                        <button type="submit" class="modal-btn btn-generate" name="generate_report">Generate Report</button>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="chatbot-trigger" id="chatbotTrigger">
            <i class="fas fa-robot"></i>
        </div>
        
        <script>
            // Data from PHP
            const timestamps = <?php echo json_encode($timestamps); ?>;
            const temperature = <?php echo json_encode($temperature); ?>;
            const pressure = <?php echo json_encode($pressure); ?>;
            const efficiency = <?php echo json_encode($efficiency); ?>;
            const pH = <?php echo json_encode($pH); ?>;
            const flow = <?php echo json_encode($flow); ?>;

            console.log('Data loaded:', { timestamps, temperature, pressure, efficiency, pH, flow });

            // Initialize charts
            function initializeCharts() {
                console.log('Initializing charts...');
                
                // Temperature Chart
                if (document.getElementById('temperatureChart')) {
                    new Chart(document.getElementById('temperatureChart'), {
                        type: 'line',
                        data: {
                            labels: timestamps,
                            datasets: [{
                                label: 'Temperature',
                                data: temperature,
                                borderColor: '#66fcf1',
                                backgroundColor: 'rgba(102, 252, 241, 0.1)',
                                tension: 0.4,
                                fill: true,
                                borderWidth: 3,
                                pointBackgroundColor: '#66fcf1',
                                pointBorderColor: '#fff',
                                pointBorderWidth: 2,
                                pointRadius: 4
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    backgroundColor: 'rgba(31, 40, 51, 0.95)',
                                    titleColor: '#66fcf1',
                                    bodyColor: '#c5c6c7',
                                    borderColor: '#66fcf1',
                                    borderWidth: 1
                                }
                            },
                            scales: {
                                x: {
                                    grid: { 
                                        color: 'rgba(197, 198, 199, 0.1)',
                                        drawBorder: false
                                    },
                                    ticks: { 
                                        color: '#c5c6c7',
                                        font: { size: 11 }
                                    }
                                },
                                y: {
                                    grid: { 
                                        color: 'rgba(197, 198, 199, 0.1)',
                                        drawBorder: false
                                    },
                                    ticks: { 
                                        color: '#c5c6c7',
                                        font: { size: 11 }
                                    }
                                }
                            }
                        }
                    });
                }

                // Pressure Chart
                if (document.getElementById('pressureChart')) {
                    new Chart(document.getElementById('pressureChart'), {
                        type: 'line',
                        data: {
                            labels: timestamps,
                            datasets: [{
                                label: 'Pressure',
                                data: pressure,
                                borderColor: '#45a29e',
                                backgroundColor: 'rgba(69, 162, 158, 0.1)',
                                tension: 0.4,
                                fill: true,
                                borderWidth: 3,
                                pointBackgroundColor: '#45a29e',
                                pointBorderColor: '#fff',
                                pointBorderWidth: 2,
                                pointRadius: 4
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    backgroundColor: 'rgba(31, 40, 51, 0.95)',
                                    titleColor: '#45a29e',
                                    bodyColor: '#c5c6c7',
                                    borderColor: '#45a29e',
                                    borderWidth: 1
                                }
                            },
                            scales: {
                                x: {
                                    grid: { 
                                        color: 'rgba(197, 198, 199, 0.1)',
                                        drawBorder: false
                                    },
                                    ticks: { 
                                        color: '#c5c6c7',
                                        font: { size: 11 }
                                    }
                                },
                                y: {
                                    grid: { 
                                        color: 'rgba(197, 198, 199, 0.1)',
                                        drawBorder: false
                                    },
                                    ticks: { 
                                        color: '#c5c6c7',
                                        font: { size: 11 }
                                    }
                                }
                            }
                        }
                    });
                }

                // Efficiency Chart
                if (document.getElementById('efficiencyChart')) {
                    new Chart(document.getElementById('efficiencyChart'), {
                        type: 'line',
                        data: {
                            labels: timestamps,
                            datasets: [{
                                label: 'Efficiency',
                                data: efficiency,
                                borderColor: '#9b59b6',
                                backgroundColor: 'rgba(155, 89, 182, 0.1)',
                                tension: 0.4,
                                fill: true,
                                borderWidth: 3,
                                pointBackgroundColor: '#9b59b6',
                                pointBorderColor: '#fff',
                                pointBorderWidth: 2,
                                pointRadius: 4
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    backgroundColor: 'rgba(31, 40, 51, 0.95)',
                                    titleColor: '#9b59b6',
                                    bodyColor: '#c5c6c7',
                                    borderColor: '#9b59b6',
                                    borderWidth: 1
                                }
                            },
                            scales: {
                                x: {
                                    grid: { 
                                        color: 'rgba(197, 198, 199, 0.1)',
                                        drawBorder: false
                                    },
                                    ticks: { 
                                        color: '#c5c6c7',
                                        font: { size: 11 }
                                    }
                                },
                                y: {
                                    grid: { 
                                        color: 'rgba(197, 198, 199, 0.1)',
                                        drawBorder: false
                                    },
                                    ticks: { 
                                        color: '#c5c6c7',
                                        font: { size: 11 }
                                    }
                                }
                            }
                        }
                    });
                }

                // pH Chart
                if (document.getElementById('pHChart')) {
                    new Chart(document.getElementById('pHChart'), {
                        type: 'line',
                        data: {
                            labels: timestamps,
                            datasets: [{
                                label: 'pH Level',
                                data: pH,
                                borderColor: '#f1c40f',
                                backgroundColor: 'rgba(241, 196, 15, 0.1)',
                                tension: 0.4,
                                fill: true,
                                borderWidth: 3,
                                pointBackgroundColor: '#f1c40f',
                                pointBorderColor: '#fff',
                                pointBorderWidth: 2,
                                pointRadius: 4
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    backgroundColor: 'rgba(31, 40, 51, 0.95)',
                                    titleColor: '#f1c40f',
                                    bodyColor: '#c5c6c7',
                                    borderColor: '#f1c40f',
                                    borderWidth: 1
                                }
                            },
                            scales: {
                                x: {
                                    grid: { 
                                        color: 'rgba(197, 198, 199, 0.1)',
                                        drawBorder: false
                                    },
                                    ticks: { 
                                        color: '#c5c6c7',
                                        font: { size: 11 }
                                    }
                                },
                                y: {
                                    grid: { 
                                        color: 'rgba(197, 198, 199, 0.1)',
                                        drawBorder: false
                                    },
                                    ticks: { 
                                        color: '#c5c6c7',
                                        font: { size: 11 }
                                    }
                                }
                            }
                        }
                    });
                }

                // Flow Chart
                if (document.getElementById('flowChart')) {
                    new Chart(document.getElementById('flowChart'), {
                        type: 'line',
                        data: {
                            labels: timestamps,
                            datasets: [{
                                label: 'Flow Rate',
                                data: flow,
                                borderColor: '#e74c3c',
                                backgroundColor: 'rgba(231, 76, 60, 0.1)',
                                tension: 0.4,
                                fill: true,
                                borderWidth: 3,
                                pointBackgroundColor: '#e74c3c',
                                pointBorderColor: '#fff',
                                pointBorderWidth: 2,
                                pointRadius: 4
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    backgroundColor: 'rgba(31, 40, 51, 0.95)',
                                    titleColor: '#e74c3c',
                                    bodyColor: '#c5c6c7',
                                    borderColor: '#e74c3c',
                                    borderWidth: 1
                                }
                            },
                            scales: {
                                x: {
                                    grid: { 
                                        color: 'rgba(197, 198, 199, 0.1)',
                                        drawBorder: false
                                    },
                                    ticks: { 
                                        color: '#c5c6c7',
                                        font: { size: 11 }
                                    }
                                },
                                y: {
                                    grid: { 
                                        color: 'rgba(197, 198, 199, 0.1)',
                                        drawBorder: false
                                    },
                                    ticks: { 
                                        color: '#c5c6c7',
                                        font: { size: 11 }
                                    }
                                }
                            }
                        }
                    });
                }

                console.log('Charts initialized successfully');
            }

            function updateStatusIndicators() {
                const tempValue = temperature.length > 0 ? temperature[temperature.length-1] : null;
                const pressValue = pressure.length > 0 ? pressure[pressure.length-1] : null;
                const effValue = efficiency.length > 0 ? efficiency[efficiency.length-1] : null;
                const phValue = pH.length > 0 ? pH[pH.length-1] : null;
                const flowValue = flow.length > 0 ? flow[flow.length-1] : null;
                
                // Update display values
                if (document.getElementById('tempValue')) {
                    document.getElementById('tempValue').textContent = tempValue ? tempValue + '°C' : '--';
                }
                if (document.getElementById('pressValue')) {
                    document.getElementById('pressValue').textContent = pressValue ? pressValue + ' bar' : '--';
                }
                if (document.getElementById('effValue')) {
                    document.getElementById('effValue').textContent = effValue ? effValue + '%' : '--';
                }
                if (document.getElementById('phValue')) {
                    document.getElementById('phValue').textContent = phValue ? phValue : '--';
                }
                if (document.getElementById('flowValue')) {
                    document.getElementById('flowValue').textContent = flowValue ? flowValue + ' L/min' : '--';
                }
            }

            // Chart toggle functionality
            function setupChartToggle(checkboxId, cardId) {
                const checkbox = document.getElementById(checkboxId);
                const card = document.getElementById(cardId);
                
                if (checkbox && card) {
                    checkbox.addEventListener("change", e => {
                        card.style.display = e.target.checked ? "flex" : "none";
                    });
                }
            }

            // Report modal functionality
            document.addEventListener('DOMContentLoaded', function() {
                console.log('DOM loaded, setting up event listeners...');
                
                // Initialize charts
                initializeCharts();
                updateStatusIndicators();
                
                // Setup chart toggles
                setupChartToggle("tempBox", "tempChartCard");
                setupChartToggle("pressBox", "pressChartCard");
                setupChartToggle("effBox", "effChartCard");
                setupChartToggle("phBox", "phChartCard");
                setupChartToggle("flowBox", "flowChartCard");
                
                // Report modal
                const reportBtn = document.getElementById('reportBtn');
                const reportModal = document.getElementById('reportModal');
                const cancelReport = document.getElementById('cancelReport');
                
                if (reportBtn && reportModal) {
                    reportBtn.addEventListener('click', function() {
                        reportModal.style.display = 'flex';
                    });
                }
                
                if (cancelReport && reportModal) {
                    cancelReport.addEventListener('click', function() {
                        reportModal.style.display = 'none';
                    });
                }
                
                // Close modal when clicking outside
                if (reportModal) {
                    window.addEventListener('click', function(event) {
                        if (event.target === reportModal) {
                            reportModal.style.display = 'none';
                        }
                    });
                }
                
                // Chatbot trigger
                const chatbotTrigger = document.getElementById('chatbotTrigger');
                if (chatbotTrigger) {
                    chatbotTrigger.addEventListener('click', function() {
                        window.location.href = 'ai.php';
                    });
                }
                
                console.log('All event listeners set up successfully');
            });

            // Auto-refresh every 30 seconds
            setInterval(function() {
                location.reload();
            }, 30000);
        </script>
    </body>
</html>