function makeplot(cpg_id){
    var file = "/MethylDB/Result/cpg_result" + cpg_id + ".txt";
    Plotly.d3.csv(file,function(data){processData(data,cpg_id)});
};
function processData(allRows,cpg_id){
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
function makePlotly(acronym_tumor,value_tumor,acronym_normal,value_normal,cpg_id){
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
    document.getElementById("placeholder_img").style.display = "none";
    Plotly.react('myChart',data,layout);
}

$(function () {
    $(document).ready(function () {
        popoverDetail = {
            content: function () {
                var detail_id = this.id;
                return $('div#detail_' + detail_id).html();
            },
            title: 'Quick look of this CpG island',
            trigger: 'hover',
            animation: false,
            placement: 'bottom',
            html: true,
            delay: {"show": 500, "hide": 100}
        };
        $('.popdetail').popover(popoverDetail);
    });
});
