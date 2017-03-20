
$(document).ready(function () {
    $("#test").click(function () {
        TestBtn();
    });

    document.domain = 'qq.com';
});

function TestBtn() {
    var wnd = self.window.open("http://gu.qq.com/sz000568");

    $.get("http://gu.qq.com/sz000568", function (data) {
        alert("Data Loaded: " + data);
    });
}