
var g_id = 600000;

$(document).ready(function () {

    $("#showInfo").click(function () {
        ShowBtn();
    });

    $("#add").click(function () {
        Add();
    });

    setInterval("ChangeId()", 200);
});

function ChangeId()
{
    $("#id").val(g_id);
    g_id++;
    Add();
}

function ShowBtn() {
    $.post("./Crawler.php",
        {
            ids: ['600694', '600012'],
        },
        function (data, textStatus, jqXHR) {
            alert("data is: " + data);
        }
    );
}

function Add() {
    if ($("#id").val() <= 0 || $("#id").val().length < 6) {
        alert("id format is not valid");
        return;
    }

    $.post("./Crawler.php",
        {
            ids: $("#id").val(),
        },
        function (data, textStatus, jqXHR) {
            //alert("data is: " + data);
            var json = JSON.parse(data);
            if( json.id !== 0 )
            {
                // alert("name is:"+json.name);
                $("#infoList").append("<li>" + "id:[" + json.id + "] name:" + json.name + " value:" + json.value + "</li>");
            }
        }
    );
}