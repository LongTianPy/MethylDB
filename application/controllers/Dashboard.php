<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {
    public function index(){
        $place_holder = "";
//            $place_holder .= "<figure class='figure-img img-fluid rounded'>";
        $place_holder .= "<img src='/MethylDB/IMG/placeholding_img.png' id='placeholder_img'>";
//            $place_holder .= "</figure>";
        if(isset($_POST['cpg_id']) or isset($_GET['cpg_id'])){
            $data = $this->search_by_id();
            if (isset($data['script'])){
                $page_data = array(
                    'place_holder' => $place_holder,
                    'script' => $data['script'],
                );
            }elseif (isset($data['no_result'])){
                $page_data = array(
                    'place_holder' => $place_holder,
                    'msg' => "No record found, please modify your search."
                );
            }
            $this->load->view('dashboardView',$page_data);
        }elseif (isset($_GET['from']) and isset($_GET['to']) and !empty($_GET['from']) and !empty($_GET['to']) ){
            $data = $this->search_by_region();
            if (isset($data['script'])){
                $buttons = $this->create_buttons($data);
                $page_data = array(
                    'place_holder' => $place_holder,
                    'buttons' => $buttons,
                    'script' => $data['script'],
                );
            }else{
                $page_data = array(
                    'place_holder' => $place_holder,
                    'msg' => "No record found, please modify your search."
                );
            }
            $this->load->view('dashboardView',$page_data);
        }elseif (isset($_POST['gene']) or isset($_GET['gene'])){
            $data = $this->search_by_gene();
            if (isset($data['script'])){
//                $buttons = $this->create_buttons($data);
//                $buttons = $this->create_genome_view($data);
//                $buttons = $this->draw_div($data);
                $json_file = $this->generage_genome_data($data);

                $page_data = array(
                    'place_holder' => $place_holder,
//                    'buttons' => $buttons,
                    'json_file' => $json_file,
                    'genomeD3plot_css'=> '<link rel="stylesheet" href="/MethylDB/CSS/linearplot.css" ><link rel="stylesheet" href="/MethylDB/CSS/tracks.css" >',
                    'genomeD3plot_js' => '<script src="'. $json_file .'" type="text/javascript"></script><script src="/MethylDB/JS/d3.min.js" type="text/javascript"></script><script src="/MethylDB/JS/d3.tip.js" type="text/javascript"></script><script src="/MethylDB/JS/linearplot.js" type="text/javascript"></script><script src="/MethylDB/JS/linearbrush.js" type="text/javascript"></script><script src="/MethylDB/JS/run_linearplot.js" type="text/javascript"></script>',
                    'script' => $data['script'],
                    'js_parameters' => "<script>var start={$data['from']};var end={$data['to']};var json_file={$json_file}</script>"
                );
            }else{
                $page_data = array(
                    'place_holder' => $place_holder,
                    'msg' => "No record found, please modify your search."
                );
            }
            $this->load->view('dashboardView',$page_data);
        }else {

            $page_data = array(
                'place_holder' => $place_holder,
            );
            $this->load->view('dashboardView',$page_data);
        }

    }

    public function get_position($cpg_id){
        settype($cpg_id,'string');
        $cpg_id = "'".$cpg_id."'";
        $sql = "select CHR,MAPINFO from Probeset where Probeset_ID={$cpg_id}";
        $result = $this->db->query($sql)->row(0);
        $position = $result->MAPINFO;
        return $position;
    }

    public function create_range_bar($data){
        $positions = array();
        $cpg_ids = explode(",",$data['cpg_ids']);
        foreach ($cpg_ids as $cpg_id) {
            $pos = $this->get_position($cpg_id);
            $positions[] = $pos;
        }
        $start = $data['from'];
        $end = $data['to'];
        $length = $end - $start;
        $data_slider_ticks = "[" . $start. "," . implode(",",$positions) . "," . $end . "]";
        $data_slider_ticks_labels = "[" . $start . "," .$data['cpg_ids'] . "," . $end . "]";
        $percentages = array();
        foreach ($positions as $pos){
            $percentages[] = ($pos-$start)/$length * 100;
        }
        $ticks_positions = "[0,".implode(",",$percentages).",100]";
        $range = "<input class='range-field my-4 w-100' id='select_cpg' type='range' data-slider-ticks='{$data_slider_ticks}' data-slider-ticks-snap-bounds='30' data-slider-ticks-labels='{$data_slider_ticks_labels}' ticks_positions='{$ticks_positions}' onchange='javscript:get_cpg(this.value)' oninput='javscript:get_cpg(this.value)'>";
        echo "<script>";
        echo "$(#select_cpg).slider({
            ticks: {$data_slider_ticks},
            tick_labels: {$data_slider_ticks_labels},
            ticks_snap_bounds: 30
        
        });";
        echo "</script>";
        return $range;
    }

    public function create_buttons($data){
        $cpg_ids = explode(",",$data['cpg_ids']);
        $buttons = "";
        $buttons .= "<div>";
        $buttons .= "<div class='btn-group btn-group-sm w-100' role='group' >";
        if (isset($data['gene'])){
            $gene = $data['gene'];
            $buttons .= "<label>{$gene}&nbsp&nbsp&nbsp&nbsp   </label>";
        }else{
            $chr = $data['chr'];
            $start = $data['from'];
            $end = $data['to'];
            $buttons .= "<label>Chr{$chr}: {$start} - {$end}&nbsp&nbsp&nbsp&nbsp    </label>";
        }
        foreach ($cpg_ids as $cpg_id) {
            $buttons .= "<button type='button' class='btn btn-secondary cpg_buttons' value='{$cpg_id}' onclick='javascript:makeplot(this.value)' style='width: 2em;' data-container='body' data-toggle='popover' data-placement='top' data-content='{$cpg_id}' data-trigger='hover'>      </button>";
        }
        $buttons .= "</div>";
        $buttons .= "<label style='color:lightgrey'>Move mouse over the above button(s) to see each CpG island, click to see distribution of methylation levels in each cancer type.</label>";
        return $buttons;
    }


    public function mark_cpg($data){
        $div = "";
        $from = $data['from'];
        $to = $data['to'];
        $range = $to-$from;
        $cpg_ids = explode(",",$data['cpg_ids']);
        $previous = $from;
        for ($i=0;$i<count($cpg_ids);$i++){
            $cpg_id = $cpg_ids[$i];
            $sql = "select * from Probeset where Probeset_ID='{$cpg_id}'";
            $result = $this->db->query($sql)->row(0);
            $chr = $result->CHR;
            $locus = $result->MAPINFO;
            $distance_to_previous = $locus - $previous;
            $relative_length = $distance_to_previous / $range * 1000;
            $div .= "<div class='d-inline-block h-100' style='width: {$relative_length}px;'>";
            $div .= "<div class='mr-0 h-100 popdetail' style='border-right: 2px solid #34495e;' id='{$cpg_id}'>";
            $div .= "<a onclick='javascript:makeplot(this.value)' value='{$cpg_id}'>  </a>";
            $div .= "</div>";
            $div .= "</div>";
            $div .= "<div style='display:none;' id='detail_{$cpg_id}'>";
            $div .= "<table class='table table-sm'>";
            $div .= "<thead><tr><th scope='col'>Info</th><th scope='col'>Value</th></tr></thead>";
            $div .= "<tbody>";
            $div .= "<tr><th scope='col'>Probe ID</th><td>{$cpg_id}</td></tr>";
            $div .= "<tr><th scope='col'>Chromosome</th><td>{$chr}</td></tr>";
            $div .= "<tr><th scope='col'>Locus</th><td>{$locus}</td></tr>";
            $div .= "</tbody>";
            $div .= "</table>";
            $div .= "</div>";
            $previous = $locus;
        }
        return $div;
    }

    public function draw_div($data){
        $div = "<div style='height: 100px;width: 1000px;'>";
//        $div .= $table;
        $div .= $this->mark_cpg($data);
        $div .= "</div>";
        return $div;
    }

    public function generage_genome_data($data){
        $chr = $data['chr'];
        $from = $data['from'];
        $to = $data['to'];
        $cpg_ids = explode(",",$data['cpg_ids']);
        $transcript_ids = $data['transcript_ID'];
        $gene_items = array();
        $exon_items = array();
        $cpg_items = array();
        $exon_id = 1;
        $gene_id = 1;
        $cpgid = 1;
        foreach ($transcript_ids as $transcript_id) {
            $sql = "select * from hg19 where transcript_ID={$transcript_id}";
            $result = $this->db->query($sql)->row(0);
            if ($result->strand == "+"){
                $strand = 1;
            }else{
                $strand = -1;
            }
            $gene_item = array(
                'id' => $gene_id,
                'start' => $result->txStart,
                'end' => $result->txEnd,
                'name' => $result->geneName,
                'strand' => $strand,
            );
            $gene_items[] = $gene_item;
            $exonStarts = explode(",",$result->exonStarts);
            $exonEnds = explode(",",$result->exonEnds);
            for ($i=0;$i<$result->exonCount;$i++){
                $exonStart = $exonStarts[$i];
                $exonEnd = $exonEnds[$i];
                $exon_item = array(
                    'id' => $exon_id,
                    'start' => $exonStart,
                    'end' => $exonEnd,
                    'name' => $result->geneName . '_exon_' . $exon_id,
                    'strand' => $strand,
                );
                $exon_items[] = $exon_item;
                $exon_id ++;
            }
            $gene_id ++;
        }
        foreach ($cpg_ids as $cpg_id){
            $sql = "select MAPINFO from Probeset where Probeset_ID='{$cpg_id}'";
            $cpg_result = $this->db->query($sql)->row(0);
            $cpg_item = array(
                'id' => $cpgid,
                'start' => $cpg_result->MAPINFO,
                'end' => $cpg_result->MAPINFO+1,
                'name' => $cpg_id,
            );
            $cpg_items[] = $cpg_item;
            $cpgid ++;
        }
        $genes = array(
            'trackName'=>'track1',
            'trackType'=>'stranded',
            'visible'=>true,
            'inner_radius' =>80,
            'outer_radius'=>120,
            'trackFeatures'=>'complex',
            'featureThreshold'=>7000000,
            'showLabels'=>true,
            'showTooltip'=>true,
            'items'=>$gene_items,
        );
        $exons = array(
            'trackName'=>'track2',
            'trackType'=>'stranded',
            'visible'=>true,
            'inner_radius' =>195,
            'outer_radius'=>234,
            'centre_line_stroke'=>"grey",
            'showLabels'=>true,
            'items'=>$exon_items,
        );
        $cpgs = array(
            'trackName'=>'gapTrack',
            'trackType'=>'gap',
            'inner_radius'=>25,
            'outer_radius'=>234,
            'linear_mouseclick'=>'linearClick',
            'showTooltip'=>true,
            'showLabels'=>true,
            'items'=>$cpg_items,
        );
        $tracks = array('tracks' =>array($genes,$exons,$cpgs));
//        $tracks = "<script>
//            var tracks = [
//                { trackName: 'track1',
//                  trackType: 'stranded',
//                  visible: true,
//                  inner_radius: 80,
//                  outer_radius: 120,
//                  trackFeatures: 'complex',
//                  featureThreshold: 7000000,
//                  mouseover_callback: 'islandPopup',
//                  mouseout_callback: 'islandPopupClear',
//                  linear_mouseclick: 'linearPopup',
//                  showLabels: true,
//                  showTooltip: true,
//                  linear_mouseclick: 'linearClick',
//                  items:
//                }
//
//            ]
//
//</script>";
        $track_jason = json_encode($tracks,JSON_PRETTY_PRINT);
        $filename = uniqid();
        $json_file = "/home/long-lamp-username/MethylDB/result/" . $filename . ".json";
        file_put_contents($json_file,$track_jason);
        $json_file_path = "/MethylDB/result/" . $filename . ".json";
        return $json_file_path;
    }


    public function search_by_id(){
        $input = "/home/long-lamp-username/MethylDB/mData_output.txt.gz";
        $output = "/home/long-lamp-username/MethylDB/result/" . uniqid() . ".txt";
        $python_scipt = "/home/long-lamp-username/Mayo_toolbox/prepare_boxplot_data.py";
        if (isset($_POST['cpg_id'])){
            $return_cpg = $this->input->post('cpg_id');
            $cpg_id = $this->input->post('cpg_id');
        } else {
            $return_cpg = $this->input->get('cpg_id');
            $cpg_id = $this->input->get('cpg_id');
        }
        settype($cpg_id,'string');
        $cpg_id = "'".$cpg_id."'";
        $sql = "select CHR,MAPINFO from Probeset where Probeset_ID={$cpg_id}";
//        exec("echo {$sql} > /home/long-lamp-username/MethylDB/result/search_by_id_sql.txt");
        $result = $this->db->query($sql)->result();
//        print_r($result);
        if (count($result)>0){
            $result = $result[0];
            $chr = $result->CHR;
            $position = $result->MAPINFO;
//        settype($position,"integer");
            $from = $position-1;
            $to = $position+1;
            $cmd = "tabix {$input} {$chr}:{$from}-{$to} -h > {$output}";
//        exec("echo {$cmd} > /home/long-lamp-username/MethylDB/result/tabix_cmd.txt");
            exec($cmd);
            $row_nums = count(file($output));
            settype($row_nums, 'integer');
            if ($row_nums > 1){
                $cmd = "python {$python_scipt} {$output}";
                $datafile = shell_exec($cmd);
                $this->session->set_userdata($datafile);
                $this->session->set_userdata($cpg_id);
                echo "<a id='datafile' style='display: none' value='{$datafile}'>{$datafile}</a>";
                echo "<a id='cpg_id' style='display: none' value='{$return_cpg}'>{$return_cpg}</a>";
                $call_this_script = '<script src="/MethylDB/JS/dashboard.js" type="text/javascript"></script>';
                $data = array('script'=>$call_this_script,);
            }else{
                $data = array('no_result' => 0,);
            }
        } else{
            $data = array('no_result' => 0,);
        }
        return $data;
    }

    public function search_by_gene(){
        $input = "/home/long-lamp-username/MethylDB/mData_output.txt.gz";
        $output = "/home/long-lamp-username/MethylDB/result/" . uniqid() . ".txt";
        $python_scipt = "/home/long-lamp-username/Mayo_toolbox/prepare_boxplot_multi.py";
        if (isset($_POST['gene'])){
            $gene = $this->input->post('gene');
        }else {
            $gene = $this->input->get('gene');
        }
        settype($gene,'string');
        strtoupper($gene);
        $transcripts = array();
        $sql = "select * from hg19 where geneName='{$gene}'";
        $result = $this->db->query($sql)->result();
        if (count($result)>0){
            foreach ($result as $row) {
                $transcripts[] = $row->transcript_ID;
            }
            $result = $result[0];
            $chr = $result->chrom;
            $chr = substr($chr,3);
            $start = $result->txStart;
            $end = $result->txEnd;
            $cmd = "tabix {$input} {$chr}:{$start}-{$end} -h > {$output}";
            exec($cmd);
            $row_nums = count(file($output));
            settype($row_nums, 'integer');
            if ($row_nums > 1){
                $cmd = "python {$python_scipt} {$output}";
                $returned = shell_exec($cmd);
                $returned = explode(",",$returned);
                $cpg_ids = array_slice($returned,0,-1);
                $cpg_ids_string = implode(",",$cpg_ids);
                $datafile = end($returned);
//        echo "<a id='datafile' style='display: none'>{$datafile}</a>";
//        echo "<a id='cpg_ids' style='display: none'>{$cpg_ids_string}</a>";
                $call_this_script = '<script src="/MethylDB/JS/dashboard_gene.js" type="text/javascript"></script>';
                $final_result = array(
                    'transcript_ID' => $transcripts,
                    'gene' => $gene,
                    'from' => $start,
                    'to' => $end,
                    'cpg_ids' => $cpg_ids_string,
                    'script' => $call_this_script,
                );
            }else{
                $final_result = array(
                    'no_result' => 0,
                );
            }
        } else {
            $final_result = array(
                'no_result' => 0,
            );
        }
        return $final_result;
    }

    public function search_by_region(){
        $input = "/home/long-lamp-username/MethylDB/mData_output.txt.gz";
        $output = "/home/long-lamp-username/MethylDB/result/" . uniqid() . ".txt";
        $python_scipt = "/home/long-lamp-username/Mayo_toolbox/prepare_boxplot_multi.py";
        if (isset($_GET['chr_id'])){
            $chr = $this->input->get('chr_id');
            $start = $this->input->get('from');
            $end = $this->input->get('to');
        }elseif (isset($_POST['chr_id'])){
            $chr = $this->input->post('chr_id');
            $start = $this->input->post('from');
            $end = $this->input->post('to');
        }
        $sql = "select * from hg19 where chrom='chr'{$chr} and txStart>{$start} and txEnd<{$end}";
        $result = $this->db->query($sql)->result();
        $transcripts = array();
        foreach ($result as $row) {
            $transcripts[] = $row->transcript_ID;
        }
        $cmd = "tabix {$input} {$chr}:{$start}-{$end} -h > {$output}";
        exec($cmd);
        $row_nums = count(file($output));
        settype($row_nums, 'integer');
        if ($row_nums > 1){
            $cmd = "python {$python_scipt} {$output}";
            $returned = shell_exec($cmd);
            $returned = explode(",",$returned);
            $cpg_ids = array_slice($returned,0,-1);
            $cpg_ids_string = implode(",",$cpg_ids);
            $datafile = end($returned);
            $call_this_script = '<script src="/MethylDB/JS/dashboard_gene.js" type="text/javascript"></script>';
            $final_result = array(
                'transcript_ID' => $transcripts,
                'chr' => $chr,
                'from' => $start,
                'to' => $end,
                'cpg_ids' => $cpg_ids_string,
                'script' => $call_this_script,
            );
            return $final_result;
        }else{
            $final_result = array(
                'no_result' => 0,
            );
        }
        return $final_result;
    }
}
?>
