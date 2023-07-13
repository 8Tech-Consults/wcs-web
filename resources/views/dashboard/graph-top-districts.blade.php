<?php
use App\Models\Utils;
?>

<style>
    .ext-icon {
        color: rgba(0, 0, 0, 0.5);
        margin-left: 10px;
    }

    .installed {
        color: #00a65a;
        margin-right: 10px;
    }

    .card {
        border-radius: 5px;
    }

    .case-item:hover {
        background-color: rgb(254, 254, 254);
    }
</style>

<div class="card mb-4 mb-md-5 border-0">
    <!--begin::Header-->
    <div class="d-flex justify-content-between px-3 px-md-4 ">
        <h3>
            <b>Cases per CA</b>
        </h3>
        <div class="dropdown">
            <button class="btn btn-sm btn-primary mt-md-4 mt-4 dropdown-toggle" type="button" id="exportDropdown"
                data-bs-toggle="dropdown" aria-expanded="false">
                Action
            </button>
            <ul class="dropdown-menu" aria-labelledby="exportDropdown">
                <li><a class="dropdown-item" href="#" id="exportCAJpegBtn">Export JPEG</a></li>
                <li><a class="dropdown-item" href="#" id="exportCACsvBtn">Export CSV</a></li>
                <li>
                    <hr class="dropdown-divider">
                </li>
                <li><a class="dropdown-item" href="{{ url('/cases') }}">View All</a></li>
            </ul>
        </div>
    </div>
    <div class="card-body py-2 py-md-3">

        <canvas id="bar" style="width: 100%;"></canvas>
        <script>
            $(function() {

                function randomScalingFactor() {
                    return Math.floor(Math.random() * 100);
                }

                window.chartColors = {
                    red: 'rgb(255, 99, 132)',
                    orange: 'rgb(255, 159, 64)',
                    yellow: 'rgb(255, 205, 86)',
                    green: 'rgb(75, 192, 192)',
                    blue: 'rgb(54, 162, 235)',
                    purple: 'rgb(153, 102, 255)',
                    grey: 'rgb(201, 203, 207)'
                };

                var config = {
                    type: 'bar',
                    data: {
                        labels: <?php echo json_encode($labels); ?>,
                        datasets: [{
                            label: 'CA',
                            data: <?php echo json_encode($count); ?>,
                            backgroundColor: [
                                window.chartColors.red,
                                window.chartColors.orange,
                                window.chartColors.yellow,
                                window.chartColors.green,
                                window.chartColors.blue,
                                window.chartColors.grey,
                                'purple',
                                'black',
                                'green',
                                'blue',
                                'red',
                            ],
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        title: {
                            display: true,
                            text: 'Chart.js Bar Chart'
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1
                                },
                                title: {
                                    display: true,
                                    text: 'Number of Cases'
                                }
                            }
                        },
                        animation: {
                            animateScale: true,
                            animateRotate: true
                        }
                    }
                };

                var ctx = document.getElementById('bar').getContext('2d');
                var chart = new Chart(ctx, config);

                // Export as JPEG (White Background)
                var exportCAJpegBtn = document.getElementById('exportCAJpegBtn');
                exportCAJpegBtn.addEventListener('click', function() {
                    var canvas = document.getElementById('bar');
                    var context = canvas.getContext('2d');
                    context.globalCompositeOperation = 'destination-over';
                    context.fillStyle = 'white';
                    context.fillRect(0, 0, canvas.width, canvas.height);
                    var image = canvas.toDataURL('image/jpeg', 1.0)
                        .replace('image/jpeg', 'image/octet-stream');
                    var link = document.createElement('a');
                    link.href = image;
                    link.download = 'ca_cases.jpg';
                    link.click();
                });

                // Export as CSV
                var exportCACsvBtn = document.getElementById('exportCACsvBtn');
                exportCACsvBtn.addEventListener('click', function() {
                    var labels = <?php echo json_encode($labels); ?>;
                    var countData = <?php echo json_encode($count); ?>;
                    var totalCases = <?php echo json_encode($totalCases); ?>;

                    var csvContent = "data:text/csv;charset=utf-8,";
                    csvContent += "CA,Percentage,Actual Number\n";
                    for (var i = 0; i < labels.length; i++) {
                        var percentage = Math.round((countData[i] / totalCases) * 100);
                        var row = labels[i] + ',' + percentage + '%,' + countData[i];
                        csvContent += row + "\r\n";
                    }

                    var encodedUri = encodeURI(csvContent);
                    var link = document.createElement("a");
                    link.setAttribute("href", encodedUri);
                    link.setAttribute("download", "ca_cases.csv");
                    document.body.appendChild(link);
                    link.click();
                });

            });
        </script>

    </div>
</div>
