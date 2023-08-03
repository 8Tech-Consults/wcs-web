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

<div class="card  mb-4 mb-md-5 border-0">
    <!--begin::Header-->
    <div class="d-flex justify-content-between px-3 px-md-4 ">
        <h3>
            <b>Specimens Of Interest</b>
        </h3>
        <div class="dropdown">
            <button class="btn btn-sm btn-primary mt-md-4 mt-4 dropdown-toggle" type="button" id="exportDropdown"
                data-bs-toggle="dropdown" aria-expanded="false">
                Action
            </button>
            <ul class="dropdown-menu" aria-labelledby="exportDropdown">
                <li><a class="dropdown-item" href="#" id="exportSpecimenJpegBtn">Export JPEG</a></li>
                <li><a class="dropdown-item" href="#" id="exportSpecimenCsvBtn">Export CSV</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="{{ url('/cases') }}">View All</a></li>
            </ul>
        </div>
    </div>
    <div class="card-body py-2 py-md-3">
        <canvas id="bar-line" style="width: 100%;"></canvas>
        <script>
            $(function () {

                function randomScalingFactor() {
                    return Math.floor(Math.random() * 100);
                }

                window.chartColors = {
                    red: 'rgb(255, 99, 132)',
                    orange: 'rgb(255, 159, 64)',
                    yellow: 'rgb(255, 205, 86)',
                    green: '#277C61',
                    blue: 'rgb(54, 162, 235)',
                    purple: 'rgb(153, 102, 255)',
                    grey: 'rgb(201, 203, 207)'
                };

                var chartData = {
                    labels: @json($labels),
                    datasets: [{
                        type: 'line',
                        label: 'Ivory',
                        borderColor: window.chartColors.red,
                        borderWidth: 3,
                        data: <?php echo json_encode($ivory); ?>
                    }, {
                        type: 'line',
                        label: 'Pangolin Scales',
                        borderColor: window.chartColors.blue,
                        borderWidth: 3,
                        data: <?php echo json_encode($pangolin_scales); ?>
                    }, {
                        type: 'line',
                        label: 'Hippo Teeth',
                        borderColor: window.chartColors.orange,
                        borderWidth: 3,
                        data: <?php echo json_encode($hippo_teeth); ?>
                    }, {
                        type: 'bar',
                        label: 'Total Cases',
                        backgroundColor: window.chartColors.green,
                        data: <?php echo json_encode($data); ?>
                    }]
                };

                var ctx = document.getElementById('bar-line').getContext('2d');
                var chart = new Chart(ctx, {
                    type: 'bar',
                    data: chartData,
                    options: {
                        responsive: true,
                        plugins: {
                            tooltip: {
                                mode: 'index',
                                intersect: true
                            }
                        }
                    }
                });

                // Export as JPEG (White Background)
                var exportSpecimenJpegBtn = document.getElementById('exportSpecimenJpegBtn');
                exportSpecimenJpegBtn.addEventListener('click', function () {
                    var canvas = document.getElementById('bar-line');
                    var context = canvas.getContext('2d');
                    context.globalCompositeOperation = 'destination-over';
                    context.fillStyle = 'white';
                    context.fillRect(0, 0, canvas.width, canvas.height);
                    var image = canvas.toDataURL('image/jpeg', 1.0)
                        .replace('image/jpeg', 'image/octet-stream');
                    var link = document.createElement('a');
                    link.href = image;
                    link.download = 'specimen_of_interest.jpg';
                    link.click();
                });

                // Export as CSV
                var exportSpecimenCsvBtn = document.getElementById('exportSpecimenCsvBtn');
                exportSpecimenCsvBtn.addEventListener('click', function () {
                    var labels = JSON.parse('<?php echo json_encode($labels); ?>');
                    var ivoryData = <?php echo json_encode($ivory); ?>;
                    var pangolinData = <?php echo json_encode($pangolin_scales); ?>;
                    var hippoData = <?php echo json_encode($hippo_teeth); ?>;
                    var totalCasesData = <?php echo json_encode($data); ?>;

                    var csvContent = "data:text/csv;charset=utf-8,";
                    csvContent += "Month,Ivory,Pangolin Scales,Hippo Teeth,Total Cases\n";
                    for (var i = 0; i < labels.length; i++) {
                        var row = labels[i] + ',' + ivoryData[i] + ',' + pangolinData[i] + ',' + hippoData[i] + ',' + totalCasesData[i];
                        csvContent += row + "\r\n";
                    }

                    var encodedUri = encodeURI(csvContent);
                    var link = document.createElement("a");
                    link.setAttribute("href", encodedUri);
                    link.setAttribute("download", "specimen_of_interest.csv");
                    document.body.appendChild(link);
                    link.click();
                });

            });
        </script>
    </div>
</div>
