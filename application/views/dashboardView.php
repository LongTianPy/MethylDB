<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="/MethylDB/CSS/bootstrap.min.css" >
    <link rel="stylesheet" href="/MethylDB/CSS/dashboard.css">
    <title>Pan-cancer DNA methylation pattern mining and visualization for biomarker discovery</title>
</head>
<body>
<nav class="navbar navbar-dark fixed-top bg-dark flex-md-nowrap p-0 shadow">
    <a class="navbar-brand col-sm-3 col-md-2 mr-0" href="<?php echo base_url(); ?>">MethylDB</a>
    <span class="w-100 navbar-text">Pan-cancer DNA methylation pattern mining and visualization for biomarker discovery</span>
    <ul class="navbar-nav px-3">
        <li class="nav-item text-nowrap">
            <a class="nav-link" href="index.php/About">About</a>
        </li>
    </ul>
</nav>
<div class="container-fluid">
    <div class="row">
<!--        <nav class="col-md-2 d-none d-md-block bg-light sidebar">-->
<!--            <div class="sidebar-sticky">-->
<!--                <ul class="nav flex-column">-->
<!--                    <li class="nav-item">-->
<!--                        <h5>-->
<!--                        <a class="nav-link active" href="#">-->
<!--                            <span data-feather="home"></span>-->
<!--                            Dashboard <span class="sr-only">(current)</span>-->
<!--                        </a>-->
<!--                        </h5>-->
<!--                    </li>-->
<!--                    <li class="nav-item" id="search_functions">-->
<!--                        <h5>-->
<!--                        <a class="nav-link" href="#">-->
<!--                            <span data-feather="file"></span>-->
<!--                            Search-->
<!--                        </a>-->
<!--                        </h5>-->
<!--                        <div class="card">-->
<!--                            <div class="card-header" id="search_by_id">-->
<!--                                <h6 class="mb-0">-->
<!--                                    <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#form_by_id" aria-expanded="false" aria-controls="form_by_id">-->
<!--                                        Search by CpG ID-->
<!--                                    </button>-->
<!--                                </h6>-->
<!--                            </div>-->
<!--                            <div id="form_by_id" class="collapse show" aria-labelledby="search_by_id" data-parent="#search_functions" >-->
<!--                                <div class="card-body">-->
<!--                                    <!--                                --><?php ////echo $search_by_id ;?>
<!--                                    <form method="post" id="form_search_by_id" action="Dashboard">-->
<!--                                        <div class="form-group">-->
<!--                                            <label for="cpg_id">CpG probe ID</label>-->
<!--                                            <input type="text" class="form-control" name="cpg_id" id="cpg_id" placeholder="e.g. cg00000029" required>-->
<!--                                        </div>-->
<!--                                        <button type="submit" class="btn btn-primary">Go</button>-->
<!--                                    </form>-->
<!--                                </div>-->
<!--                            </div>-->
<!--                        </div>-->
<!--                        <div class="card">-->
<!--                            <div class="card-header" id="search_by_gene">-->
<!--                                <h6 class="mb-0">-->
<!--                                    <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#form_by_gene" aria-expanded="false" aria-controls="form_by_gene">-->
<!--                                        Search by gene name-->
<!--                                    </button>-->
<!--                                </h6>-->
<!--                            </div>-->
<!--                            <div id="form_by_gene" class="collapse" aria-labelledby="search_by_gene" data-parent="#search_functions">-->
<!--                                <div class="card-body">-->
<!--<!--                                    --><?php ////echo $search_by_gene ;?>
<!--                                    search by gene name content-->
<!--                                </div>-->
<!--                            </div>-->
<!--                        </div>-->
<!--                        <div class="card">-->
<!--                            <div class="card-header" id="search_by_region">-->
<!--                                <h6 class="mb-0">-->
<!--                                    <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#form_by_region" aria-expanded="false" aria-controls="form_by_region">-->
<!--                                        Search by genomic region-->
<!--                                    </button>-->
<!--                                </h6>-->
<!--                            </div>-->
<!--                            <div id="form_by_region" class="collapse" aria-labelledby="search_by_region" data-parent="#search_functions">-->
<!--                                <div class="card-body">-->
<!--<!--                                    --><?php ////echo $search_by_region; ?>
<!--                                    Search by genomic region content-->
<!--                                </div>-->
<!--                            </div>-->
<!--                        </div>-->
<!--                    </li>-->
<!--                </ul>-->
<!--            </div>-->
<!--        </nav>-->
        <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Dashboard</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <div class="btn-group mr-2">
                        <button class="btn btn-sm btn-outline-secondary">Share</button>
                        <button class="btn btn-sm btn-outline-secondary">Export</button>
                    </div>
                </div>
            </div>
            <canvas class="my-4 w-100" id="myChart" width="900" height="600"></canvas>
            <h2>Methylation beta values</h2>
            <table class="table table-striped table-sm">

            </table>
        </main>
    </div>
</div>
<script src="/MethylDB/JS/jquery.js" type="text/javascript"></script>
<script src="/MethylDB/JS/popper.js" type="text/javascript"></script>
<script src="/MethylDB/JS/bootstrap.js" type="text/javascript"></script>
<script src="/MethylDB/JS/feather.js" type="text/javascript"></script>
<script>
    feather.replace()
</script>
<script src="https://cdn.plot.ly/plotly-latest.js"></script>
<script src="/MethylDB/JS/dashboard.js" type="text/javascript"></script>
<!--<script src="distrochart.js" charset="utf-8"></script>
<script src="/MethylDB/JS/chart.js" type="text/javascript"></script>
<script>
    var ctx = document.getElementById("myChart");
    var myChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"],
            datasets: [{
                data: [15339, 21345, 18483, 24003, 23489, 24092, 12034],
                lineTension: 0,
                backgroundColor: 'transparent',
                borderColor: '#007bff',
                borderWidth: 4,
                pointBackgroundColor: '#007bff'
            }]
        },
        options: {
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: false
                    }
                }]
            },
            legend: {
                display: false,
            }
        }
    });
</script>-->
</body>
</html>