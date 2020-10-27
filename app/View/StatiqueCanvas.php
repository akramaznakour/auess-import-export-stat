<div style=" height:400px !important;">
    <canvas  id="<?= $canvas["id"] ?>"></canvas>
    <hr/>
</div>

<script>
    var config_<?= $canvas["id"] ?> = {
        type: 'line',
        data: {
            labels: <?= json_encode( $canvas["labels"] );?> ,
            datasets: [
				<?php foreach ($canvas["datasets"] as $dataset): ?>
                {
                    label: '<?= $dataset["label"] ?>' ,
                    backgroundColor: window.chartColors.<?= $dataset["backgroundColor"] ?>,
                    borderColor: window.chartColors.<?= $dataset["borderColor"] ?>,
                    data: <?= json_encode($dataset["data"]) ?> ,
                    fill: false,
                },
				<?php endforeach; ?>
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            title: {
                display: true,
                text: '<?= $canvas["title"] ?>',
                fontSize: 20,
                padding: 20
            },
            tooltips: {
                mode: 'index',
                intersect: false,
            },
            hover: {
                mode: 'nearest',
                intersect: true
            },
            scales: {
                xAxes: [{
                    ticks: {
                        fontSize: 17
                    },
                    display: true,
                    scaleLabel: {
                        fontSize: 15,
                        fontStyle: "bold",
                        display: true,
                        labelString: '<?= $canvas["xAxesLabel"] ?>'
                    }
                }],
                yAxes: [{
                      ticks: {
                        fontSize: 12
                    },
                    display: true,
                    scaleLabel: {
                        fontStyle: "bold",
                        fontSize: 15,
                        display: true,
                        labelString: '<?= $canvas["yAxesLabel"] ?>'
                    }
                }]
            },
            legend: {
                labels: {
                // This more specific font property overrides the global property
                    fontSize: 15
                }
            }
        }
    };

    var ctx = document.getElementById('<?= $canvas["id"] ?>').getContext('2d');
    window.myLine = new Chart(ctx, config_<?= $canvas["id"] ?> );


    var colorNames = Object.keys(window.chartColors);

</script>