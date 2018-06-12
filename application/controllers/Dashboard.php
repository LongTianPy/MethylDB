<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {
    public function index(){
        if(isset($_POST['cpg_id']) and !empty($_POST['cpg_id'])){
            $this->search_by_id();
            $this->load->view('dashboardView');
        }elseif (isset($_POST['chr_id']) and isset($_POST['from']) and isset($_POST['to']) and !empty($_POST['chr_id']) and !empty($_POST['from']) and !empty($_POST['to']) ){
            $this->search_by_region();
            $this->load->view('dashboardView');
        }elseif (isset($_POST['gene']) or isset($_GET['gene'])){
            $data = $this->search_by_gene();
            $range = $this->create_range_bar($data);
            $page_data = array(
              'range' => $range,
            );
            $this->load->view('dashboardView',$page_data);
        }else {
            $this->load->view('dashboardView');
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
        $positions = [];
        $cpg_ids = explode(",",$data['cpg_ids']);
        foreach ($cpg_ids as $cpg_id) {
            $pos = $this->get_position($cpg_id);
            $positions[] = $pos;
        }
        $start = $data['from'];
        $end = $data['to'];
        $length = $end - $start;
        $data_slider_ticks = "[" . $start . implode(",",$positions) . $end . "]";
        $data_slider_ticks_labels = "[" . $start .$data['cpg_ids'] . $end . "]";
        $percentages = [];
        foreach ($positions as $pos){
            $percentages[] = ($pos-$start)/$length * 100;
        }
        $ticks_positions = "[0,".implode(",",$percentages).",100]";
        $range = "<input class='range-field my-4 w-100' id='select_cpg' type='range' data-slider-ticks='{$data_slider_ticks_labels}' data-slider-ticks-snap-bounds='30' data-slider-ticks-labels='{$data_slider_ticks_labels}' ticks_positions='{$ticks_positions}' onchange='javscript:get_cpg(this.value)' oninput='javscript:get_cpg(this.value)'>";
        return $range;
    }

    public function search_by_id(){
        $input = "/home/long-lamp-username/MethylDB/mData_output.txt.gz";
        $output = "/home/long-lamp-username/MethylDB/result/" . uniqid() . ".txt";
        $python_scipt = "/home/long-lamp-username/Mayo_toolbox/prepare_boxplot_data.py";
        $cpg_id = $this->input->post('cpg_id');
//        $cpg_id = $this->input->get('cpg_id');
        settype($cpg_id,'string');
        $cpg_id = "'".$cpg_id."'";
        $sql = "select CHR,MAPINFO from Probeset where Probeset_ID={$cpg_id}";
//        exec("echo {$sql} > /home/long-lamp-username/MethylDB/result/search_by_id_sql.txt");
        $result = $this->db->query($sql)->row(0);
        $chr = $result->CHR;
        $position = $result->MAPINFO;
//        settype($position,"integer");
        $from = $position-1;
        $to = $position+1;
        $cmd = "tabix {$input} {$chr}:{$from}-{$to} -h > {$output}";
//        exec("echo {$cmd} > /home/long-lamp-username/MethylDB/result/tabix_cmd.txt");
        exec($cmd);
        $cmd = "python {$python_scipt} {$output}";
        $datafile = shell_exec($cmd);
        $this->session->set_userdata($datafile);
        $this->session->set_userdata($cpg_id);
//        echo "
//        <script type='text/javascript'>var datafile='{$datafile}';
//        var cpg_id={$cpg_id};</script>
//        ";
        echo "<a id='datafile' style='display: none' value='{$datafile}'>{$datafile}</a>";
        echo "<a id='cpg_id' style='display: none' value='{$cpg_id}'>{$cpg_id}</a>";
    }

    public function search_by_gene(){
        $input = "/home/long-lamp-username/MethylDB/mData_output.txt.gz";
        $output = "/home/long-lamp-username/MethylDB/result/" . uniqid() . ".txt";
        $python_scipt = "/home/long-lamp-username/Mayo_toolbox/prepare_boxplot_multi.py";
        $gene = $this->input->post('gene');
        $gene = $this->input->get('gene');
        settype($gene,'string');
        strtoupper($gene);
        $sql = "select * from Gene where gene='{$gene}'";
        $result = $this->db->query($sql)->row(0);
        $chr = $result->CHR;
        $start = $result->start;
        $end = $result->end;
        $cmd = "tabix {$input} {$chr}:{$start}-{$end} -h > {$output}";
        exec($cmd);
        $cmd = "python {$python_scipt} {$output}";
        $returned = shell_exec($cmd);
        $returned = explode(",",$returned);
        $cpg_ids = array_slice($returned,0,-1);
        $cpg_ids_string = implode(",",$cpg_ids);
        $datafile = end($returned);
//        echo "<a id='datafile' style='display: none'>{$datafile}</a>";
//        echo "<a id='cpg_ids' style='display: none'>{$cpg_ids_string}</a>";
        $final_result = array(
            'from' => $start,
            'to' => $end,
            'cpg_ids' => $cpg_ids_string,
        );
        return $final_result;
    }

}
?>
