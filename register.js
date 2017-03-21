/**
 * Created by Administrator on 2017/3/8.
 */

//get param first
function ParseParam() {
    var vs = self.location.href.split("?")[1];
    if ( null === vs )
        return;

    var vArr = vs.split(",");
    var len = vArr.length;
    if (len > 0) {
    }
}

$(document).ready(function () {
    ParseParam();

    $("#btnSubmit").click(function () {
        //check null first
        var bRes = CheckRegisterInfo();

        if (bRes) {
            $.post(
                "register.php",
                {
                    name: $("#accountName").val(),
                    pw: $("#pw").val(),
                    pwConfirm: $("#pwConfirm").val(),
                    email: $("#email").val(),
                    phone: $("#phone").val()
                },
                function (data, status) {
                    var json = JSON.parse(data);
                    //alert("Data:"+data+"\nStatus:"+status+"\nrep name:"+json.nameRepeat);

                    ShowRegisterResult(json);
                }
            );
        }

    });
});

function ClearRegisterTips() {
    $("#accountTip").html("");
    $("#pwTip1").html("");
    $("#pwTip2").html("");
    $("#emailTip").html("");
    $("#phoneTip").html("");
}

function VerifyEmailFormat(email) {
    var reg = /\w+[@]{1}\w+[.]\w+/;
    if (!reg.test(email))
        return false;

    return true;
}

function CheckRegisterInfo() {
    ClearRegisterTips();

    if ($("#accountName").val().length < 4) {
        $("#accountTip").html("length less than 4");
        return false;
    }

    if ($("#pw").val().length < 6) {
        $("#pwTip1").html("length less than 6");
        return false;
    }

    if ($("#pwConfirm").val().length < 6) {
        $("#pwTip2").html("length less than 6");
        return false;
    }
    else {
        if ($("#pwConfirm").val() != $("#pw").val()) {
            $("#pwTip2").html("mismatched");
            return false;
        }
    }

    var email = $("#email").val();
    if (!VerifyEmailFormat(email)) {
        $("#emailTip").html("请输入正确的email地址");
        return false;
    }

    return true;
}

function ShowRegisterResult(json) {
    ClearRegisterTips();

    if (json.nameRepeat !== 0) {
        $("#accountTip").html("name existed");
    }

    if (json.emailRepeat !== 0) {
        $("#emailTip").html("email existed");
    }

    if (json.newResult == 1) {
        $("#registerTip").html("Congratulation, Register succeed!will return after 3s...");
        setTimeout(function () {
            window.location.href = "main.html?account="+$("#accountName").val();
        }, 3000);
    }
}