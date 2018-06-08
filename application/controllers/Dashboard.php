<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {
    public function index(){
        if(isset($_POST['cpg_id']) && !empty($_POST['cpg_id'])){
            $this->search_by_id();
        }
        $this->load->view('dashboardView');
    }

    public function parse_result($file){
        $lines = file($file,FILE_IGNORE_NEW_LINES |FILE_SKIP_EMPTY_LINES);
        $data = array_map(function($v){
            return array_filter(preg_split("/\s+/", $v));
        }, $lines);
        $header = array_shift($data);
        $data = array_map(function ($v)use($header){
           return array_combine($header,$v);
        },$data);
        return $data;
    }

    public function search_by_id(){
        $input = "/home/long-lamp-username/MethylDB/mData_output.txt.gz";
        $output = "/home/long-lamp-username/MethylDB/result/result.txt";
        $python_scipt = "/home/long-lamp-username/Mayo_toolbox/prepare_boxplot_data.py";
        $cpg_id = $_POST['cpg_id'];
        $sql = "select CHR,MAPINFO from Probeset where Probeset_ID={$cpg_id}";
        $result = $this->db->query($sql)->result(0);
        $chr = $result->CHR;
        $position = $this->MAPINFO;
        settype($position,"integer");
        $from = $position-1;
        $to = $position+1;
        $cmd = "tabix {$input} {$chr}:{$from}-{$to} -h > {$output}";
        exec($cmd);
        $cmd = "python {$python_scipt} {$output}";
        $datafile = shell_exec($cmd);
        $this->session->set_userdata($datafile);
        $this->session->set_userdata($cpg_id);
    }

}
?>
