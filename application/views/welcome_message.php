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

    <title>Pan-cancer DNA methylation pattern mining and visualization for biomarker discovery</title>
</head>
<body>
<div class="container.fluid">
<?php $this->load->view('template/header'); ?>


<?php $this->load->view('template/footer'); ?>
</div>
<script type="text/javascript" src="<?php echo base_url() . 'JS/jquery.js'; ?>" ></script>
<script src="<?php echo base_url() . 'JS/popper.js'; ?>" type="text/javascript"></script>
<script src="<?php echo base_url() . 'JS/bootstrap.js'; ?>" type="text/javascript"></script>
</body>
</html>