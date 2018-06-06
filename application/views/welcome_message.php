<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="<?php echo base_url() . 'CSS/bootstrap.min.css'; ?>" >
    <link rel="stylesheet" href="<?php echo base_url() . 'CSS/cover.css' ;?>">
    <title>Pan-cancer DNA methylation pattern mining and visualization for biomarker discovery</title>
</head>
<body>
<div class="cover-container d-flex w-100 h-100 p-3 mx-auto flex-column">
<?php $this->load->view('template/header'); ?>
<main role="main" class="inner hover">
    <h1 class="cover-heading"> MethylDB</h1>
    <p class="lead">Pan-cancer DNA methylation pattern mining and visualization for biomarker discovery</p>
    <p class="lead">
        <a href="dashboard" class="btn btn-lg btn-secondary">
            Go to dashboard
        </a>
    </p>
</main>
<?php $this->load->view('template/footer'); ?>
</div>
<script type="text/javascript" src="<?php echo base_url() . 'JS/jquery.js'; ?>" ></script>
<script src="<?php echo base_url() . 'JS/popper.js'; ?>" type="text/javascript"></script>
<script src="<?php echo base_url() . 'JS/bootstrap.js'; ?>" type="text/javascript"></script>
</body>
</html>