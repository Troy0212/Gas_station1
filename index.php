<?php

session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include "db.php";


/* =========================================================
   CURRENT DATE
========================================================= */

$currentYear = date("Y");
$currentMonth = date("n");


/* =========================================================
   CALENDAR MONTH / YEAR
========================================================= */

$calendarYear = isset($_GET['year'])
    ? (int)$_GET['year']
    : (int)$currentYear;

$calendarMonth = isset($_GET['month'])
    ? (int)$_GET['month']
    : (int)$currentMonth;


/* Validate month */

if ($calendarMonth < 1 || $calendarMonth > 12) {
    $calendarMonth = $currentMonth;
}


/* Validate year */

if ($calendarYear < 2000 || $calendarYear > 2100) {
    $calendarYear = $currentYear;
}


/* =========================================================
   MONTH NAME
========================================================= */

$monthName = date(
    "F",
    mktime(0, 0, 0, $calendarMonth, 1, $calendarYear)
);


/* =========================================================
   PREVIOUS / NEXT MONTH
========================================================= */

$previousMonth = $calendarMonth - 1;
$previousYear = $calendarYear;

if ($previousMonth < 1) {
    $previousMonth = 12;
    $previousYear--;
}


$nextMonth = $calendarMonth + 1;
$nextYear = $calendarYear;

if ($nextMonth > 12) {
    $nextMonth = 1;
    $nextYear++;
}


/* =========================================================
   GET FUEL PRICES
========================================================= */

$prices = [
    "Gasoline" => 0,
    "Diesel" => 0,
    "Premium" => 0
];

$priceResult = mysqli_query(
    $conn,
    "SELECT fuel_type, price FROM fuel_prices"
);

while ($priceRow = mysqli_fetch_assoc($priceResult)) {

    if (isset($prices[$priceRow['fuel_type']])) {

        $prices[$priceRow['fuel_type']] =
            (float)$priceRow['price'];

    }
}


/* =========================================================
   TOTAL REVENUE
========================================================= */

$totalGasoline = 0;
$totalDiesel = 0;
$totalPremium = 0;
$totalRevenue = 0;

$result = mysqli_query(
    $conn,
    "SELECT * FROM sales"
);

while ($row = mysqli_fetch_assoc($result)) {

    $total = (float)$row['total'];

    $totalRevenue += $total;

    if ($row['fuel_type'] == "Gasoline") {
        $totalGasoline += $total;
    }

    if ($row['fuel_type'] == "Diesel") {
        $totalDiesel += $total;
    }

    if ($row['fuel_type'] == "Premium") {
        $totalPremium += $total;
    }
}


/* =========================================================
   PIE CHART PERCENTAGES
========================================================= */

$percentGasoline = $totalRevenue
    ? ($totalGasoline / $totalRevenue) * 100
    : 0;

$percentDiesel = $totalRevenue
    ? ($totalDiesel / $totalRevenue) * 100
    : 0;

$percentPremium = $totalRevenue
    ? ($totalPremium / $totalRevenue) * 100
    : 0;


/* =========================================================
   SELECTED MONTH TOTAL
========================================================= */

$monthStart = sprintf(
    "%04d-%02d-01",
    $calendarYear,
    $calendarMonth
);

$nextMonthStart = date(
    "Y-m-d",
    strtotime("+1 month", strtotime($monthStart))
);

$monthlyTotal = 0;

$monthlyResult = mysqli_query(
    $conn,
    "SELECT SUM(total) AS total
     FROM sales
     WHERE date >= '$monthStart'
     AND date < '$nextMonthStart'"
);

$monthlyRow = mysqli_fetch_assoc($monthlyResult);

if ($monthlyRow['total'] !== null) {
    $monthlyTotal = (float)$monthlyRow['total'];
}


/* =========================================================
   SELECTED YEAR TOTAL
========================================================= */

$yearStart = $calendarYear . "-01-01";

$nextYear = $calendarYear + 1;

$nextYearStart = $nextYear . "-01-01";

$yearlyTotal = 0;

$yearlyResult = mysqli_query(
    $conn,
    "SELECT SUM(total) AS total
     FROM sales
     WHERE date >= '$yearStart'
     AND date < '$nextYearStart'"
);

$yearlyRow = mysqli_fetch_assoc($yearlyResult);

if ($yearlyRow['total'] !== null) {
    $yearlyTotal = (float)$yearlyRow['total'];
}


/* =========================================================
   CURRENT MONTH TOTAL
========================================================= */

$currentMonthStart =
    date("Y-m-01");

$currentNextMonthStart =
    date(
        "Y-m-d",
        strtotime("+1 month", strtotime($currentMonthStart))
    );

$currentMonthTotal = 0;

$currentMonthResult = mysqli_query(
    $conn,
    "SELECT SUM(total) AS total
     FROM sales
     WHERE date >= '$currentMonthStart'
     AND date < '$currentNextMonthStart'"
);

$currentMonthRow =
    mysqli_fetch_assoc($currentMonthResult);

if ($currentMonthRow['total'] !== null) {

    $currentMonthTotal =
        (float)$currentMonthRow['total'];
}


/* =========================================================
   CURRENT YEAR TOTAL
========================================================= */

$currentYearStart =
    $currentYear . "-01-01";

$currentNextYearStart =
    ($currentYear + 1) . "-01-01";

$currentYearTotal = 0;

$currentYearResult = mysqli_query(
    $conn,
    "SELECT SUM(total) AS total
     FROM sales
     WHERE date >= '$currentYearStart'
     AND date < '$currentNextYearStart'"
);

$currentYearRow =
    mysqli_fetch_assoc($currentYearResult);

if ($currentYearRow['total'] !== null) {

    $currentYearTotal =
        (float)$currentYearRow['total'];
}


/* =========================================================
   DAILY SALES FOR CALENDAR
========================================================= */

$dailySales = [];

$dailyResult = mysqli_query(
    $conn,
    "SELECT
        DATE(date) AS sale_date,
        SUM(total) AS total_sales,
        COUNT(*) AS sale_count
     FROM sales
     WHERE date >= '$monthStart'
     AND date < '$nextMonthStart'
     GROUP BY DATE(date)
     ORDER BY DATE(date)"
);

while ($row = mysqli_fetch_assoc($dailyResult)) {

    $day = (int)date(
        "j",
        strtotime($row['sale_date'])
    );

    $dailySales[$day] = [
        "total" => (float)$row['total_sales'],
        "count" => (int)$row['sale_count']
    ];
}


/* =========================================================
   YEARLY SALES
========================================================= */

$yearlySales = [];

$yearResult = mysqli_query(
    $conn,
    "SELECT
        YEAR(date) AS sale_year,
        SUM(total) AS total_sales
     FROM sales
     GROUP BY YEAR(date)
     ORDER BY YEAR(date) DESC"
);

while ($row = mysqli_fetch_assoc($yearResult)) {

    $yearlySales[] = [
        "year" => $row['sale_year'],
        "total" => (float)$row['total_sales']
    ];
}


/* =========================================================
   MONTHLY SALES FOR SELECTED YEAR
========================================================= */

$monthlySales = [];

for ($i = 1; $i <= 12; $i++) {

    $start = sprintf(
        "%04d-%02d-01",
        $calendarYear,
        $i
    );

    $end = date(
        "Y-m-d",
        strtotime("+1 month", strtotime($start))
    );

    $monthlyQuery = mysqli_query(
        $conn,
        "SELECT SUM(total) AS total
         FROM sales
         WHERE date >= '$start'
         AND date < '$end'"
    );

    $monthlyRow =
        mysqli_fetch_assoc($monthlyQuery);

    $monthTotal =
        $monthlyRow['total'] !== null
        ? (float)$monthlyRow['total']
        : 0;

    $monthlySales[$i] = $monthTotal;
}


/* =========================================================
   CALENDAR INFORMATION
========================================================= */

$daysInMonth = cal_days_in_month(
    CAL_GREGORIAN,
    $calendarMonth,
    $calendarYear
);


/*
   PHP date:
   0 = Sunday
   1 = Monday
   ...
   6 = Saturday
*/

$firstDay = date(
    "w",
    strtotime($monthStart)
);

?>

<!DOCTYPE html>

<html>

<head>

<title>
Fire Gas Station System
</title>


<link
    rel="stylesheet"
    href="style.css"
>


<link
    href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap"
    rel="stylesheet"
>


<style>

/* =========================================================
   GENERAL
========================================================= */

.report-container {
    display: none;
    margin-top: 30px;
    text-align: center;
}


.report-button {

    background:
        linear-gradient(
            90deg,
            #ff3b3b,
            #ff7a00
        );

    border: none;

    padding: 12px 25px;

    border-radius: 6px;

    color: white;

    font-weight: 600;

    cursor: pointer;

    margin-top: 10px;

    text-decoration: none;

    display: inline-block;
}


.report-button:hover {
    opacity: 0.9;
}


/* =========================================================
   PIE CHART
========================================================= */

.pie-chart-container {

    position: relative;

    width: 300px;

    height: 300px;

    margin: auto;
}


svg {
    transform: rotate(-90deg);
}


.pie-center {

    position: absolute;

    top: 50%;

    left: 50%;

    transform:
        translate(-50%, -50%);

    width: 120px;

    height: 120px;

    background: #0a0f19;

    border-radius: 50%;

    display: flex;

    justify-content: center;

    align-items: center;

    font-weight: 700;

    font-size: 18px;

    color: white;

    box-shadow:
        0 0 10px #ff7a00aa;
}


/* =========================================================
   LEGEND
========================================================= */

.legend {

    display: flex;

    justify-content: center;

    gap: 20px;

    margin-top: 20px;

    flex-wrap: wrap;
}


.legend div {

    display: flex;

    align-items: center;

    gap: 6px;
}


.legend span {

    width: 20px;

    height: 20px;

    display: inline-block;

    border-radius: 4px;
}


.gasoline {
    background: #f97316;
}


.diesel {
    background: #3b82f6;
}


.premium {
    background: #a855f7;
}


/* =========================================================
   PRICE EDIT
========================================================= */

.price-edit {

    text-align: center;

    margin: 20px 0;
}


/* =========================================================
   SALES SUMMARY
========================================================= */

.sales-summary {

    margin-top: 40px;
}


.sales-summary h2 {

    text-align: center;

    margin-bottom: 25px;
}


/* =========================================================
   SUMMARY CARDS
========================================================= */

.summary-cards {

    display: grid;

    grid-template-columns:
        repeat(
            3,
            minmax(180px, 1fr)
        );

    gap: 20px;

    margin-bottom: 30px;
}


.summary-card {

    background:
        linear-gradient(
            135deg,
            #111827,
            #1f2937
        );

    border-radius: 10px;

    padding: 25px;

    text-align: center;

    box-shadow:
        0 5px 15px rgba(
            0,
            0,
            0,
            0.25
        );
}


.summary-card h3 {

    margin: 0 0 10px 0;

    color: #aaa;
}


.summary-card p {

    margin: 0;

    font-size: 25px;

    font-weight: 700;

    color: #ff7a00;
}


/* =========================================================
   CALENDAR
========================================================= */

.calendar-box {

    background: #111827;

    border-radius: 12px;

    padding: 25px;

    margin-bottom: 30px;

    box-shadow:
        0 5px 20px rgba(
            0,
            0,
            0,
            0.25
        );
}


.calendar-header {

    display: flex;

    justify-content: space-between;

    align-items: center;

    margin-bottom: 20px;

    gap: 10px;
}


.calendar-header h2 {

    margin: 0;

    color: white;

    font-size: 22px;
}


.calendar-nav {

    display: flex;

    gap: 8px;
}


.calendar-nav a {

    text-decoration: none;

    background: #374151;

    color: white;

    padding: 8px 13px;

    border-radius: 6px;

    font-size: 14px;
}


.calendar-nav a:hover {

    background: #ff7a00;
}


/* =========================================================
   CALENDAR GRID
========================================================= */

.calendar-grid {

    display: grid;

    grid-template-columns:
        repeat(
            7,
            minmax(0, 1fr)
        );

    gap: 5px;
}


.calendar-day-name {

    background: #1f2937;

    color: #aaa;

    padding: 12px 5px;

    text-align: center;

    font-weight: 700;

    font-size: 13px;
}


.calendar-day {

    min-height: 95px;

    background: #0f172a;

    border-radius: 6px;

    padding: 8px;

    position: relative;

    border:
        1px solid #1f2937;

    box-sizing: border-box;
}


.calendar-day.empty {

    background: transparent;

    border: none;
}


.day-number {

    color: white;

    font-weight: 700;

    font-size: 14px;
}


.calendar-day.has-sales {

    border:
        1px solid #ff7a00;

    background:
        linear-gradient(
            135deg,
            #1f2937,
            #172033
        );
}


.day-sales {

    margin-top: 10px;

    color: #ff7a00;

    font-size: 13px;

    font-weight: 700;
}


.day-count {

    margin-top: 4px;

    color: #9ca3af;

    font-size: 11px;
}


.today {

    box-shadow:
        inset 0 0 0 2px #ff3b3b;
}


/* =========================================================
   REPORT BOX
========================================================= */

.sales-report-box {

    background: #111827;

    border-radius: 10px;

    padding: 25px;

    margin-bottom: 30px;

    overflow-x: auto;
}


.sales-report-box h3 {

    margin-top: 0;

    margin-bottom: 20px;

    color: white;
}


/* =========================================================
   REPORT TABLE
========================================================= */

.sales-report-table {

    width: 100%;

    border-collapse: collapse;
}


.sales-report-table th {

    background: #1f2937;

    color: white;

    padding: 12px;

    text-align: left;
}


.sales-report-table td {

    padding: 12px;

    border-bottom:
        1px solid #374151;

    color: #ddd;
}


.sales-total {

    color: #ff7a00 !important;

    font-weight: 700;
}


/* =========================================================
   RESPONSIVE
========================================================= */

@media (max-width: 800px) {

    .summary-cards {

        grid-template-columns: 1fr;
    }

    .calendar-day {

        min-height: 75px;

        padding: 5px;
    }

    .day-sales {

        font-size: 10px;
    }

    .day-count {

        font-size: 9px;
    }

}


@media (max-width: 500px) {

    .calendar-box {

        padding: 10px;
    }

    .calendar-day {

        min-height: 65px;
    }

    .calendar-header {

        flex-direction: column;
    }

}

</style>

</head>


<body>


<!-- ========================================================
     HEADER
========================================================= -->

<header class="topbar">


    <div class="brand">


        <div class="logo-circle">
            ⛽
        </div>


        <div class="brand-text">


            <h1>
                Fire Gas Station
            </h1>


            <p>
                Management System
            </p>


        </div>


    </div>


    <div class="topbar-right">


        <span class="welcome">

            Welcome,
            <?php

            echo htmlspecialchars(
                $_SESSION['username']
            );

            ?>

        </span>


        <a
            href="logout.php"
            class="logout-btn"
        >

            Logout

        </a>


    </div>


</header>



<div class="main-container">


<!-- ========================================================
     DASHBOARD CARDS
========================================================= -->

<div class="dashboard">


    <div class="card gasoline">

        <h3>
            Gasoline
        </h3>

        <p>

            ₱<?php

            echo number_format(
                $prices['Gasoline'],
                2
            );

            ?>

            / Liter

        </p>

    </div>



    <div class="card diesel">

        <h3>
            Diesel
        </h3>

        <p>

            ₱<?php

            echo number_format(
                $prices['Diesel'],
                2
            );

            ?>

            / Liter

        </p>

    </div>



    <div class="card premium">

        <h3>
            Premium
        </h3>

        <p>

            ₱<?php

            echo number_format(
                $prices['Premium'],
                2
            );

            ?>

            / Liter

        </p>

    </div>



    <div class="card total-revenue">

        <h3>
            Total Revenue
        </h3>

        <p>

            ₱<?php

            echo number_format(
                $totalRevenue,
                2
            );

            ?>

        </p>


        <button
            class="report-button"
            id="toggleReport"
            type="button"
        >

            View Sales Report

        </button>

    </div>


</div>



<!-- ========================================================
     EDIT GAS PRICES
========================================================= -->

<div class="price-edit">


    <a
        href="edit_prices.php"
        class="report-button"
    >

        ✏ Edit Gas Prices

    </a>


</div>



<!-- ========================================================
     PIE CHART
========================================================= -->

<div
    class="report-container"
    id="reportContainer"
>


    <div class="pie-chart-container">


        <svg
            viewBox="0 0 200 200"
        >


            <path
                id="arcGasoline"
                fill="#f97316"
            ></path>


            <path
                id="arcDiesel"
                fill="#3b82f6"
            ></path>


            <path
                id="arcPremium"
                fill="#a855f7"
            ></path>


        </svg>


        <div class="pie-center">

            ₱<?php

            echo number_format(
                $totalRevenue,
                2
            );

            ?>

        </div>


    </div>


    <div class="legend">


        <div>

            <span class="gasoline"></span>

            Gasoline
            (<?php echo round($percentGasoline); ?>%)

        </div>


        <div>

            <span class="diesel"></span>

            Diesel
            (<?php echo round($percentDiesel); ?>%)

        </div>


        <div>

            <span class="premium"></span>

            Premium
            (<?php echo round($percentPremium); ?>%)

        </div>


    </div>


</div>



<!-- ========================================================
     SALES SUMMARY
========================================================= -->

<div class="sales-summary">


    <h2>
        📊 Sales Summary
    </h2>


    <!-- SUMMARY CARDS -->

    <div class="summary-cards">


        <div class="summary-card">

            <h3>
                <?php echo $monthName; ?>
                <?php echo $calendarYear; ?>
            </h3>

            <p>

                ₱<?php

                echo number_format(
                    $monthlyTotal,
                    2
                );

                ?>

            </p>

        </div>



        <div class="summary-card">

            <h3>
                <?php echo $calendarYear; ?>
                Total Sales
            </h3>

            <p>

                ₱<?php

                echo number_format(
                    $yearlyTotal,
                    2
                );

                ?>

            </p>

        </div>



        <div class="summary-card">

            <h3>
                Current Month
            </h3>

            <p>

                ₱<?php

                echo number_format(
                    $currentMonthTotal,
                    2
                );

                ?>

            </p>

        </div>


    </div>



    <!-- ====================================================
         CALENDAR
    ===================================================== -->

    <div class="calendar-box">


        <div class="calendar-header">


            <h2>

                📅
                <?php echo $monthName; ?>
                <?php echo $calendarYear; ?>

            </h2>


            <div class="calendar-nav">


                <a
                    href="index.php?month=<?php echo $previousMonth; ?>&year=<?php echo $previousYear; ?>"
                >

                    ← Previous

                </a>


                <a
                    href="index.php"
                >

                    Today

                </a>


                <a
                    href="index.php?month=<?php echo $nextMonth; ?>&year=<?php echo $nextYear; ?>"
                >

                    Next →

                </a>


            </div>


        </div>



        <!-- CALENDAR -->

        <div class="calendar-grid">


            <!-- DAYS -->

            <div class="calendar-day-name">
                Sun
            </div>

            <div class="calendar-day-name">
                Mon
            </div>

            <div class="calendar-day-name">
                Tue
            </div>

            <div class="calendar-day-name">
                Wed
            </div>

            <div class="calendar-day-name">
                Thu
            </div>

            <div class="calendar-day-name">
                Fri
            </div>

            <div class="calendar-day-name">
                Sat
            </div>


            <?php

            /* EMPTY DAYS BEFORE MONTH */

            for (
                $i = 0;
                $i < $firstDay;
                $i++
            ) {

            ?>

                <div
                    class="calendar-day empty"
                ></div>

            <?php

            }


            /* DAYS OF MONTH */

            for (
                $day = 1;
                $day <= $daysInMonth;
                $day++
            ) {


                $hasSales =
                    isset(
                        $dailySales[$day]
                    );


                $classes =
                    "calendar-day";


                if ($hasSales) {

                    $classes .=
                        " has-sales";

                }


                /* CHECK TODAY */

                if (
                    $day == date("j") &&
                    $calendarMonth == date("n") &&
                    $calendarYear == date("Y")
                ) {

                    $classes .=
                        " today";

                }

            ?>


                <div
                    class="<?php echo $classes; ?>"
                >


                    <div class="day-number">

                        <?php echo $day; ?>

                    </div>


                    <?php if ($hasSales) { ?>


                        <div class="day-sales">

                            ₱<?php

                            echo number_format(
                                $dailySales[$day]['total'],
                                2
                            );

                            ?>

                        </div>


                        <div class="day-count">

                            <?php

                            echo $dailySales[$day]['count'];

                            ?>

                            sale<?php

                            echo
                                $dailySales[$day]['count'] != 1
                                ? "s"
                                : "";

                            ?>

                        </div>


                    <?php } ?>


                </div>


            <?php

            }


            /* COMPLETE LAST ROW */

            $totalCalendarCells =
                $firstDay +
                $daysInMonth;


            $remainingCells =
                7 -
                ($totalCalendarCells % 7);


            if ($remainingCells < 7) {

                for (
                    $i = 0;
                    $i < $remainingCells;
                    $i++
                ) {

            ?>

                    <div
                        class="calendar-day empty"
                    ></div>

            <?php

                }

            }

            ?>


        </div>


    </div>



    <!-- ====================================================
         MONTHLY SALES
    ===================================================== -->

    <div class="sales-report-box">


        <h3>

            📅 Total Sales Per Month —
            <?php echo $calendarYear; ?>

        </h3>


        <table
            class="sales-report-table"
        >


            <thead>

                <tr>

                    <th>
                        Month
                    </th>

                    <th>
                        Total Sales
                    </th>

                </tr>

            </thead>


            <tbody>


            <?php

            for (
                $i = 1;
                $i <= 12;
                $i++
            ) {


                $displayMonth =
                    date(
                        "F",
                        mktime(
                            0,
                            0,
                            0,
                            $i,
                            1,
                            $calendarYear
                        )
                    );


            ?>


                <tr>


                    <td>

                        <?php

                        echo $displayMonth;

                        ?>

                    </td>


                    <td class="sales-total">

                        ₱<?php

                        echo number_format(
                            $monthlySales[$i],
                            2
                        );

                        ?>

                    </td>


                </tr>


            <?php

            }

            ?>


            </tbody>


        </table>


    </div>



    <!-- ====================================================
         YEARLY SALES
    ===================================================== -->

    <div class="sales-report-box">


        <h3>

            📆 Total Sales Per Year

        </h3>


        <table
            class="sales-report-table"
        >


            <thead>

                <tr>

                    <th>
                        Year
                    </th>

                    <th>
                        Total Sales
                    </th>

                </tr>

            </thead>


            <tbody>


            <?php

            if (
                count($yearlySales) > 0
            ) {


                foreach (
                    $yearlySales
                    as $year
                ) {

            ?>


                <tr>


                    <td>

                        <?php

                        echo $year['year'];

                        ?>

                    </td>


                    <td class="sales-total">

                        ₱<?php

                        echo number_format(
                            $year['total'],
                            2
                        );

                        ?>

                    </td>


                </tr>


            <?php

                }


            } else {


            ?>


                <tr>

                    <td colspan="2">

                        No yearly sales yet.

                    </td>

                </tr>


            <?php

            }

            ?>


            </tbody>


        </table>


    </div>


</div>



<!-- ========================================================
     ADD SALE + SALES RECORDS
========================================================= -->

<div class="content">


    <!-- ADD SALE -->

    <div class="form-section">


        <h2>
            Add Fuel Sale
        </h2>


        <form
            action="add.php"
            method="POST"
        >


            <label>
                Fuel Type
            </label>


            <select
                name="fuel_type"
                required
            >


                <option value="Gasoline">

                    Gasoline -
                    ₱<?php

                    echo number_format(
                        $prices['Gasoline'],
                        2
                    );

                    ?>/L

                </option>


                <option value="Diesel">

                    Diesel -
                    ₱<?php

                    echo number_format(
                        $prices['Diesel'],
                        2
                    );

                    ?>/L

                </option>


                <option value="Premium">

                    Premium -
                    ₱<?php

                    echo number_format(
                        $prices['Premium'],
                        2
                    );

                    ?>/L

                </option>


            </select>


            <label>
                Liters
            </label>


            <input
                type="number"
                step="0.01"
                min="0.01"
                name="liters"
                required
            >


            <button
                type="submit"
            >

                Add Sale

            </button>


        </form>


    </div>



    <!-- SALES RECORDS -->

    <div class="table-section">


        <h2>
            Sales Records
        </h2>


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

            $result = mysqli_query(
                $conn,
                "SELECT *
                 FROM sales
                 ORDER BY id DESC"
            );


            if (
                mysqli_num_rows($result)
                > 0
            ) {


                while (
                    $row =
                    mysqli_fetch_assoc(
                        $result
                    )
                ) {

            ?>


                <tr>


                    <td>

                        <?php
                        echo $row['id'];
                        ?>

                    </td>


                    <td>


                        <span
                            class="fuel-badge <?php

                            echo strtolower(
                                htmlspecialchars(
                                    $row['fuel_type']
                                )
                            );

                            ?>"
                        >

                            <?php

                            echo htmlspecialchars(
                                $row['fuel_type']
                            );

                            ?>

                        </span>


                    </td>


                    <td>

                        <?php

                        echo number_format(
                            $row['liters'],
                            2
                        );

                        ?>

                        L

                    </td>


                    <td>

                        ₱<?php

                        echo number_format(
                            $row['price_per_liter'],
                            2
                        );

                        ?>

                    </td>


                    <td class="total">

                        ₱<?php

                        echo number_format(
                            $row['total'],
                            2
                        );

                        ?>

                    </td>


                    <td>

                        <?php

                        echo htmlspecialchars(
                            $row['date']
                        );

                        ?>

                    </td>


                    <td>


                        <a
                            class="delete"
                            href="delete.php?id=<?php echo $row['id']; ?>"
                            onclick="return confirm('Are you sure you want to delete this sale?');"
                        >

                            Delete

                        </a>


                    </td>


                </tr>


            <?php

                }


            } else {

            ?>


                <tr>


                    <td
                        colspan="7"
                        style="text-align:center;"
                    >

                        No sales records yet.

                    </td>


                </tr>


            <?php

            }

            ?>


            </tbody>


        </table>


    </div>


</div>


</div>



<script>

/* =========================================================
   TOGGLE PIE REPORT
========================================================= */

document
    .getElementById("toggleReport")
    .addEventListener(
        "click",
        function () {


            const report =
                document.getElementById(
                    "reportContainer"
                );


            if (
                report.style.display ===
                "block"
            ) {

                report.style.display =
                    "none";

            } else {

                report.style.display =
                    "block";

                animatePie();

            }

        }
    );


/* =========================================================
   POLAR COORDINATES
========================================================= */

function polarToCartesian(
    cx,
    cy,
    r,
    angle
) {

    let rad =
        (angle - 90) *
        Math.PI /
        180;


    return {

        x:
            cx +
            r *
            Math.cos(rad),

        y:
            cy +
            r *
            Math.sin(rad)

    };

}


/* =========================================================
   DESCRIBE ARC
========================================================= */

function describeArc(
    cx,
    cy,
    r,
    startAngle,
    endAngle
) {


    if (
        endAngle <=
        startAngle
    ) {

        return "";

    }


    const start =
        polarToCartesian(
            cx,
            cy,
            r,
            endAngle
        );


    const end =
        polarToCartesian(
            cx,
            cy,
            r,
            startAngle
        );


    const largeArcFlag =
        endAngle -
        startAngle <= 180
        ? 0
        : 1;


    return `
        M ${cx} ${cy}
        L ${start.x} ${start.y}
        A ${r} ${r} 0
        ${largeArcFlag} 0
        ${end.x} ${end.y}
        Z
    `;

}


/* =========================================================
   PIE VALUES
========================================================= */

const pGasoline =
    <?php echo $percentGasoline; ?>;

const pDiesel =
    <?php echo $percentDiesel; ?>;

const pPremium =
    <?php echo $percentPremium; ?>;


/* =========================================================
   SVG
========================================================= */

const arcG =
    document.getElementById(
        "arcGasoline"
    );


const arcD =
    document.getElementById(
        "arcDiesel"
    );


const arcP =
    document.getElementById(
        "arcPremium"
    );


const cx = 100;

const cy = 100;

const radius = 90;


/* =========================================================
   ANIMATE PIE
========================================================= */

function animatePie() {


    let progress = 0;


    const targetGasoline =
        pGasoline * 3.6;


    const targetDiesel =
        pDiesel * 3.6;


    const targetPremium =
        pPremium * 3.6;


    function animate() {


        progress++;


        const g =
            targetGasoline *
            progress /
            100;


        const d =
            targetDiesel *
            progress /
            100;


        const p =
            targetPremium *
            progress /
            100;


        arcG.setAttribute(
            "d",
            describeArc(
                cx,
                cy,
                radius,
                0,
                g
            )
        );


        arcD.setAttribute(
            "d",
            describeArc(
                cx,
                cy,
                radius,
                g,
                g + d
            )
        );


        arcP.setAttribute(
            "d",
            describeArc(
                cx,
                cy,
                radius,
                g + d,
                g + d + p
            )
        );


        if (
            progress < 100
        ) {

            requestAnimationFrame(
                animate
            );

        }

    }


    animate();

}

</script>


</body>

</html>
