var wnd;

$(document).ready(function () {
    //alert("main page ready");
    ParseParam();

    $("button#toReg").click(function () {
        self.location.href = "register.html";
    });
})

function ParseParam() {
    var strParams = location.href.split("?")[1];
    if (null == strParams)
        return;

    var vArr = strParams.split(",");
    if (vArr.length > 0) {
        alert("received " + vArr.length + " params");

        if ( vArr.length == 1 && vArr[0].split("=")[0] == "account" )
        {
            
        }
    }
}