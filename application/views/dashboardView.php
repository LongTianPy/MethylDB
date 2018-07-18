<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.1.0/css/all.css" integrity="sha384-lKuwvrZot6UHsBSfcMvOkWwlCMgc0TaWr+30HWe3a4ltaBwTZhyTEggF5tJv8tbt" crossorigin="anonymous">
    <link rel="stylesheet" href="/MethylDB/CSS/bootstrap.min.css" >
    <link rel="stylesheet" href="/MethylDB/CSS/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/10.0.2/css/bootstrap-slider.css">
    <link rel="stylesheet" type="text/css" href="https://igv.org/web/release/1.0.9/igv-1.0.9.css">
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
        <nav class="col-md-2 d-none d-md-block bg-light sidebar">
            <div class="sidebar-sticky">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <h5>
                            <a class="nav-link active" href="#">
                                <span data-feather="home"></span>
                                Dashboard <span class="sr-only">(current)</span>
                            </a>
                        </h5>
                    </li>
                    <li class="nav-item" id="search_functions">
                        <h5>
                            <a class="nav-link" href="#">
                                <span data-feather="file"></span>
                                Search
                            </a>
                        </h5>
                        <div class="card">
                            <div class="card-header" id="search_by_id">
                                <h6 class="mb-0">
                                    <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#form_by_id" aria-expanded="false" aria-controls="form_by_id">
                                        Search by CpG ID
                                    </button>
                                </h6>
                            </div>
                            <div id="form_by_id" class="collapse show" aria-labelledby="search_by_id" data-parent="#search_functions" >
                                <div class="card-body">
                                    <form method="get" id="form_search_by_id" action="Dashboard">
                                        <div class="form-group">
                                            <label for="cpg_id">CpG probe ID</label>
                                            <input type="text" class="form-control" name="cpg_id" id="cpg_id" placeholder="e.g. cg00000029" required>
                                        </div>
                                        <button type="submit" class="btn btn-primary">Go</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header" id="search_by_gene">
                                <h6 class="mb-0">
                                    <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#form_by_gene" aria-expanded="false" aria-controls="form_by_gene">
                                        Search by gene name
                                    </button>
                                </h6>
                            </div>
                            <div id="form_by_gene" class="collapse" aria-labelledby="search_by_gene" data-parent="#search_functions">
                                <div class="card-body">
                                    <form method="get" id="form_search_by_gene" action="Dashboard">
                                        <div class="form-group">
                                            <label for="gene">Gene name</label>
                                            <input type="text" class="form-control" name="gene" id="gene" placeholder="e.g. EGFR" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="upstream">Upstream</label>
                                            <input type="text" class="form-control" name="upstream" id="upstream" placeholder="Default 0, optional" />bp
                                        </div>
                                        <div class="form-group">
                                            <label for="downstream">Downstream</label>
                                            <input type="text" class="form-control" name="downstream" id="downstream" placeholder="Default 0, optional" />bp
                                        </div>
                                        <button type="submit" class="btn btn-primary">Go</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header" id="search_by_region">
                                <h6 class="mb-0">
                                    <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#form_by_region" aria-expanded="false" aria-controls="form_by_region">
                                        Search by genomic region
                                    </button>
                                </h6>
                            </div>
                            <div id="form_by_region" class="collapse" aria-labelledby="search_by_region" data-parent="#search_functions">
                                <div class="card-body">
                                    <form method="get" id="form-search_by_region" action="Dashboard">
                                        <div class="form-group">
                                            <label for="chr_id">Chromosome</label>
                                            <select class="form-control" id="chr_id" name="chr_id">
                                                <?php
                                                for ($i=1;$i<=22;$i++) {
                                                    echo "<option>{$i}</option>";
                                                }
                                                ?>
                                                <option>X</option>
                                                <option>Y</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="from">From</label>
                                            <input type="text" id="from" class="form-control" name="from" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="to">To</label>
                                            <input type="text" id="to" class="form-control" name="to" required>
                                        </div>
                                        <button type="submit" class="btn btn-primary">Go</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </nav>
        <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Dashboard</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <div class="btn-group mr-2">
                        <button class="btn btn-sm btn-outline-secondary">Share</button>
                        <?php
                        if (isset($download)){
                            echo "<button class='btn btn-sm btn-outline-secondary' href='{$download}' download>Export</button>";
                        }else{
                            echo "<button class='btn btn-sm btn-outline-secondary'>Export</button>";
                        }
                        ?>
                    </div>
                ort</b
            </div>
            <?php
            if (isset($igv)){
                echo "<div id='igvDiv' style='padding-top:10px;padding-bottom: 10px;border: 1px solid lightgrey'></div>";
            }
            ?>
            <div class="my-4 w-100 figure-img img-fluid rounded" id="myChart" width="900" height="600" style="min-height: 600px;">
                <?php
                if (isset($place_holder)) {
                    echo $place_holder;
                }
                ?>
            </div>
            <h2>Methylation beta values</h2>
            <table class="table table-striped table-sm">

            </table>
        </main>
    </div>
</div>
<script src="/MethylDB/JS/jquery.js" type="text/javascript"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.js" type="text/javascript"></script>
<script src="/MethylDB/JS/popper.js" type="text/javascript"></script>
<script src="/MethylDB/JS/bootstrap.js" type="text/javascript"></script>
<script src="/MethylDB/JS/feather.js" type="text/javascript"></script>
<script>
    feather.replace()
</script>
<script type="text/javascript" src="https://igv.org/web/release/1.0.9/igv-1.0.9.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/10.0.2/bootstrap-slider.js" type="text/javascript"></script>
<script src="https://cdn.plot.ly/plotly-latest.js"></script>
<?php
if (isset($igv)){
    echo "<script>var chromosome={$chr}; var start={$start}; var end={$end}</script>";
    echo "<script src='/MethylDB/JS/run_igv.js' type='text/javascript'></script>";
}elseif (isset($js_parameters)){
    echo $js_parameters;
    echo "<script src='/MethylDB/JS/dashboard.js' type='text/javascript'></script>";
}
?>
</body>
</html>