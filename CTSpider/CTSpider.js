var step = 10;
var averageCostTime = 650;
var timerHandle;

var idJsonInfo = {
    num:6,
    index:0,
    arr:
    [
        {startId:0, stopId:999, bFinish:false},
        {startId:2000, stopId:2999, bFinish:false},
        {startId:300000, stopId:300999, bFinish:false},
        {startId:600000, stopId:600999, bFinish:false},
        {startId:601000, stopId:601999, bFinish:false},
        {startId:603000, stopId:603999, bFinish:false},
    ]
};

$(document).ready(function () {
    $("#evaluate").click(function () {
        Evaluate();
    });

    timerHandle = setInterval('Evaluate()', averageCostTime*step);
});

function Evaluate() {
    var idArr = [];
    for (var i = 0; i < step; i++) {
        idArr.push(idJsonInfo.arr[idJsonInfo.index].startId + i);
    }

    $("#tip").html("processing...");

    $.post("./CTSpider.php",
        {
            // ids:[26]
            ids:idArr
        },
        function (data, textStatus, jqXHR) {
            // alert(data);
            try {
                var json = JSON.parse(data);
                if (null !== json) {
                    if ( 1 === json.status ){
                        // alert("total cost time:"+(json.totalTime).toFixed(2)+" average time:"+(json.averageTime).toFixed(2)+" handleNum:"+json.handleNum);
                        $("#tip").html("succeed handle to id:"+(idJsonInfo.arr[idJsonInfo.index].startId+step-1)+"\ntotal cost time:"+(json.totalTime).toFixed(2)+" average time:"+(json.averageTime).toFixed(2)+" handleNum:"+json.handleNum);
                        $("#id").val(idJsonInfo.arr[idJsonInfo.index].startId+step-1);
                        $("#time").val((json.totalTime).toFixed(2));
                        $("#num").val(json.handleNum);
                        $("#averageTime").val((json.averageTime).toFixed(2));
                        idJsonInfo.arr[idJsonInfo.index].startId = idJsonInfo.arr[idJsonInfo.index].startId+step;
                        if (idJsonInfo.arr[idJsonInfo.index].startId > idJsonInfo.arr[idJsonInfo.index].stopId){
                            // $("#tip").html("Handle completed");
                            idJsonInfo.arr[idJsonInfo.index].bFinish = true;
                            idJsonInfo.index++;
                            if (idJsonInfo.index >= idJsonInfo.num){
                                clearInterval(timerHandle);
                                alert("Handle completed");
                            }
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