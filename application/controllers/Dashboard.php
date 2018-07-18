<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {
    public function index(){
        $place_holder = "<img src='/MethylDB/IMG/placeholding_img.png' id='placeholder_img'>";
        if (isset($_GET['cpg_id'])){
            $data = $this->search_by_id();
            if (isset($data['script'])) {
                $page_data = array(
                    'place_holder' => $place_holder,
                    'script' => $data['script'],
                    'js_parameters'=>$data['js_parameters'],
                );
            }else{
                $page_data = array(
                    'place_holder' => $place_holder,
                    'msg' => "No record found, please modify your search.",
                );
            }
            $this->load->view('dashboardView',$page_data);
        }
        elseif (isset($_GET['from']) and isset($_GET['to'])){
            $data = $this->search_by_region();
            if (!isset($data['no_result'])){
                $page_data = array(
                    'place_holder'=>$place_holder,
                    'igv'=>1,
                    'chr'=>$data['chr'],
                    'start'=>$data['from'],
                    'end'=>$data['to'],
                );
            }else{
                $page_data = array(
                    'place_holder' => $place_holder,
                    'msg' => "No record found, please modify your search.",
                );
            }
            $this->load->view('dashboardView',$page_data);
        }
        elseif (isset($_GET['gene'])){
            $data = $this->search_by_gene();
            if (!isset($data['no_result'])){
                $page_data = array(
                    'place_holder'=>$place_holder,
                    'igv'=>1,
                    'chr'=>$data['chr'],
                    'start'=>$data['from'],
                    'end'=>$data['to'],
                );
            }else{
                $page_data = array(
                    'place_holder' => $place_holder,
                    'msg' => "No record found, please modify your search.",
                );
            }
            $this->load->view('dashboardView',$page_data);
        }
        else{
            $this->load->view('dashboardView');
        }
    }

    public function search_gene($gene){
        $sql = "select * from hg19 where geneName='{$gene}' order by exonCount desc limit 1";
        $result = $this->db->query($sql)->row(0);
        $gene_id = $result->transcript_ID;
        $exonCount = $result->exonCount;
        $txStart = $result->txStart;
        $txEnd = $result->txEnd;
        if ($result->strand == "+"){
            $strand = 1;
        }else{
            $strand = -1;
        }
        $exon_item = array();
        $exonStarts = explode(",",$result->exonStarts);
        $exonEnds = explode(",",$result->exonEnds);
        $gene_item = array(
            'id'=> $gene_id,
            'start'=> $txStart,
            'end' => $txEnd,
            'strand'=>$strand,
        );
        $exon_id = 1;
        for ($i=0;$i<$exonCount;$i++){
            $exon_item[] = array(
                'id'=> $gene_id . "_" . $exon_id,
                'start'=>$exonStarts[$i],
                'end'=>$exonEnds[$i],
                'strand'=>$strand,
            );
            $exon_id ++ ;
        }
        $data = array(
            "gene_item"=>$gene_item,
            "exon_items"=>$exon_item,
        );
        return $data;
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
        strtolower($return_cpg);
        settype($cpg_id,'string');
        strtolower($cpg_id);
        $cpg_id = "'".$cpg_id."'";
        $sql = "select CHR,MAPINFO from Probeset where Probeset_ID={$cpg_id}";
        $result = $this->db->query($sql)->result();
        if (count($result)>0){
            $downloadfile = $return_cpg.".txt";
            exec("cp /data1/MethylDB/CpG/cpg_result/{$return_cpg}.txt /var/www/html/MethylDB/tmp/{$return_cpg}.txt");
            $call_this_script = '<script src="/MethylDB/JS/dashboard.js" type="text/javascript"></script>';
            $js_parameters = "<script>var cpg_id={$cpg_id}</script>";
            $data = array('script'=>$call_this_script,
                'js_parameters'=>$js_parameters,
                'download' => '/MethylDB/tmp/'.$downloadfile,
            );
        }else{
            $data = array('no_result' => 0,);
        }
        return $data;
    }

    public function search_by_region(){
        $input = "/home/long-lamp-username/MethylDB/mData_output.txt.gz";
        $output = "/home/long-lamp-username/MethylDB/result/" . uniqid() . ".txt";
        $python_scipt = "/home/long-lamp-username/Mayo_toolbox/prepare_boxplot_multi.py";
        $chr = $this->input->get('chr_id');
        $start = $this->input->get('from');
        $end = $this->input->get('to');
        $file = shell_exec("python /home/long-lamp-username/Mayo_toolbox/compress_selected_CpG.py {$chr} {$start} {$end}");
        $file = str_replace("\n","",$file);
        $final_result = array(
            'chr' => $chr,
            'from' => $start,
            'to' => $end,
            'download' => '/MethylDB/tmp/' . $file,
        );
        return $final_result;
    }

    public function search_by_gene(){
        $input = "/home/long-lamp-username/MethylDB/mData_output.txt.gz";
        $output = "/home/long-lamp-username/MethylDB/result/" . uniqid() . ".txt";
        $python_scipt = "/home/long-lamp-username/Mayo_toolbox/prepare_boxplot_multi.py";
        $gene = $this->input->get('gene');
        strtoupper($gene);
        $up = $this->input->get('upstream');
        $down = $this->input->get('downstream');
        if ($up==''){
            $up = 0;
        }
        if ($down==''){
            $down = 0;
        }
        $sql = "
        select * from Gene where gene='{$gene}'
        ";
        $result = $this->db->query($sql)->result();
        if (count($result)>0){
            $chr = $result[0]->CHR;
            $start = $result[0]->start;
            $end = $result[0]->end;
            $new_start = $start - $up;
            $new_end = $end + $down;
            $file = shell_exec("python /home/long-lamp-username/Mayo_toolbox/compress_selected_CpG.py {$chr} {$new_start} {$new_end}");
            $file = str_replace("\n","",$file);
            $data = array(
                'chr' => $chr,
                'from' => $new_start,
                'to' => $new_end,
                'download' => '/MethylDB/tmp/'. $file,
            );}
        else {
            $data = array('no_result'=>0);
        }
        return $data;
    }

}
?>
<!DOCTYPE html>