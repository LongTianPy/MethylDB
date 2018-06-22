var tracks;
$.get(json_file,function (data) {
    // var tracks = [];
    // $.each(data,function (index,value) {
    //     tracks.push(value);
    // })
    // console.log(tracks);
    tracks = data;

    var linearlayout = {
        genomesize: end-start,
        height:250,
        width:1000,
        container: '#linearchart',
        initStart: start,
        initEnd: end,
    };

    var contextLayout = {
        genomesize:end-start,
        container: '#brush'
    };

    var linearTrack = new genomeTrack(linearlayout,tracks);
    var brush = new linearBrush(contextLayout,linearTrack);

    linearTrack.addBrushCallback(brush);
    window.onload = function() {
        if('undefined' !== typeof cTrack) {
            console.log("Hooking up circular plot callback");
            linearTrack.addBrushCallback(cTrack);
        }
    }
    /* Callback to demo resizing the linear plot */
    function resizeLinearPlot() {
        linearTrack.resize(1000);
    }

    /* Catch a click callback on the linear plot and show what
       information we're given about the track item */

    function linearClick(trackName, d) {
        console.log(d);
        var cpg_id = d.name;
        makeplot(cpg_id);
    }


})



/* Callback to demo resizing the linear plot */
function resizeLinearPlot() {
    linearTrack.resize(1000);
}

/* Catch a click callback on the linear plot and show what
   information we're given about the track item */

function linearClick(trackName, d) {
    console.log(d);
    var cpg_id = d.name;
    makeplot(cpg_id);
}

function makeplot(cpg_id){
    var file = "/MethylDB/Result/" + cpg_id + ".txt";
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
