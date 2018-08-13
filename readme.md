# MethylDB

[MethylDB](https://methyldb.centralus.cloudapp.azure.com/MethylDB/index.php) is hosted on a Microsoft Azure VM managed by Mayo Clinic. It is developed as an internship project by [@Long Tian](https://github.com/LongTianPy), supervised by Dr. Zhifu Sun started from May 2018.
It was initially designed as an interface to visualize the DNA methylation data from TCGA. At the time of writing this document, MethylDB is being developed as a platform to be able to host different types of data.

## Dependencies
MethylDB is built in LAMP stack: **L**inux OS Ubuntu 17.10 + **A**pache2 + **M**ySQL + **P**HP.
[Bootstrap 4](https://getbootstrap.com/) adds style to web pages. Javascript libraries, such as [Plotly](https://plot.ly/javascript/), [igv.js](https://github.com/igvteam/igv.js), [Simple-statistics](https://github.com/simple-statistics/simple-statistics), and [JStat](https://github.com/jstat/jstat) are used for data visualization and data analysis.
Some python scripts are used for the installation of extra dataset, and this part will be introduced in detail in following sections.

## Structure and functions of MethylDB
### Database
Data are stored in both MySQL and files on disk.
MySQL hosts gene and CpG site info, can be visited by:  
```
mysql -u methyldb -pmayoproject MetylDB
```  
Then the tables in the database:  
```
mysql> show tables;
+--------------------+
| Tables_in_MethylDB |
+--------------------+
| Gene               |
| Patient            |
| Probeset           |
| hg19               |
+--------------------+
```
Table `Gene` stores the gene metadata that a query needs  
```
mysql> describe Gene;
+---------+--------------+------+-----+---------+----------------+
| Field   | Type         | Null | Key | Default | Extra          |
+---------+--------------+------+-----+---------+----------------+
| Gene_ID | int(11)      | NO   | PRI | NULL    | auto_increment |
| CHR     | varchar(10)  | YES  |     | NULL    |                |
| start   | int(11)      | YES  |     | NULL    |                |
| end     | int(11)      | YES  |     | NULL    |                |
| gene    | varchar(100) | YES  |     | NULL    |                |
+---------+--------------+------+-----+---------+----------------+
```
Table `Patient` stores sample info ~50 items, most of them are for future purpose  
```
mysql> describe Patient;
+--------------------------------------------+-------------+------+-----+---------+-------+
| Field                                      | Type        | Null | Key | Default | Extra |
+--------------------------------------------+-------------+------+-----+---------+-------+
| Sample_ID                                  | varchar(30) | NO   | PRI | NULL    |       |
| patient_barcode                            | text        | YES  |     | NULL    |       |
| tissue                                     | text        | YES  |     | NULL    |       |
| others                                     | text        | YES  |     | NULL    |       |
| bcr_patient_uuid                           | text        | YES  |     | NULL    |       |
| acronym                                    | text        | YES  |     | NULL    |       |
| gender                                     | text        | YES  |     | NULL    |       |
| vital_status                               | text        | YES  |     | NULL    |       |
| days_to_birth                              | text        | YES  |     | NULL    |       |
| days_to_death                              | text        | YES  |     | NULL    |       |
| days_to_last_followup                      | text        | YES  |     | NULL    |       |
| days_to_initial_pathologic_diagnosis       | text        | YES  |     | NULL    |       |
| age_at_initial_pathologic_diagnosis        | text        | YES  |     | NULL    |       |
| icd_10                                     | text        | YES  |     | NULL    |       |
| tissue_retrospective_collection_indicator  | text        | YES  |     | NULL    |       |
| icd_o_3_histology                          | text        | YES  |     | NULL    |       |
| tissue_prospective_collection_indicator    | text        | YES  |     | NULL    |       |
| history_of_neoadjuvant_treatment           | text        | YES  |     | NULL    |       |
| icd_o_3_site                               | text        | YES  |     | NULL    |       |
| tumor_tissue_site                          | text        | YES  |     | NULL    |       |
| new_tumor_event_after_initial_treatment    | text        | YES  |     | NULL    |       |
| radiation_therapy                          | text        | YES  |     | NULL    |       |
| race                                       | text        | YES  |     | NULL    |       |
| prior_dx                                   | text        | YES  |     | NULL    |       |
| ethnicity                                  | text        | YES  |     | NULL    |       |
| informed_consent_verified                  | text        | YES  |     | NULL    |       |
| person_neoplasm_cancer_status              | text        | YES  |     | NULL    |       |
| patient_id                                 | text        | YES  |     | NULL    |       |
| year_of_initial_pathologic_diagnosis       | text        | YES  |     | NULL    |       |
| histological_type                          | text        | YES  |     | NULL    |       |
| tissue_source_site                         | text        | YES  |     | NULL    |       |
| form_completion_date                       | text        | YES  |     | NULL    |       |
| pathologic_T                               | text        | YES  |     | NULL    |       |
| pathologic_M                               | text        | YES  |     | NULL    |       |
| pathologic_N                               | text        | YES  |     | NULL    |       |
| system_version                             | text        | YES  |     | NULL    |       |
| pathologic_stage                           | text        | YES  |     | NULL    |       |
| clinical_stage                             | text        | YES  |     | NULL    |       |
| postoperative_rx_tx                        | text        | YES  |     | NULL    |       |
| primary_therapy_outcome_success            | text        | YES  |     | NULL    |       |
| lymph_node_examined_count                  | text        | YES  |     | NULL    |       |
| primary_lymph_node_presentation_assessment | text        | YES  |     | NULL    |       |
| initial_pathologic_diagnosis_method        | text        | YES  |     | NULL    |       |
| number_of_lymphnodes_positive_by_he        | text        | YES  |     | NULL    |       |
| anatomic_neoplasm_subdivision              | text        | YES  |     | NULL    |       |
| residual_tumor                             | text        | YES  |     | NULL    |       |
| neoplasm_histologic_grade                  | text        | YES  |     | NULL    |       |
| height                                     | text        | YES  |     | NULL    |       |
| weight                                     | text        | YES  |     | NULL    |       |
| tobacco_smoking_history                    | text        | YES  |     | NULL    |       |
| laterality                                 | text        | YES  |     | NULL    |       |
| TumorNormal                                | text        | YES  |     | NULL    |       |
+--------------------------------------------+-------------+------+-----+---------+-------+
```
And the `Probeset` table stores metadata info associated to CpG sites
```
mysql> describe Probeset;
+-----------------------------+-------------+------+-----+---------+-------+
| Field                       | Type        | Null | Key | Default | Extra |
+-----------------------------+-------------+------+-----+---------+-------+
| Probeset_ID                 | varchar(20) | NO   | PRI | NULL    |       |
| Infinium_Design_Type        | tinytext    | YES  |     | NULL    |       |
| Genome_Build                | tinytext    | YES  |     | NULL    |       |
| CHR                         | tinytext    | YES  |     | NULL    |       |
| MAPINFO                     | int(11)     | YES  |     | NULL    |       |
| Chromosome_36               | tinytext    | YES  |     | NULL    |       |
| Coordinate_36               | tinytext    | YES  |     | NULL    |       |
| Strand                      | tinytext    | YES  |     | NULL    |       |
| Probe_SNPs                  | tinytext    | YES  |     | NULL    |       |
| Methyl27_Loci               | tinytext    | YES  |     | NULL    |       |
| UCSC_RefGene_Name           | tinytext    | YES  |     | NULL    |       |
| UCSC_RefGene_Accession      | tinytext    | YES  |     | NULL    |       |
| UCSC_RefGene_Group          | tinytext    | YES  |     | NULL    |       |
| UCSC_CpG_Islands_Name       | tinytext    | YES  |     | NULL    |       |
| Relation_to_UCSC_CpG_Island | tinytext    | YES  |     | NULL    |       |
| DMR                         | tinytext    | YES  |     | NULL    |       |
| Enhancer                    | tinytext    | YES  |     | NULL    |       |
| HMM_Island                  | tinytext    | YES  |     | NULL    |       |
| Regulatory_Feature_Name     | tinytext    | YES  |     | NULL    |       |
| Regulatory_Feature_Group    | tinytext    | YES  |     | NULL    |       |
+-----------------------------+-------------+------+-----+---------+-------+
```
Table `hg19` stores the full info of the genome we are using.

So, as I said in the slides, the query will first go through the database for the actual location and then can be passed to the igv browser or directly get the CpG site.  

The visualization of methylation data relies on the physical data on the disk that have been re-organized for the visualization purpose.  
Currently, `/data1/` has 500GB space (75GB taken by MethylDB). MethylDB related files are all in `/data1/MethylDB/`. CpG data for visualization are stored in `/data1/MethylDB/CpG/cpg_result`. Each file is comma (`,`) delimited with header of `Acronym,TumorNormal,Value`.
T-test results are stored in `/data1/MethylDB/CpG/pvalues_table_python3`. Files are HTML `<table>` components.
### Website
MethylDB is built in a MVC framework called [CodeIgniter](https://www.codeigniter.com/). Note that before getting started, it's better to read the official document of CodeIgniter.  
The website root is `/var/www/html/MethylDB`. `CSS`, `JS`, `IMG`'s usage are straightforward by their names.  
`application/controllers` and `application/views` are the two most important folders here. Controller scripts usually calculate and process to get the content to be printed on the final page and view scripts define how the content are displayed. 
