<?php

session_start();

if(!isset($_SESSION['username'])){
    header("Location: login.php");
    exit();
}

include "db.php";

// ================= CALCULATE TOTALS =================

$result = mysqli_query($conn,"SELECT * FROM sales");

// Initialize totals
$totalGasoline = 0;
$totalDiesel = 0;
$totalPremium = 0;
$totalRevenue = 0;

while($row = mysqli_fetch_assoc($result)){

    $totalRevenue += $row['total'];

    if($row['fuel_type']=="Gasoline"){
        $totalGasoline += $row['total'];
    }

    if($row['fuel_type']=="Diesel"){
        $totalDiesel += $row['total'];
    }

    if($row['fuel_type']=="Premium"){
        $totalPremium += $row['total'];
    }

}

$percentGasoline = $totalRevenue ? ($totalGasoline/$totalRevenue)*100 : 0;
$percentDiesel   = $totalRevenue ? ($totalDiesel/$totalRevenue)*100 : 0;
$percentPremium  = $totalRevenue ? ($totalPremium/$totalRevenue)*100 : 0;

?>


<!DOCTYPE html>
<html>
<head>
<title>Fire Gas Station System</title>


<link rel="stylesheet" href="style.css">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">


<style>
/* Report container */
.report-container{
    display:none;
    margin-top:30px;
    text-align:center;
}
.report-button{
    background: linear-gradient(90deg,#ff3b3b,#ff7a00);
    border:none;
    padding:12px 25px;
    border-radius:6px;
    color:white;
    font-weight:600;
    cursor:pointer;
    margin-top:10px;
}
.pie-chart-container{
    position: relative;
    width:300px;
    height:300px;
    margin:auto;
}
svg{
    transform: rotate(-90deg);
}
.pie-center{
    position:absolute;
    top:50%;
    left:50%;
    transform:translate(-50%,-50%);
    width:120px;
    height:120px;
    background:#0a0f19;
    border-radius:50%;
    display:flex;
    justify-content:center;
    align-items:center;
    font-weight:700;
    font-size:18px;
    color:#fff;
    box-shadow:0 0 10px #ff7a00aa;
}
.legend{
    display:flex;
    justify-content:center;
    gap:20px;
    margin-top:20px;
}
.legend div{
    display:flex;
    align-items:center;
    gap:6px;
}
.legend span{
    width:20px; height:20px; display:inline-block; border-radius:4px;
}
.gasoline{background:#f97316;}
.diesel{background:#3b82f6;}
.premium{background:#a855f7;}
</style>
</head>
<body>


 <header class="topbar">

    <div class="brand">
        <div class="logo-circle">⛽</div>

        <div class="brand-text">
            <h1>Fire Gas Station</h1>
            <p>Management System</p>
        </div>
    </div>

    <div class="topbar-right">
        <span class="welcome">
            Welcome, <?php echo $_SESSION['username']; ?>
        </span>

        <a href="logout.php" class="logout-btn">
            Logout
        </a>
    </div>

</header>


<div class="main-container">


  <!-- DASHBOARD CARDS -->
  <div class="dashboard">
    <div class="card gasoline">
      <h3>Gasoline</h3>
      <p>₱65.50 / Liter</p>
    </div>
    <div class="card diesel">
      <h3>Diesel</h3>
      <p>₱60.20 / Liter</p>
    </div>
    <div class="card premium">
      <h3>Premium</h3>
      <p>₱72.90 / Liter</p>
    </div>
    <div class="card total-revenue">
      <h3>Total Revenue</h3>
      <p>₱<?php echo number_format($totalRevenue,2); ?></p>
      <button class="report-button" id="toggleReport">View Sales Report</button>
    </div>
  </div>


  <!-- REPORT SECTION -->
  <div class="report-container" id="reportContainer">
      <div class="pie-chart-container">
          <svg viewBox="0 0 200 200" role="img" aria-label="Pie chart of fuel sales">
              <path id="arcGasoline" fill="#f97316"></path>
              <path id="arcDiesel" fill="#3b82f6"></path>
              <path id="arcPremium" fill="#a855f7"></path>
          </svg>
          <div class="pie-center">₱<?php echo number_format($totalRevenue,2); ?></div>
      </div>
      <div class="legend">
          <div><span class="gasoline"></span> Gasoline (<?php echo round($percentGasoline); ?>%)</div>
          <div><span class="diesel"></span> Diesel (<?php echo round($percentDiesel); ?>%)</div>
          <div><span class="premium"></span> Premium (<?php echo round($percentPremium); ?>%)</div>
      </div>
  </div>


  <div class="content">


    <!-- FORM SECTION -->
    <div class="form-section">
      <h2>Add Fuel Sale</h2>
      <form action="add.php" method="POST">
        <label>Fuel Type</label>
        <select name="fuel_type">
          <option>Gasoline</option>
          <option>Diesel</option>
          <option>Premium</option>
        </select>


        <label>Liters</label>
        <input type="number" step="0.01" name="liters" required>


        <label>Price Per Liter</label>
        <input type="number" step="0.01" name="price" required>


        <button type="submit">Add Sale</button>
      </form>
    </div>


    <!-- TABLE SECTION -->
    <div class="table-section">
      <h2>Sales Records</h2>
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Fuel</th>
            <th>Liters</th>
            <th>Price/L</th>
            <th>Total</th>
            <th>Date</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $result = mysqli_query($conn,"SELECT * FROM sales ORDER BY id DESC");
          while($row = mysqli_fetch_assoc($result)){
          ?>
          <tr>
            <td><?php echo $row['id']; ?></td>
            <td>
              <span class="fuel-badge <?php echo strtolower($row['fuel_type']); ?>">
                <?php echo $row['fuel_type']; ?>
              </span>
            </td>
            <td><?php echo $row['liters']; ?> L</td>
            <td>₱<?php echo $row['price_per_liter']; ?></td>
            <td class="total">₱<?php echo $row['total']; ?></td>
            <td><?php echo $row['date']; ?></td>
            <td>
              <a class="delete" href="delete.php?id=<?php echo $row['id']; ?>">Delete</a>
            </td>
          </tr>
          <?php } ?>
        </tbody>
      </table>
    </div>


  </div>
</div>


<script>
// Toggle Report
document.getElementById("toggleReport").addEventListener("click", function () {

    const report = document.getElementById("reportContainer");

    if(report.style.display === "block"){
        report.style.display = "none";
    }else{
        report.style.display = "block";
        animatePie();
    }

});


// Convert Polar Coordinates
function polarToCartesian(cx, cy, r, angle){

    let rad = (angle - 90) * Math.PI / 180;

    return {
        x: cx + r * Math.cos(rad),
        y: cy + r * Math.sin(rad)
    };

}


// Draw Pie Slice
function describeArc(cx, cy, r, startAngle, endAngle){

    if(endAngle <= startAngle){
        return "";
    }

    const start = polarToCartesian(cx, cy, r, endAngle);
    const end = polarToCartesian(cx, cy, r, startAngle);

    const largeArcFlag = endAngle - startAngle <= 180 ? 0 : 1;

    return `M ${cx} ${cy}
            L ${start.x} ${start.y}
            A ${r} ${r} 0 ${largeArcFlag} 0 ${end.x} ${end.y}
            Z`;

}


// PHP Values
const pGasoline = <?php echo $percentGasoline; ?>;
const pDiesel   = <?php echo $percentDiesel; ?>;
const pPremium  = <?php echo $percentPremium; ?>;


// SVG Paths
const arcG = document.getElementById("arcGasoline");
const arcD = document.getElementById("arcDiesel");
const arcP = document.getElementById("arcPremium");


const cx = 100;
const cy = 100;
const radius = 90;


// Animate Pie
function animatePie(){

    let progress = 0;

    const targetGasoline = pGasoline * 3.6;
    const targetDiesel   = pDiesel * 3.6;
    const targetPremium  = pPremium * 3.6;

    function animate(){

        progress++;

        const g = targetGasoline * progress / 100;
        const d = targetDiesel * progress / 100;
        const p = targetPremium * progress / 100;

        arcG.setAttribute("d", describeArc(cx, cy, radius, 0, g));

        arcD.setAttribute("d",
            describeArc(cx, cy, radius, g, g + d)
        );

        arcP.setAttribute("d",
            describeArc(cx, cy, radius, g + d, g + d + p)
        );

        if(progress < 100){
            requestAnimationFrame(animate);
        }

    }

    animate();

}
</script>


</body>
</html>
fonts.googleapis.com