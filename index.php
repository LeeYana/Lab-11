<?php include 'connect.php'; ?>

<!DOCTYPE html>
<html>
<head>
  <title>Tourist Expenditure Graphs</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
   body {
  font-family: 'Poppins', sans-serif;
  background: linear-gradient(to right, #a1c4fd, #c2e9fb);
  text-align: center;
  padding: 40px;
}
    select {
      font-size: 16px;
      padding: 6px;
      margin: 10px;
    }
 canvas {
  max-width: 90%;
  width: 600px;
  height: 350px;
  display: block;
  margin: 30px auto;
  background-color: white;
  border-radius: 10px;
  padding: 20px;
  box-shadow: 0 0 10px rgba(0,0,0,0.2);
}

  </style>
</head>
<body>

  <h1>Expenditure by <span id="labelType">Domestic Visitors</span> (<span id="labelYear">2011</span>)</h1>

  <form method="GET">
    <label>Choose Type:
      <select name="type" onchange="this.form.submit()">
        <option value="visitor" <?= ($_GET['type'] ?? '') === 'visitor' ? 'selected' : '' ?>>Visitor</option>
        <option value="tourist" <?= ($_GET['type'] ?? '') === 'tourist' ? 'selected' : '' ?>>Tourist</option>
      </select>
    </label>

    <label>Choose Year:
      <select name="year" onchange="this.form.submit()">
        <option value="2010" <?= ($_GET['year'] ?? '') === '2010' ? 'selected' : '' ?>>2010</option>
        <option value="2011" <?= ($_GET['year'] ?? '2011') === '2011' ? 'selected' : '' ?>>2011</option>
      </select>
    </label>
  </form>

 <canvas id="barChart"></canvas>
<canvas id="pieChart"></canvas>


  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    const labels = <?= json_encode($labels = []); ?>;
    const data = <?= json_encode($data = []); ?>;
  </script>

<?php
// PHP: Fetch data from database
$type = $_GET['type'] ?? 'visitor';
$year = $_GET['year'] ?? '2011';

$sql = "SELECT component, amount FROM expenditure WHERE type = '$type' AND year = $year";
$result = $conn->query($sql);

$labels = [];
$data = [];

if ($result->num_rows > 0) {
  while($row = $result->fetch_assoc()) {
   $labels[] = str_replace(
  'Expenditure before the trip/packages/entrance fees/tickets',
  'Pre-trip Fees',
  $row['component']
);
    
    $data[] = (float)$row['amount'];
  }
}
$conn->close();
?>

<script>
  const chartLabels = <?= json_encode($labels); ?>;
  const chartData = <?= json_encode($data); ?>;

  const barConfig = {
  type: 'bar',
  data: {
    labels: chartLabels,
    datasets: [{
      label: 'Expenditure (RM million)',
      data: chartData,
      backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40', '#7C4DFF']
    }]
  },
  options: {
    plugins: {
      legend: { display: true }
    },
    scales: {
      x: {
        ticks: {
          maxRotation: 25,
          minRotation: 0
        }
      }
    }
  }
};


  const pieConfig = {
    type: 'pie',
    data: {
      labels: chartLabels,
      datasets: [{
        data: chartData,
        backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40', '#7C4DFF']
      }]
    }
  };

  new Chart(document.getElementById('barChart'), barConfig);
  new Chart(document.getElementById('pieChart'), pieConfig);

  document.getElementById('labelType').textContent = 'Domestic ' + (<?= json_encode($type) ?> === 'visitor' ? 'Visitors' : 'Tourists');
  document.getElementById('labelYear').textContent = <?= json_encode($year) ?>;
</script>

</body>
</html>
<?php
// End of PHP code
