var startId = 601169;
var stopId = 601999;
var step = 10;
var averageCostTime = 800;
var timerHandle;

$(document).ready(function () {
    $("#evaluate").click(function () {
        Evaluate();
    });

    timerHandle = setInterval('Evaluate()', averageCostTime*step);
});

function Evaluate() {
    var idArr = [];
    for (var i = 0; i < step; i++) {
        idArr.push(startId + i);
    }

    $("#tip").html("processing...");

    $.post("./CTSpider.php",
        {
            // ids:[600090]
            ids:idArr
        },
        function (data, textStatus, jqXHR) {
            // alert(data);
            try {
                
                var json = JSON.parse(data);
                if (null !== json) {
                    if ( 1 === json.status ){
                        // alert("total cost time:"+(json.totalTime).toFixed(2)+" average time:"+(json.averageTime).toFixed(2)+" handleNum:"+json.handleNum);
                        $("#tip").html("succeed handle to id:"+(startId+step-1)+"\ntotal cost time:"+(json.totalTime).toFixed(2)+" average time:"+(json.averageTime).toFixed(2)+" handleNum:"+json.handleNum);
                        $("#id").val(startId+step-1);
                        $("#time").val((json.totalTime).toFixed(2));
                        $("#num").val(json.handleNum);
                        $("#averageTime").val((json.averageTime).toFixed(2));
                        startId = startId+step;
                        if (startId > stopId){
                            $("#tip").html("Handle completed");
                            clearInterval(timerHandle);
                        }
                    }
                }
            } catch (error) {
                alert(data);
                $("#tip").html("failed...");
                return;
            }
        }
    );

    // $.ajax({
    //     type: "POST",
    //     url: "./CTSpider.php",
    //     data:         
    //     {
    //         // ids:[600090]
    //         ids:idArr
    //     },
    //     dataType: "json",
    //     success: function (response) {
    //         alert(response);
    //     },
    //     error: function(XMLHttpRequest, textStatus, errorThrown){
    //         alert(textStatus);
    //     }
    // });
}