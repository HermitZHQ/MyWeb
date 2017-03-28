
$(document).ready(function () {
    $("#evaluate").click(function () {
        Evaluate();
    });
});

function Evaluate() {
    $.post("./CTSpider.php",
        {
            id:$("#id").val()
        },
        function (data, textStatus, jqXHR) {
            alert(data);
            try {
                var json = JSON.parse(data);
                if (null !== json) {
                    
                }
            } catch (error) {
                alert(error);
                return;
            }
        }
    );
}