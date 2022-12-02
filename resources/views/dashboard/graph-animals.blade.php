<?php
use App\Models\Utils;
?><style>
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
            <b>Most affected animals</b>
        </h3>
        <div>
            <a href="{{ url('/case-suspects') }}" class="btn btn-sm btn-primary mt-md-4 mt-4">
                View All
            </a>
        </div>
    </div>
    <div class="card-body py-2 py-md-3">

        <canvas id="graph_animals" style="width: 100%;"></canvas>
        <script>
            $(function() {

                function randomScalingFactor() {
                    return Math.floor(Math.random() * 100)
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
                    type: 'pie',
                    data: {
                        datasets: [{
                            data: [
                                randomScalingFactor(),
                                randomScalingFactor(),
                                randomScalingFactor(),
                                randomScalingFactor(),
                                randomScalingFactor(),
                            ],
                            backgroundColor: [
                                window.chartColors.red,
                                window.chartColors.orange,
                                window.chartColors.yellow,
                                window.chartColors.green,
                                window.chartColors.blue,
                            ],
                            label: 'Dataset 1'
                        }],
                        labels: [
                            'Panther',
                            'Hyena',
                            'Gorilla',
                            'Giraffe',
                            'Lion'
                        ]
                    },
                    options: {
                        responsive: true,
                        legend: {
                            position: 'top',
                        },
                        title: {
                            display: true,
                            text: 'Chart.js Doughnut Chart'
                        },
                        animation: {
                            animateScale: true,
                            animateRotate: true
                        }
                    }
                };

                var ctx = document.getElementById('graph_animals').getContext('2d');
                new Chart(ctx, config);
            });
        </script>

    </div>
</div> 