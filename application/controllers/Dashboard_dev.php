<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard_dev extends CI_Controller {
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
            $this->load->view('dashboardView_dev',$page_data);
        }
        elseif (isset($_GET['from']) and isset($_GET['to'])){
            $data = $this->search_by_region();
            if (!isset($data['no_result'])){
                $page_data = array(
                    'place_holder'=>$place_holder,
                    'igv'=>1,
                    'chr'=>$data['chr'],
                    'start'=>$data['start'],
                    'end'=>$data['end'],
                );
            }else{
                $page_data = array(
                    'place_holder' => $place_holder,
                    'msg' => "No record found, please modify your search.",
                );
            }
            $this->load->view('dashboardView_dev',$page_data);
        }
        elseif (isset($_GET['gene'])){
            $data = $this->search_by_gene();
            if (!isset($data['no_result'])){
                $page_data = array(
                    'place_holder'=>$place_holder,
                    'igv'=>1,
                    'chr'=>$data['chr'],
                    'start'=>$data['start'],
                    'end'=>$data['end'],
                );
            }else{
                $page_data = array(
                    'place_holder' => $place_holder,
                    'msg' => "No record found, please modify your search.",
                );
            }
            $this->load->view('dashboardView_dev',$page_data);
        }
        else{
            $this->load->view('dashboardView_dev');
        }
    }

    public function search_gene($gene){
        $sql = "select * from hg19 where geneName='{$gene}' order by exonCount desc limit 1";
        $result = $this->db->query($sql);
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
        settype($cpg_id,'string');
        $cpg_id = "'".$cpg_id."'";
        $sql = "select CHR,MAPINFO from Probeset where Probeset_ID={$cpg_id}";
        $result = $this->db->query($sql)->result();
        if (count($result)>0){
            $result = $result[0];
            $chr = $result->CHR;
            $position = $result->MAPINFO;
            $from = $position-1;
            $to = $position+1;
            $cmd = "tabix {$input} {$chr}:{$from}-{$to} -h > {$output}";
            exec($cmd);
            $row_nums = count(file($output));
            settype($row_nums, 'integer');
            if ($row_nums > 1){
                $cmd = "python {$python_scipt} {$output}";
                $datafile = shell_exec($cmd);
                $datafile = str_replace("\n","",$datafile);
                $call_this_script = '<script src="/MethylDB/JS/dashboard.js" type="text/javascript"></script>';
                $js_parameters = "<script>var datafile='{$datafile}'; var cpg_id='{$return_cpg}'</script>";
                $data = array('script'=>$call_this_script,
                    'js_parameters'=>$js_parameters,
                    );
            }else{
                $data = array('no_result' => 0,);
            }
        } else{
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
            $sql = "select * from Gene where CHR='{$chr}' and start<={$start} and end>={$end}";
            $result = $this->db->query($sql)->result();
            $genes = [];
            $gene_items = [];
            $exon_items = [];
            foreach ($result as $row){
                $genes[] = $row->gene;
                $items = $this->search_gene($row->gene);
                $gene_items[] = $items->gene_item;
                $exon_items[] = $items->exon_items;
            }
            $cpg_items = [];
            $cpg_iter_id = 1;
            foreach ($cpg_ids as $cpg_id){
                $sql = "select * from Probeset where Probeset_ID='{$cpg_id}'";
                $cpg_result = $this->db->query($sql)->row(0);
                $locus = $cpg_result->MAPINFO;
                $cpg_items[] = array(
                    'id' => $cpg_iter_id,
                    'bp' => $locus,
                    'name' => $cpg_id,
                );
                $cpg_iter_id ++;
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
                'linear_mouseclick'=>'linearPopup',
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
                'linear_mouseclick'=>'linearPopup',
            );
            $cpgs = array(
                'trackName'=>'gapTrack',
                'trackType'=>'gap',
                'inner_radius'=>25,
                'outer_radius'=>235,
                'showLabels'=>true,
                'showTooltip'>true,
                'items'=>$cpg_items,
            );
            $tracks = array($genes,$exons,$cpgs);
            $final_result = array(
                'chr' => $chr,
                'from' => $start,
                'to' => $end,
                'cpg_ids' => $cpg_ids_string,
                'script' => $call_this_script,
                'tracks' => $tracks,
            );
        }else{
            $final_result = array(
                'no_result' => 0,
            );
        }
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
        $sql = "
        select * from Gene where gene='{$gene}'
        ";
        $result = $this->db->query($sql)->result();
        if (count($result)>0){
            $chr = $result->CHR;
            $start = $result->start;
            $end = $result->end;
            $new_start = $start - $up;
            $new_end = $end + $down;
//            $sql = "select * from Gene where end>={$new_start} and start<={$new_end}";
//            $result = $this->db->query($sql)->result();
            $cmd = "tabix {$input} {$chr}:{$new_start}-{$new_end} -h > {$output}";
            exec($cmd);
            $row_nums = count(file($output));
            settype($row_nums, 'integer');
            if ($row_nums>1){
                $cmd = "python {$python_scipt} {$output}";
                $returned = shell_exec($cmd);
                $returned = explode(",",$returned);
                $cpg_ids = array_slice($returned,0,-1);
                $cpg_ids_string = implode(",",$cpg_ids);
                $datafile = end($returned);
                $call_this_script = '<script src="/MethylDB/JS/dashboard_gene.js" type="text/javascript"></script>';
                $items = $this->search_gene($gene);
                $gene_items = $items['gene_item'];
                $exon_items = $items['exon_items'];
                $cpg_items = [];
                $cpg_iter_id = 1;
                foreach ($cpg_ids as $cpg_id){
                    $sql = "select * from Probeset where Probeset_ID='{$cpg_id}'";
                    $cpg_result = $this->db->query($sql)->row(0);
                    $locus = $cpg_result->MAPINFO;
                    $cpg_items[] = array(
                        'id' => $cpg_iter_id,
                        'bp' => $locus,
                        'name' => $cpg_id,
                    );
                    $cpg_iter_id ++;
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
                    'linear_mouseclick'=>'linearPopup',
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
                    'linear_mouseclick'=>'linearPopup',
                );
                $cpgs = array(
                    'trackName'=>'gapTrack',
                    'trackType'=>'gap',
                    'inner_radius'=>25,
                    'outer_radius'=>235,
                    'showLabels'=>true,
                    'showTooltip'>true,
                    'items'=>$cpg_items,
                );
                $tracks = array($genes,$exons,$cpgs);
                $data = array(
                    'chr' => $chr,
                    'from' => $start,
                    'to' => $end,
                    'cpg_ids' => $cpg_ids_string,
                    'script' => $call_this_script,
                    'tracks' => $tracks,
                );
            }
            else {
                $data = array('no_result'=>0);
            }
        }
        else {
            $data = array('no_result'=>0);
        }
        return $data;
    }


}



?>
<!DOCTYPE html>