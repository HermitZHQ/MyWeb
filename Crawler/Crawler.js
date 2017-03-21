
var g_id;

$(document).ready(function () {

    var timerHandle;

    $("#showInfo").click(function () {
        ShowBtn();
    });

    $("#add").click(function () {
        Add();
    });

    $("#autoStart").click(function () {
        g_id = $("#id").val();
        timerHandle = setInterval("ChangeId()", 200);
    });

    $("#autoStop").click(function () {
        clearInterval(timerHandle);
    });

});

function GenerateID(str) {
    var pad = "000000";
    return pad.substring(0, pad.length - str.length) + str;
}

function ChangeId() {
    var tmp = GenerateID(g_id.toString());
    $("#id").val(tmp);
    g_id++;
    Add();
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
            var json;
            try {
                json = JSON.parse(data);
            } catch (error) {
                // alert(error);
                return;
            }
            if (json.id !== 0) {
                // alert("name is:"+json.name);
                // $("#infoList").append("<li>" + "id:[" + json.id + "] name:" + json.name + " value:" + json.value + "</li>");
                $("#infoList").append("<p class="+"preP"+">id:</p><p class="+"sufP"+">"+json.id+"</p><p class="+"preP"+">name:</p><p class="+"sufP"+">"+json.name+"</p><p class="+"preP"+">value:</p><p class="+"sufP"+">"+json.value+"</p><br>")
            }
        }
    );
}