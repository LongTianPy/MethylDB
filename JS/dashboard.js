// function makeplot(cpg_id){
//     var file = "/MethylDB/Result/cpg_result/" + cpg_id + ".txt";
//     Plotly.d3.csv(file,function(data){processData(data)});
// };
// function processData(allRows){
//     console.log(allRows);
//     var acronym_tumor=[], acronym_normal=[],value_tumor=[],value_normal=[], cpg_id='';
//     for (var i=0; i<allRows.length;i++){
//         row=allRows[i];
//         if (row["TumorNormal"]=='Tumor'){
//             acronym_tumor.push(row["Acronym"]);
//             value_tumor.push(row["Value"]);
//         }
//         else {
//             acronym_normal.push(row["Acronym"]);
//             value_normal.push(row["Value"]);
//         }
//
//     }
//     console.log("Acronym_tumor",acronym_tumor,"Acronym_normal",acronym_normal,"Value_tumor",value_tumor,"Value_normal",value_normal);
//     makePlotly(acronym_tumor,value_tumor,acronym_normal,value_normal);
// }
// function makePlotly(acronym_tumor,value_tumor,acronym_normal,value_normal){
//     var plotDiv = document.getElementById("myChart");
//     var trace1={
//         y: value_tumor,
//         x: acronym_tumor,
//         name: 'Tumor',
//         marker: {color: '#FF4136'},
//         type: 'box'
//     };
//     var trace2 = {
//         y: value_normal,
//         x: acronym_normal,
//         name: "Normal",
//         marker: {color: '#3D9970'},
//         type: 'box'
//     };
//     var data = [trace1,trace2];
//     var layout = {
//         title: document.getElementById('cpg_id').innerHTML,
//         yaxis: {
//             title: 'DNA Methylation level',
//             zeroline: false
//         },
//         xaxis: {
//             tickangle: 270
//         },
//         boxmode: 'group'
//     }
//     document.getElementById("placeholder_img").style.display = "none";
//     Plotly.react('myChart',data,layout);
// }
$(document).ready(function(){
    var loadFile = function(filePath) {
        var result = null;
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.open("GET", filePath, false);
        xmlhttp.send();
        if (xmlhttp.status==200 || xmlhttp.status==0) {
            result = xmlhttp.responseText;
        }
        return result;
    }
    var makeplot = function(cpg_id){
        var file = "/MethylDB/Result/cpg_result/" + cpg_id + ".txt";
        Plotly.d3.csv(file,function(data){processData(data)});
    };
    var processData = function(allRows) {
        console.log(allRows);
        var acronym_tumor = [], acronym_normal = [], value_tumor = [], value_normal = [], cpg_id = '';
        for (var i = 0; i < allRows.length; i++) {
            row = allRows[i];
            if (row["Value"] != 'nan'){
                if (row["TumorNormal"] == 'Tumor') {
                    acronym_tumor.push(row["Acronym"]);
                    value_tumor.push(parseFloat(row["Value"]));
                }
                else {
                    acronym_normal.push(row["Acronym"]);
                    value_normal.push(parseFloat(row["Value"]));
                }
            }
        }
        console.log("Acronym_tumor", acronym_tumor, "Acronym_normal", acronym_normal, "Value_tumor", value_tumor, "Value_normal", value_normal);
        makePlotly(acronym_tumor, value_tumor, acronym_normal, value_normal);
    };
    var makePlotly = function(acronym_tumor,value_tumor,acronym_normal,value_normal) {
        var plotDiv = document.getElementById("myChart");
        var trace1 = {
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
        var data = [trace1, trace2];
        var layout = {
            title: document.getElementById('cpg_id').innerHTML,
            yaxis: {
                title: 'DNA Methylation level',
                zeroline: false
            },
            xaxis: {
                tickangle: 270
            },
            boxmode: 'group'
        }
        document.getElementById("placeholder_img").style.display = "none";
        Plotly.react('myChart', data, layout);
        // var mean = ss.mean(value_tumor);
        // console.log(mean);
        // // var tscore=jStat.tscore(mean,value_tumor);
        // // console.log(tscore);
        // var pvalue = jStat.ttest(mean,value_normal,2);
        // console.log(pvalue);
        // $('#stats_output').html('<table><tr><td>T-test between normal and tumor samples: </td><td></td><td>p-value = ' + pvalue +'</td></tr></table>');
        var tablefile = loadFile("/MethylDB/Result/pvalues_table_python3/" + cpg_id + ".html")
        $('#stats_output').html('<ul><li>Differential methylation between tumor and normal samples</li></ul>');
        $('#stats_output').append(tablefile);
    }
    var file=cpg_id;
    makeplot(file);
    var popoverDetail = {
        content: "Please right-click and use 'Save link as' to save the file to a selected folder.",
        trigger: 'hover',
        animation: false,
        placement: 'left',
        html: true,
        delay: {"show":500,"hide":100},
    };
    $('#export_button').popover(popoverDetail);
    
});

