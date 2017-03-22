var g_id;
var g_idMap;

$(document).ready(function () {

    var autoCrawlerTimerHandle;
    g_idMap = new HashMap();

    $("#record").click(function () {
        RecordToDB();
    });

    $("#load").click(function () {
        LoadFromDB();
    });

    $("#add").click(function () {
        AddOneInfo();
    });

    $("#autoStart").click(function () {
        g_id = $("#id").val();
        autoCrawlerTimerHandle = setInterval('ChangeId()', 200);
    });

    $("#autoStop").click(function () {
        clearInterval(autoCrawlerTimerHandle);
    });

    $('#id').bind('keypress', function (event) {
        if (event.keyCode == "13") {
            AddOneInfo();
        }
    });

    setInterval('UpdateInfo()', 4500);
});

function LoadFromDB() {
    $.post("./LoadFromDB.php",
        {

        },
        function (data, textStatus, jqXHR) {
            // alert(data);
            var jsonArr = JSON.parse(data);
            if (jsonArr.length > 0) {
                for (var i = 0; i < jsonArr.length; i++) {
                    var json = JSON.parse(jsonArr[i]);
                    //comment id:cmt+id
                    //value id:id
                    //add li to dom
                    alert("<li><p class=" + "preP" + ">id:</p><p id=\"" + "cmt" + json.id + "\" data-simpletooltip=init title=\"" + json.comment + "\" class=" + "sufP" + ">" + json.id + "</p><p class=" + "preP" + ">name:</p><p class=" + "sufP" + ">" + json.name + "</p><p class=" + "preP" + ">value:</p><p id=" + json.id + " class=" + "sufP" + ">" + json.value + "</p></li><br>");
                    $("#infoList").append("<li><p class=" + "preP" + ">id:</p><p id=\"" + "cmt" + json.id + "\" data-simpletooltip=init title=\"" + json.comment + "\" class=" + "sufP" + ">" + json.id + "</p><p class=" + "preP" + ">name:</p><p class=" + "sufP" + ">" + json.name + "</p><p class=" + "preP" + ">value:</p><p id=" + json.id + " class=" + "sufP" + ">" + json.value + "</p></li><br>");

                    //update HashMap with json info, we will use this info to record to db
                    var json2 = {
                        id: json.id,
                        name: json.name,
                        value: json.value,
                        tvalue: json.tvalue,
                        comment: json.comment
                    };
                    g_idMap.put(json.id, json2);
                }
            }
        }
    );
}

function RecordToDB() {
    var s = g_idMap.size();
    if (s === 0) {
        return;
    }

    var idArr = [];
    var infoArr = [];

    idArr = g_idMap.keySet();
    infoArr = g_idMap.values();

    $.post("./SaveToDB.php", {
        ids: idArr,
        infos: infoArr
    },
        function (data, textStatus, jqXHR) {
            alert(data);
        }
    );
}

function UpdateInfo() {
    var mapSize = g_idMap.size();
    if (0 === mapSize)
        return;

    var idArr = g_idMap.keySet();

    $.post("./Crawler.php", {
        ids: idArr
    },
        function (data, textStatus, jqXHR) {
            //alert(data);

            var infoArr = data.split(";");
            if (infoArr.length === 0) {
                return;
            }

            //semicolon will separate the data to two, the second part is "\n", we should ignore it
            for (var i = 0; i < infoArr.length - 1; i++) {
                var dataArr = infoArr[i].split("~");
                if (dataArr.length === 0) {
                    continue;
                }

                $("#" + dataArr[2]).html(dataArr[3]);
            }
        }
    );
}

function GenerateID(str) {
    var pad = "000000";
    return pad.substring(0, pad.length - str.length) + str;
}

function ChangeId() {
    var tmp = GenerateID(g_id.toString());
    $("#id").val(tmp);
    g_id++;
    AddOneInfo();
}

function AddOneInfo() {
    if ($("#id").val() <= 0 || $("#id").val().length < 6) {
        alert("id format is not valid");
        return;
    }

    //don't add repeat id
    if (g_idMap.containsKey($("#id").val())) {
        return;
    }

    //add id to map
    g_idMap.put($("#id").val(), 0);

    $.post("./Crawler.php", {
        ids: [$("#id").val()],
    },
        function (data, textStatus, jqXHR) {
            //alert("data is: " + data);

            var infoArr = data.split(";");
            if (infoArr.length === 0) {
                return;
            }

            //semicolon will separate the data to two, the second part is "\n", we should ignore it
            for (var i = 0; i < infoArr.length - 1; i++) {
                var dataArr = infoArr[i].split("~");
                if (dataArr.length === 0) {
                    continue;
                }
                // alert($("#comment").val());

                //comment id:cmt+id
                //value id:id
                //add li to dom
                $("#infoList").append("<li><p class=" + "preP" + ">id:</p><p id=\"" + "cmt" + dataArr[2] + "\" data-simpletooltip=init title=\"" + $("#comment").val() + "\" class=" + "sufP" + ">" + dataArr[2] + "</p><p class=" + "preP" + ">name:</p><p class=" + "sufP" + ">" + dataArr[1] + "</p><p class=" + "preP" + ">value:</p><p id=" + dataArr[2] + " class=" + "sufP" + ">" + dataArr[3] + "</p></li><br>");

                //update HashMap with json info, we will use this info to record to db
                var json = {
                    id: dataArr[2],
                    name: dataArr[1],
                    value: dataArr[3],
                    tvalue: dataArr[3],
                    comment: $("#comment").val()
                };
                g_idMap.put(dataArr[2], json);
            }
        }
    );
}