
$(document).ready(function () {
    $("#test").click(function () {
        Test();
    });
});

function Test() {
    $.post("./CTSpider.php",
        {},
        function (data, textStatus, jqXHR) {
            alert(data);

            // var dataTmp = data.substring(data.indexOf("[")+1, data.lastIndexOf("]"));
            // dataTmp = dataTmp.substring(dataTmp.indexOf("[")+1, dataTmp.lastIndexOf("]"));
            // alert(dataTmp);
            // var infoArr = dataTmp.split(",");
            // alert(infoArr[0]);
        }
    );
}