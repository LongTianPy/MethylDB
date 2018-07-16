// var div = document.getElementById('igvDiv');
// var options = {
//     reference: {
//         id: "hg19",
//         fastaURL: "https://s3.amazonaws.com/igv.broadinstitute.org/genomes/seq/1kg_v37/human_g1k_v37_decoy.fasta",
//         cytobandURL: "https://s3.amazonaws.com/igv.broadinstitute.org/genomes/seq/b37/b37_cytoband.txt"
//     },
//     locus: chromosome.toString() + ":" + start.toString() + "-" + end.toString(),
//     tracks: [
//         {
//             name: "Genes",
//             type: "annotation",
//             format: "bed",
//             sourceType: "file",
//             url: "https://s3.amazonaws.com/igv.broadinstitute.org/annotations/hg19/genes/refGene.hg19.bed.gz",
//             indexURL: "https://s3.amazonaws.com/igv.broadinstitute.org/annotations/hg19/genes/refGene.hg19.bed.gz.tbi",
//             order: Number.MAX_VALUE,
//             visibilityWindow: 300000000,
//             displayMode: "EXPANDED"
//         },
//
//         {
//             name: "DNA Methylation CpG sites",
//             format: "bed",
//             url: "/MethylDB/Data/modified_cpg.txt.gz",
//             indexURL:  "/MethylDB/Data/modified_cpg.txt.gz.tbi",
//         }
//     ]
//
// };
// var browser = igv.createBrowser(div, options);

$(document).ready(function () {
    var processData = function(allRows,cpg_id){
        console.log(allRows);
        var acronym_tumor=[], acronym_normal=[],value_tumor=[],value_normal=[];
        for (var i=0; i<allRows.length;i++){
            row=allRows[i];
            if (row["TumorNormal"]=='Tumor'){
                acronym_tumor.push(row["Acronym"]);
                value_tumor.push(row["Value"]);
            }
            else {
                acronym_normal.push(row["Acronym"]);
                value_normal.push(row["Value"]);
            }

        }
        console.log("Acronym_tumor",acronym_tumor,"Acronym_normal",acronym_normal,"Value_tumor",value_tumor,"Value_normal",value_normal,'cpg_id',cpg_id);
        makePlotly(acronym_tumor,value_tumor,acronym_normal,value_normal,cpg_id);
    }
    var makePlotly = function(acronym_tumor,value_tumor,acronym_normal,value_normal,cpg_id){
        var plotDiv = document.getElementById("myChart");
        var trace1={
            y: value_tumor,
            x: acronym_tumor,
            name: 'Tumor',
            marker: {color: '#FF4136'},
            type: 'box'
        };
        var trace2 = {
            y: value_normal,
            x: acronym_normal,
            name: "Normal",
            marker: {color: '#3D9970'},
            type: 'box'
        };
        var data = [trace1,trace2];
        var layout = {
            title: cpg_id,
            yaxis: {
                title: 'DNA Methylation level',
                zeroline: false
            },
            xaxis: {
                tickangle: 270
            },
            boxmode: 'group'
        }
        // document.getElementById("placeholder_img").style.display = "none";
        Plotly.react('myChart',data,layout);
    }

    var div = document.getElementById('igvDiv');
    var options = {
        reference: {
            id: "hg19",
            fastaURL: "https://s3.amazonaws.com/igv.broadinstitute.org/genomes/seq/1kg_v37/human_g1k_v37_decoy.fasta",
            cytobandURL: "https://s3.amazonaws.com/igv.broadinstitute.org/genomes/seq/b37/b37_cytoband.txt"
        },
        locus: chromosome.toString() + ":" + start.toString() + "-" + end.toString(),
        tracks: [
            {
                name: "Genes",
                type: "annotation",
                format: "bed",
                sourceType: "file",
                url: "https://s3.amazonaws.com/igv.broadinstitute.org/annotations/hg19/genes/refGene.hg19.bed.gz",
                indexURL: "https://s3.amazonaws.com/igv.broadinstitute.org/annotations/hg19/genes/refGene.hg19.bed.gz.tbi",
                order: Number.MAX_VALUE,
                visibilityWindow: 300000000,
                displayMode: "EXPANDED"
            },

            {
                name: "DNA Methylation CpG sites",
                format: "bed",
                url: "/MethylDB/Data/modified_cpg.txt.gz",
                indexURL:  "/MethylDB/Data/modified_cpg.txt.gz.tbi",
            }
        ]

    };

    var FileExists = function (filepath) {
        var http = new XMLHttpRequest();
        http.open('HEAD', filepath, false);
        http.send();
        return http.status!=404;
    }

    var browser = igv.createBrowser(div, options);
    browser.on('trackclick',function (track,popoverData) {
        $("#myChart").html('');
        if (track.name=="DNA Methylation CpG sites") {
            popoverData.forEach(function (nameValue) {
                if (nameValue.name){
                    if (nameValue.name == "Name"){
                        // var shell = require('shelljs');
                        // shell.exec('echo testing_node_js_shelljs > /home/long-lamp-username/Downloads/test.txt')
                        // shell.exec('python /home/long-lamp-username/test.py')
                        var cpg_id = nameValue.value;
                        var file = "/MethylDB/Result/cpg_result/" + cpg_id + ".txt";
                        if (FileExists(file)){
                            Plotly.d3.csv(file,function(data){processData(data,cpg_id)});
                        } else {
                            $("#myChart").html("<div class='d-flex justify-content-center'><h2>No Data Found For This CpG Site</h2></div>");
                        }
                    }
                }

            })
        }
    })
});
