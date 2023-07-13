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
            <b>Suspects Vs Actions</b>
        </h3>
        <div class="btn-group">
            <button type="button" class="btn btn-primary mt-md-4 mt-4 dropdown-toggle" data-bs-toggle="dropdown"
                aria-expanded="false">
                Action
            </button>
            <ul class="dropdown-menu">
                <li>
                    <a class="dropdown-item" href="#" id="exportJpegBtn">Export JPEG</a>
                </li>
                <li>
                    <a class="dropdown-item" href="#" id="exportCsvBtn">Export CSV</a>
                </li>
                <li>
                    <hr class="dropdown-divider">
                </li>
                <li>
                    <a class="dropdown-item" href="{{ url('/case-suspects') }}">View All</a>
                </li>
            </ul>
        </div>
    </div>
    <div class="card-body py-2 py-md-3">
        <canvas id="line-stacked" style="width: 100%;"></canvas>
    </div>
</div>


<script>
    $(function () {

        function randomScalingFactor() {
            return Math.floor(Math.random() * 100);
        }

        window.chartColors = {
            red: 'rgb(255, 99, 132)',
            purple: 'rgb(153, 102, 255)',
            yellow: 'rgb(255, 205, 86)',
            orange: 'rgb(255, 159, 64)',
            green: 'rgb(75, 192, 192)',
            blue: 'rgb(54, 162, 235)',
            grey: 'rgb(201, 203, 207)'
        };

        var config = {
            type: 'line',
            data: {
                labels: <?php echo json_encode($labels); ?>,
                datasets: [{
                    label: 'Number of Suspects',
                    borderColor: window.chartColors.red,
                    backgroundColor: window.chartColors.red,
                    data: <?php echo json_encode($created_at); ?>,
                }, {
                    label: 'In Court',
                    borderColor: window.chartColors.blue,
                    backgroundColor: window.chartColors.blue,
                    data: <?php echo json_encode($is_suspect_appear_in_court); ?>,
                }, {
                    label: 'Convicted',
                    borderColor: window.chartColors.purple,
                    backgroundColor: window.chartColors.purple,
                    data: <?php echo json_encode($is_convicted); ?>,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    tooltip: {
                        mode: 'index'
                    }
                },
                scales: {
                    x: {
                        display: true,
                        title: {
                            display: true,
                            text: 'Month'
                        }
                    },
                    y: {
                        stacked: true,
                        display: true,
                        title: {
                            display: true,
                            text: 'Value'
                        }
                    }
                }
            }
        };

        var ctx = document.getElementById('line-stacked').getContext('2d');
        var chart = new Chart(ctx, config);

        // Export as JPEG (White Background)
        var exportJpegBtn = document.getElementById('exportJpegBtn');
        exportJpegBtn.addEventListener('click', function () {
            var canvas = document.getElementById('line-stacked');
            var image = canvas.toDataURL('image/jpeg', 1.0)
                .replace('image/jpeg', 'image/octet-stream');
            var link = document.createElement('a');
            link.href = image;
            link.download = 'suspects_vs_actions.jpg';
            link.click();
        });

        // Export as CSV
        var exportCsvBtn = document.getElementById('exportCsvBtn');
        exportCsvBtn.addEventListener('click', function () {
            var labels = <?php echo json_encode($labels); ?>;
            var createdData = <?php echo json_encode($created_at); ?>;
            var courtData = <?php echo json_encode($is_suspect_appear_in_court); ?>;
            var convictedData = <?php echo json_encode($is_convicted); ?>;

            var csvContent = "data:text/csv;charset=utf-8,";
            csvContent += "Month,Number of Suspects,In Court,Convicted\n";
            for (var i = 0; i < labels.length; i++) {
                var row = labels[i] + ',' + createdData[i] + ',' + courtData[i] + ',' + convictedData[i];
                csvContent += row + "\r\n";
            }

            var encodedUri = encodeURI(csvContent);
            var link = document.createElement("a");
            link.setAttribute("href", encodedUri);
            link.setAttribute("download", "suspects_vs_actions.csv");
            document.body.appendChild(link);
            link.click();
        });
    });
</script>
