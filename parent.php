<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <meta content="" name="description">
  <meta content="" name="keywords">

  <!-- Favicons 
  <link href="assets/img/favicon.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">
 -->
  <!-- Google Fonts
  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">
   -->
  <!-- Vendor CSS Files -->
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets2/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets2/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="assets2/vendor/quill/quill.snow.css" rel="stylesheet">
  <link href="assets2/vendor/quill/quill.bubble.css" rel="stylesheet">
  <link href="assets2/vendor/remixicon/remixicon.css" rel="stylesheet">
  <link href="style.css" rel="stylesheet">

  <!-- Template Main CSS File -->
  <style>
    /*--------------------------------------------------------------
# Dashboard
--------------------------------------------------------------*/
/* Filter dropdown */
.dashboard .filter {
  position: absolute;
  right: 0px;
  top: 15px;
}

.dashboard .filter .icon {
  color: #aab7cf;
  padding-right: 20px;
  padding-bottom: 5px;
  transition: 0.3s;
  font-size: 16px;
}

.dashboard .filter .icon:hover,
.dashboard .filter .icon:focus {
  color: #4154f1;
}

.dashboard .filter .dropdown-header {
  padding: 8px 15px;
}

.dashboard .filter .dropdown-header h6 {
  text-transform: uppercase;
  font-size: 14px;
  font-weight: 600;
  letter-spacing: 1px;
  color: #aab7cf;
  margin-bottom: 0;
  padding: 0;
}

.dashboard .filter .dropdown-item {
  padding: 8px 15px;
}

/* Info Cards */
.dashboard .info-card {
  padding-bottom: 10px;
}

.dashboard .info-card h6 {
  font-size: 28px;
  color: #012970;
  font-weight: 700;
  margin: 0;
  padding: 0;
}

.dashboard .card-icon {
  font-size: 32px;
  line-height: 0;
  width: 64px;
  height: 64px;
  flex-shrink: 0;
  flex-grow: 0;
}

.dashboard .sales-card .card-icon {
  color: #4154f1;
  background: #f6f6fe;
}

.dashboard .revenue-card .card-icon {
  color: #2eca6a;
  background: #e0f8e9;
}

.dashboard .customers-card .card-icon {
  color: #ff771d;
  background: #ffecdf;
}

/* Activity */
.dashboard .activity {
  font-size: 14px;
}

.dashboard .activity .activity-item .activite-label {
  color: #888;
  position: relative;
  flex-shrink: 0;
  flex-grow: 0;
  min-width: 64px;
}

.dashboard .activity .activity-item .activite-label::before {
  content: "";
  position: absolute;
  right: -11px;
  width: 4px;
  top: 0;
  bottom: 0;
  background-color: #eceefe;
}

.dashboard .activity .activity-item .activity-badge {
  margin-top: 3px;
  z-index: 1;
  font-size: 11px;
  line-height: 0;
  border-radius: 50%;
  flex-shrink: 0;
  border: 3px solid #fff;
  flex-grow: 0;
}

.dashboard .activity .activity-item .activity-content {
  padding-left: 10px;
  padding-bottom: 20px;
}

.dashboard .activity .activity-item:first-child .activite-label::before {
  top: 5px;
}

.dashboard .activity .activity-item:last-child .activity-content {
  padding-bottom: 0;
}

/* News & Updates */
.dashboard .news .post-item+.post-item {
  margin-top: 15px;
}

.dashboard .news img {
  width: 80px;
  float: left;
  border-radius: 5px;
}

.dashboard .news h4 {
  font-size: 15px;
  margin-left: 95px;
  font-weight: bold;
  margin-bottom: 5px;
}

.dashboard .news h4 a {
  color: #012970;
  transition: 0.3s;
}

.dashboard .news h4 a:hover {
  color: #4154f1;
}

.dashboard .news p {
  font-size: 14px;
  color: #777777;
  margin-left: 95px;
}

/* Recent Sales */
.dashboard .recent-sales {
  font-size: 14px;
}

.dashboard .recent-sales .table thead {
  background: #f6f6fe;
}

.dashboard .recent-sales .table thead th {
  border: 0;
}

.dashboard .recent-sales .dataTable-top {
  padding: 0 0 10px 0;
}

.dashboard .recent-sales .dataTable-bottom {
  padding: 10px 0 0 0;
}

/* Top Selling */
.dashboard .top-selling {
  font-size: 14px;
}

.dashboard .top-selling .table thead {
  background: #f6f6fe;
}

.dashboard .top-selling .table thead th {
  border: 0;
}

.dashboard .top-selling .table tbody td {
  vertical-align: middle;
}

.dashboard .top-selling img {
  border-radius: 5px;
  max-width: 60px;
}

  </style>
</head>

<body>
  <main id="main" class="main">
    <section class="section dashboard">
      <div class="row">

        <!-- Left side columns -->
        <div class="col-lg-8">
          <div class="row">

          
<!-- Sales Card -->
<div class="col-xxl-4 col-md-6">
  <div class="card info-card sales-card">
    <div class="filter">
      <a class="icon" href="#" id="filterDropdown" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
      <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
        <li class="dropdown-header text-start">
          <h6>Filter</h6>
        </li>
        <li><a class="dropdown-item filter-option" href="#" data-filter="today">Today</a></li>
        <li><a class="dropdown-item filter-option" href="#" data-filter="month">This Month</a></li>
        <li><a class="dropdown-item filter-option" href="#" data-filter="year">This Year</a></li>
      </ul>
    </div>
    <div class="card-body">
      <h5 class="card-title">Purchases <span id="filterSpan">| Today</span></h5>
      <div class="d-flex align-items-center">
        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
          <i class="bi bi-cart"></i>
        </div>
        <div class="ps-3">
          <h6 id="numberOfSales"></h6>
        </div>
      </div>
    </div>
  </div>
</div><!-- End Sales Card -->

           <!-- Revenue Card -->
<div class="col-xxl-4 col-md-6">
  <div class="card info-card revenue-card">
    <div class="filter">
      <a class="icon" href="#" id="revenueFilterDropdown" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
      <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
        <li class="dropdown-header text-start">
          <h6>Filter</h6>
        </li>
        <li><a class="dropdown-item revenue-filter-option" href="#" data-filter="today">Today</a></li>
        <li><a class="dropdown-item revenue-filter-option" href="#" data-filter="month">This Month</a></li>
        <li><a class="dropdown-item revenue-filter-option" href="#" data-filter="year">This Year</a></li>
      </ul>
    </div>
    <div class="card-body">
      <h5 class="card-title">Sales <span id="revenueFilterSpan">| Month</span></h5>
      <div class="d-flex align-items-center">
        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
          <i class="bi bi-cash"></i>
        </div>
        <div class="ps-3">
          <h6 id="revenueAmount"></h6>
        </div>
      </div>
    </div>
  </div>
</div>

            <!-- Customers Card -->
<div class="col-xxl-4 col-md-6">
  <div class="card info-card customers-card">
    <div class="filter">
      <a class="icon" href="#" id="filterDropdown" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
      <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
        <li class="dropdown-header text-start">
          <h6>Filter</h6>
        </li>
        <li><a class="dropdown-item customer-filter-option" href="#" data-filter="today">Today</a></li>
        <li><a class="dropdown-item customer-filter-option" href="#" data-filter="month">This Month</a></li>
        <li><a class="dropdown-item customer-filter-option" href="#" data-filter="year">This Year</a></li>
      </ul>
    </div>
    <div class="card-body">
      <h5 class="card-title">New Users <span id="customerFilterSpan">| This Year</span></h5>
      <div class="d-flex align-items-center">
        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
          <i class="bi bi-people"></i>
        </div>
        <div class="ps-3">
          <h6 id="numberOfCustomers"></h6>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- End Customers Card -->
<!-- Events Card -->
<div class="col-xxl-4 col-md-6">
  <div class="card info-card events-card">
    <div class="filter">
      <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
      <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
        <li class="dropdown-header text-start">
          <h6>Filter</h6>
        </li>
        <li><a class="dropdown-item event-filter-option" href="#" data-filter="today">Today</a></li>
        <li><a class="dropdown-item event-filter-option" href="#" data-filter="month">This Month</a></li>
        <li><a class="dropdown-item event-filter-option" href="#" data-filter="year">This Year</a></li>
      </ul>
    </div>
    <div class="card-body">
      <h5 class="card-title">New Events <span id="eventFilterSpan">| Today</span></h5>
      <div class="d-flex align-items-center">
        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
          <i class="bi bi-calendar"></i>
        </div>
        <div class="ps-3">
          <h6 id="numberOfEvents">0</h6> <!-- Placeholder for displaying the number of events -->

        </div>
      </div>
    </div>
  </div>
</div>
<!-- End Events Card -->
           <!-- Reports -->
<div class="col-12">
  <div class="card">
    <div class="card-body">
      <h5 class="card-title">Reports <span id="reportFilterSpan"></span></h5>
    <!-- Line Chart -->
<div id="reportChart"></div>
<script>
    document.addEventListener("DOMContentLoaded", () => {
        const filterOptions = document.querySelectorAll('.report-filter-option');
        const filterSpan = document.getElementById('reportFilterSpan');
        const reportChart = document.getElementById('reportChart');

        filterOptions.forEach(option => {
            option.addEventListener('click', function () {
                const filter = this.getAttribute('data-filter');
                filterSpan.textContent = `| ${this.textContent}`;
                // Fetch data based on the selected filter
                fetchData(filter);
            });
        });

        // Function to fetch data based on the selected filter
        function fetchData(filter) {
            fetch(`fetch_report_data.php?filter=${filter}`)
                .then(response => response.json())
                .then(data => {
                    // Update the chart based on the fetched data
                    renderChart(data);
                })
                .catch(error => console.error('Error fetching data:', error));
        }

        // Function to render the chart
        function renderChart(data) {
            new ApexCharts(reportChart, {
                series: [{
                    name: 'Users',
                    data: data.users
                }, {
                    name: 'Events',
                    data: data.events
                }, {
                    name: 'Purchases',
                    data: data.sales
                }],
                chart: {
                    height: 350,
                    type: 'line',
                    toolbar: {
                        show: false
                    },
                },
                markers: {
                    size: 4
                },
                colors: ['#4154f1', '#2eca6a', '#ff771d'],
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'smooth',
                    width: 2
                },
                xaxis: {
                    categories: data.months // Assuming data.months contains an array of month names
                },
                yaxis: {
                    labels: {
                        formatter: function (value) {
                            return value.toFixed(0); // Format y-axis labels to show whole numbers
                        }
                    }
                },
                legend: {
                    position: 'top'
                }
            }).render();
        }

        // Fetch initial data for 'This Year'
        fetchData('this_year');
    });

</script>
<!-- End Line Chart -->
</div>
  </div>
</div>
<!-- End Reports -->

          </div>
        </div><!-- End Left side columns -->

        <!-- Right side columns -->
        <div class="col-lg-4">
          <div class="row">
            <!-- Pending Events Card -->
            <div class="col-lg-6 col-md-7">
              <div class="card info-card pending-events-card">
                <div class="card-body">
                  <h1 class="card-title">Pending Events</h1>
                  <div class="d-flex align-items-center flex-column"> <!-- Added flex-column class -->
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center small-icon">
                      <i class="bi bi-calendar-check"></i>
                    </div>
                    <div class="ps-3">
                      <h6 id="numberOfPendingEvents"></h6>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- End Pending Events Card -->

            <!-- Pending Users Card -->
            <div class="col-lg-6 col-md-6">
              <div class="card info-card pending-users-card">
                <div class="card-body">
                  <h5 class="card-title">Pending Users</h5>
                  <div class="d-flex align-items-center flex-column"> <!-- Added flex-column class -->
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center small-icon">
                      <i class="bi bi-person-check"></i>
                    </div>
                    <div class="ps-3">
                      <h6 id="numberOfPendingUsers"></h6>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- End Pending Users Card -->
          </div>

         <!-- Event Categories -->
<div class="card">
  <div class="card-body pb-0">
    <h5 class="card-title">Event Categories</h5>
    <div id="trafficChart" style="min-height: 500px;" class="echart"></div>
    <script>
  document.addEventListener("DOMContentLoaded", () => {
    // Fetch event category data from the backend (PHP) and render the chart
    fetchEventDataAndRenderChart();
  });

  function fetchEventDataAndRenderChart() {
    fetch('fetch_event_count.php') // Adjust the URL according to your PHP script
      .then(response => response.json())
      .then(data => {
        renderChart(data);
      })
      .catch(error => console.error('Error fetching event data:', error));
  }

  function renderChart(eventData) {
    const trafficChart = echarts.init(document.querySelector("#trafficChart"));
    trafficChart.setOption({
      tooltip: {
        trigger: 'item'
      },
      legend: {
        top: '5%',
        left: 'center'
      },
      series: [{
        name: 'Event Categories',
        type: 'pie',
        radius: ['40%', '70%'],
        avoidLabelOverlap: false,
        label: {
          show: false,
          position: 'center'
        },
        emphasis: {
          label: {
            show: true,
            fontSize: '18',
            fontWeight: 'bold'
          }
        },
        labelLine: {
          show: false
        },
        data: eventData // Use the fetched event category data here
      }]
    });
  }
</script>

  </div>
</div>
<!-- End Website Traffic 
<a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>-->
  <!-- Template Main JS File -->
  <script src="assets2/js/main.js"></script>
  <script>
  document.addEventListener("DOMContentLoaded", () => {
    const filterOptions = document.querySelectorAll('.filter-option');
    const filterSpan = document.getElementById('filterSpan');
    const numberOfSales = document.getElementById('numberOfSales');

    filterOptions.forEach(option => {
      option.addEventListener('click', function() {
        const filter = this.getAttribute('data-filter');
        filterSpan.textContent = `| ${this.textContent}`;
        // Fetch data based on the selected filter
        fetchData(filter);
      });
    });

    // Function to fetch data based on the selected filter
    function fetchData(filter) {
      fetch('fetch_data.php?filter=' + filter) // Adjust the URL according to your PHP script
        .then(response => response.json())
        .then(data => {
          // Update the sales count based on the fetched data
          numberOfSales.textContent = data.numberOfSales;
        })
        .catch(error => console.error('Error fetching data:', error));
    }


    // Fetch initial data for 'Today'
    fetchData('today');
  });
</script>
<script>
  document.addEventListener("DOMContentLoaded", () => {
    const revenueFilterOptions = document.querySelectorAll('.revenue-filter-option');
    const revenueFilterSpan = document.getElementById('revenueFilterSpan');
    const revenueAmount = document.getElementById('revenueAmount');

    revenueFilterOptions.forEach(option => {
      option.addEventListener('click', function() {
        const filter = this.getAttribute('data-filter');
        revenueFilterSpan.textContent = `| ${this.textContent}`;
        // Fetch data based on the selected filter
        fetchRevenueData(filter);
      });
    });

    // Function to fetch revenue data based on the selected filter
    function fetchRevenueData(filter) {
      fetch('fetch_revenue_data.php?filter=' + filter) // Adjust the URL according to your PHP script
        .then(response => response.json())
        .then(data => {
          // Update the revenue amount based on the fetched data
          revenueAmount.textContent = data.revenueAmount;
        })
        .catch(error => console.error('Error fetching revenue data:', error));
    }

    // Fetch initial revenue data for 'Today'
    fetchRevenueData('month');
  });
</script>
<script>
  document.addEventListener("DOMContentLoaded", () => {
    const filterOptions = document.querySelectorAll('.customer-filter-option');
    const filterSpan = document.getElementById('customerFilterSpan');
    const numberOfCustomers = document.getElementById('numberOfCustomers');

    filterOptions.forEach(option => {
      option.addEventListener('click', function() {
        const filter = this.getAttribute('data-filter');
        filterSpan.textContent = `| ${this.textContent}`;
        // Fetch data based on the selected filter
        fetchData(filter);
      });
    });

    // Function to fetch data based on the selected filter
    function fetchData(filter) {
      fetch('fetch_customer_data.php?filter=' + filter) // Adjust the URL according to your PHP script
        .then(response => response.json())
        .then(data => {
          // Update the number of customers based on the fetched data
          numberOfCustomers.textContent = data.numberOfCustomers;
        })
        .catch(error => console.error('Error fetching data:', error));
    }

    // Fetch initial data for 'This Year'
    fetchData('year');
  });
</script>
<script>
  document.addEventListener("DOMContentLoaded", () => {
    const filterOptions = document.querySelectorAll('.event-filter-option');
    const filterSpan = document.getElementById('eventFilterSpan');
    const numberOfEvents = document.getElementById('numberOfEvents');

    filterOptions.forEach(option => {
      option.addEventListener('click', function() {
        const filter = this.getAttribute('data-filter');
        filterSpan.textContent = `| ${this.textContent}`;
        // Fetch data based on the selected filter
        fetchData(filter);
      });
    });

    // Function to fetch data based on the selected filter
    function fetchData(filter) {
      fetch('fetch_event_data.php?filter=' + filter) // Adjust the URL according to your PHP script
        .then(response => response.json())
        .then(data => {
          // Update the number of events based on the fetched data
          numberOfEvents.textContent = data.numberOfEvents;
        })
        .catch(error => console.error('Error fetching data:', error));
    }

    // Fetch initial data for 'Today'
    fetchData('today');
  });
</script>
<script>
  document.addEventListener("DOMContentLoaded", () => {
  // Fetch data for pending events and update the count
  fetchEventData();

  // Fetch data for pending users and update the count
  fetchUserData();
});

function fetchEventData() {
  fetch('fetch_pending.php')
    .then(response => response.json())
    .then(data => {
      document.getElementById('numberOfPendingEvents').textContent = data.numberOfPendingEvents;
    })
    .catch(error => console.error('Error fetching pending event data:', error));
}

function fetchUserData() {
  fetch('fetch_pending.php')
    .then(response => response.json())
    .then(data => {
      document.getElementById('numberOfPendingUsers').textContent = data.numberOfPendingUsers;
    })
    .catch(error => console.error('Error fetching pending user data:', error));
}

</script>

<?php include 'footer.php';?>
</body>

</html>