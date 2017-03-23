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

    $("#toTop").click(function () {
        ToTop();
    });

    $("#find").click(function(){
        Find();
    });

    setInterval('UpdateInfo()', 4500);
});

function Find(){
    self.location.href = "#"+$("#id").val();
}

function ToTop() {
    self.location.href = "#topAnchor";
}

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
                    //comment id:cmt+id(we can use this id to update the comment)
                    //value id:id
                    //add li to dom
                    var strID = PadIDWithZero(json.id);
                    $("#infoList").append("<li><p class=" + "preP" + ">id:</p><p id=\"" + "cmt" + strID + "\" data-simpletooltip=init title=\"" + json.comment + "\" class=" + "sufP" + ">" + strID + "</p><p class=" + "preP" + ">name:</p><p class=" + "sufP" + ">" + json.name + "</p><p class=" + "preP" + ">value:</p><p id=" + strID + " class=" + "sufP tvalue=" + json.tvalue + ">" + json.value + "</p></li><br>");

                    //update HashMap with json info, we will use this info to record to db
                    var json2 = {
                        id: json.id,
                        name: json.name,
                        value: json.value,
                        tvalue: json.tvalue,
                        comment: json.comment
                    };
                    g_idMap.put(strID, json2);
                }

                $(document).click(function(e){
                    HandleListAreaPClick(e);
                });
            }
        }
    );
}

function HandleListAreaPClick(e){
    if ($(e.target).is("p") === true && typeof($(e.target).attr("tvalue")) !== "undefined")
    {
        $("#tvalue").val($(e.target).attr("tvalue"));
        $("#comment").val($("#cmt"+$(e.target).attr("id")).attr("title"));
        $("#id").val($(e.target).attr("id"));
    }
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

    //update list data
    $.post("./Crawler.php", {
        ids: idArr
    },
        function (data, textStatus, jqXHR) {
            //alert(data);

            var infoArr = data.split(";");
            if (infoArr.length === 0) {
                return;
            }

            $("#tipList").empty();

            //semicolon will separate the data to two, the second part is "\n", we should ignore it
            for (var i = 0; i < infoArr.length - 1; i++) {
                var dataArr = infoArr[i].split("~");
                if (dataArr.length === 0) {
                    continue;
                }

                $("#" + dataArr[2]).html(dataArr[3]);

                //update tip list
                if (dataArr[3] < parseFloat($("#"+dataArr[2]).attr("tvalue"))) {
                    $("#tipList").append("<li>"+dataArr[2]+":"+dataArr[1]+"("+dataArr[3]+"/"+$("#"+dataArr[2]).attr("tvalue")+")</li>");
                }
            }
        }
    );

}

function PadIDWithZero(str) {
    var pad = "000000";
    return pad.substring(0, pad.length - str.length) + str;
}

function ChangeId() {
    var tmp = PadIDWithZero(g_id.toString());
    $("#id").val(tmp);
    g_id++;
    AddOneInfo();
}

function AddOneInfo() {
    if ($("#id").val() <= 0 || $("#id").val().length < 6) {
        alert("id format is not valid");
        return;
    }

    //don't add repeat id, but we should update it
    if (g_idMap.containsKey($("#id").val())) {
        $("#cmt" + $("#id").val()).attr("title", $("#comment").val());
        var json = g_idMap.get($("#id").val());
        json.comment = $("#comment").val();
        json.tvalue = $("#tvalue").val();
        g_idMap.put($("#id").val(), json);

        //update element content
        $("#"+json.id).attr("tvalue", $("#tvalue").val());

        //clear ele content
        $("#comment").val("");
        $("#tvalue").val("");
        $("#id").val("");

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

                //comment id:cmt+id(we can use this id to update the comment)
                //value id:id
                //add li to dom
                $("#infoList").append("<li><p class=" + "preP" + ">id:</p><p id=\"" + "cmt" + dataArr[2] + "\" data-simpletooltip=init title=\"" + $("#comment").val() + "\" class=" + "sufP" + ">" + dataArr[2] + "</p><p class=" + "preP" + ">name:</p><p class=" + "sufP" + ">" + dataArr[1] + "</p><p class=" + "preP" + ">value:</p><p id=" + dataArr[2] + " class=" + "sufP tvalue=" + $("#tvalue").val() + ">" + dataArr[3] + "</p></li><br>");

                //update HashMap with json info, we will use this info to record to db
                var json = {
                    id: dataArr[2],
                    name: dataArr[1],
                    value: dataArr[3],
                    tvalue: $("#tvalue").val(),
                    comment: $("#comment").val()
                };
                g_idMap.put(dataArr[2], json);
                $("#comment").val("");
            }
        }
    );
}