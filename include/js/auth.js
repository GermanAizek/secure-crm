$("#contactForm").validator().on("submit", function (event) {
    if (event.isDefaultPrevented()) {
        // handle the invalid form...
        submitMSG(false, "Введите данные для входа");
    } else {
        // everything looks good!
        event.preventDefault();
        submitForm();
    }
});

function submitForm() {
    // Initiate Variables With Form Content
    var user = $("#usr").val();
    var pass = $("#pwd").val();
 
    $.ajax({
        type: "POST",
        url: "login.php",
        dataType: 'json',
        data: {usr: user, pwd: pass},
        success : function(text){
            if (text == "suckcess") {
                formSuccess();
                location.href = location.protocol + "//" + location.hostname + "/main.html";
            }

            if (text == "invalid") {
              formFail();
            }
        },
        error: function(data) {
          alert(data.responseText);
        }
    });
}

function formSuccess() {
    $("#contactForm")[0].reset();
    submitMSG(true, "Вход выполнен успешно")
}

function formFail() {
    submitMSG(false, "Указанные данные неверны")
}

function submitMSG(valid, msg) {
    if(valid){
        var msgClasses = "alert alert-success";
    } else {
        var msgClasses = "alert alert-danger";
    }
    $("#msgSubmit").removeClass().addClass(msgClasses).text(msg);
}