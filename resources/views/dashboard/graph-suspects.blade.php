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
            <b>Crimes Vs Actions</b>
        </h3>
        <div>
            <a href="{{ url('/case-suspects') }}" class="btn btn-sm btn-primary mt-md-4 mt-4">
                View All
            </a>
        </div>
    </div>
    <div class="card-body py-2 py-md-3">

        {{--  
 
            $data['is_jailed'][] = $is_jailed;
            $data['is_fined'][] = $is_fined;
            $data['labels'][] = Utils::month($max);
    --}}
        <canvas id="line-stacked" style="width: 100%;"></canvas>
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
                    type: 'line',
                    data: {
                        labels: JSON.parse('<?php echo json_encode($labels); ?>'),
                        datasets: [{
                                label: 'Number of Suspects',
                                borderColor: window.chartColors.red,
                                backgroundColor: window.chartColors.red,
                                data: JSON.parse('<?php echo json_encode($created_at); ?>'),
                            }, {
                                label: 'Number of arrests',
                                borderColor: window.chartColors.blue,
                                backgroundColor: window.chartColors.blue,
                                data: JSON.parse('<?php echo json_encode($is_suspects_arrested); ?>'),
                            }, {
                                label: 'Suspects appeared in court',
                                borderColor: window.chartColors.green,
                                backgroundColor: window.chartColors.green,
                                data: JSON.parse('<?php echo json_encode($is_suspect_appear_in_court); ?>'),
                            },
                            {
                                label: 'Number of convictions',
                                borderColor: window.chartColors.yellow,
                                backgroundColor: window.chartColors.yellow,
                                data: JSON.parse('<?php echo json_encode($is_convicted); ?>'),
                            },
                        ]
                    },
                    options: {
                        responsive: true,
                        title: {
                            display: true,
                            text: 'Chart.js Line Chart - Stacked Area'
                        },
                        tooltips: {
                            mode: 'index',
                        },
                        hover: {
                            mode: 'index'
                        },
                        scales: {
                            xAxes: [{
                                scaleLabel: {
                                    display: true,
                                    labelString: 'Month'
                                }
                            }],
                            yAxes: [{
                                stacked: true,
                                scaleLabel: {
                                    display: true,
                                    labelString: 'Value'
                                }
                            }]
                        }
                    }
                };

                var ctx = document.getElementById('line-stacked').getContext('2d');
                new Chart(ctx, config);
            });
        </script>

    </div>
</div>
