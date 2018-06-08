$(function () {
    $(document).ready(function() {
        $('#form_search_by_id').submit(function (event) {
            var cgp_id = $('#cpg_id').val();
            $.ajax({
                url: 'index.php/Dashboard',
                type: 'post',
                data:{cgp_id:'cgp_id'},
                success: function(datafile,cpg_id){
                    document.writeln("something;s going on.");
                    function makeplot(){
                        Plot.d3.csv(datafile,function(data){processData(data)});
                    };
                    function processData(allRows){
                        console.log(allRows);
                        var acronym_tumor=[], acrynom_normal=[],value_tumor=[],value_normal=[], cpg_id='';
                        for (var i=0; i<allRows.length;i++){
                            row=allRows[i];
                            if (row["TumorNormal"]=='Tumor'){
                                acronym_tumor.push(row["Acronym"]);
                                value_tumor.push(row["Value"]);
                            }
                            else {
                                acrynom_normal.push(row["Acronym"]);
                                value_normal.push(row["Value"]);
                            }

                        }
                        console.log("Acronym_tumor",acronym_tumor,"Acronym_normal",acrynom_normal,"Value_tumor",value_tumor,"Value_normal",value_normal);
                        makePlotly(acronym_tumor,value_tumor,acrynom_normal,value_normal);
                    }
                    function makePlotly(acronym_tumor,value_tumor,acrynom_normal,value_normal){
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
                            x: acrynom_normal,
                            name: "Normal",
                            marker: {color: '#3D9970'},
                            type: 'box'
                        };
                        var data = [trace1,trace2];
                        var layout = {
                            yaxis: {
                                title: 'DNA Methylation level',
                                zeroline: false
                            },
                            boxmode: 'group'
                        }
                        Plotly.newPlot('myChart',data,layout);
                    }
                    makeplot();
                }
            })
        })
    });
})