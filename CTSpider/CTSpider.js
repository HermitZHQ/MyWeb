
$(document).ready(function () {
    $("#test").click(function () {
        Test();
    });
});

function Test() {
    $.post("./CTSpider.php",
        {

        },
        function (data, textStatus, jqXHR) {
            alert("return data is:"+data);
        }
    );
}