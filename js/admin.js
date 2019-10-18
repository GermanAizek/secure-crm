var accessCatalogs = [];

$( '.dropdown-menu a' ).on( 'click', function( event ) {

   var $target = $( event.currentTarget ),
       val = $target.attr( 'data-value' ),
       $inp = $target.find( 'input' ),
       idx;

   if ( ( idx = accessCatalogs.indexOf( val ) ) > -1 ) {
      accessCatalogs.splice( idx, 1 );
      setTimeout( function() { $inp.prop( 'checked', false ) }, 0);
   } else {
      accessCatalogs.push( val );
      setTimeout( function() { $inp.prop( 'checked', true ) }, 0);
   }

   $( event.target ).blur();
      
   // console.log( accessCatalogs );
   return false;
});

// TODO: Создай два разных массива доступа к каталогам, иначе пхп берет берет не заполненный массив с создания аккаунта

// CREATE USER

$("#createUserForm").validator().on("submit", function (event) {
    if (event.isDefaultPrevented()) {
        // handle the invalid form...
        //submitMSG(false, "Введите данные для входа");
    } else {
        // everything looks good!
        event.preventDefault();
        submitCreateUserForm();
    }
});

function submitCreateUserForm() {
    var user = $("#usr").val();
    var pass = $("#pwd").val();
    var admin = $("#isAdmin").is(":checked");
    var accessText = accessCatalogs;
 
    $.ajax({
        type: "POST",
        url: "admin.php",
        data: {usrReg: user, pwdReg: pass, accessCats: accessText, isAdmin: admin},
        success : function(text) {
            if (text == "suckcess") {
                alert("Пользователь создан");
                formSuccess("#createUserForm");
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

function formSuccess(idReset) {
    $(idReset)[0].reset();
    submitMSG(true, "Действие выполнено")
}

function formFail() {
    submitMSG(false, "Действие не выполнено")
}

function submitMSG(valid, msg) {
    if(valid) {
        var msgClasses = "h4 text-center tada text-success";
    } else {
        var msgClasses = "h4 text-center text-danger";
    }
    $("#msgSubmit").removeClass().addClass(msgClasses).text(msg);
}

// DELETE USER

$("#deleteUserForm").validator().on("submit", function (event) {
    if (event.isDefaultPrevented()) {
        submitMSG(false, "Выберите пользователя для удаления");
    } else {
        event.preventDefault();
        submitDeleteUserForm();
    }
});

$("#deleteUserList a").click(function(e){
    e.preventDefault(); // cancel the link behaviour
    var selectUser = $(this).text();
    $("#deleteUserList").dropdown("toggle");
    $("#deleteUserButton").text(selectUser);
});

$("#editUserList a").click(function(e){
    e.preventDefault(); // cancel the link behaviour
    var selectUser = $(this).text();
    $("#editUserList").dropdown("toggle");
    $("#editUserButton").text(selectUser);
});

function submitDeleteUserForm() {
    var name = $("#deleteUserButton").text();
    // alert(name);
 
    $.ajax({
        type: "POST",
        url: "admin.php",
        dataType: 'json',
        data: {usrDel: name},
        success : function(text){
            if (text == "suckcess") {
                formSuccess("#deleteUserForm");
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

// EDIT USER

$("#editUserForm").validator().on("submit", function (event) {
    if (event.isDefaultPrevented()) {
        submitMSG(false, "Выберите пользователя для редактирования");
    } else {
        event.preventDefault();
        submitEditUserForm();
    }
});

function submitEditUserForm() {
    var name = $("#editUsr").val();
    var pass = $("#editPwd").val();
    var admin = $("#editIsAdmin").is(":checked");
    var accessText = accessCatalogs;
    var nameSelect = $("#editUserButton").text();
 
    $.ajax({
        type: "POST",
        url: "admin.php",
        dataType: 'json',
        data: {usrEdit: name, pwdEdit: pass, accessCats: accessText, isAdmin: admin, usrSelect: nameSelect},
        success : function(text) {
            if (text == "suckcess") {
                formSuccess("#editUserForm");
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

// CREATE CATALOG

$("#createCatalogForm").validator().on("submit", function (event) {
    if (event.isDefaultPrevented()) {
        submitMSG(false, "Введите данные для создания каталога");
    } else {
        event.preventDefault();
        submitCatalogForm();
    }
});

function submitCatalogForm() {
    var name = $("#nam").val();
 
    $.ajax({
        type: "POST",
        url: "admin.php",
        dataType: 'json',
        data: {catalogAddName: name},
        success : function(text){
            if (text == "suckcess") {
                formSuccess("#createCatalogForm");
                //location.href = location.protocol + "//" + location.hostname + "/main.html";
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

// DELETE CATALOG

$("#deleteCatalogForm").validator().on("submit", function (event) {
    if (event.isDefaultPrevented()) {
        submitMSG(false, "Выберите каталог для удаления");
    } else {
        event.preventDefault();
        submitDeleteCatalogForm();
    }
});

$("#deleteCatalogList a").click(function(e){
    e.preventDefault(); // cancel the link behaviour
    var selectCatalog = $(this).text();
    $("#deleteCatalogList").dropdown("toggle");
    $("#deleteCatalogButton").text(selectCatalog);
});

function submitDeleteCatalogForm() {
    var name = $("#deleteCatalogButton").text();
 
    $.ajax({
        type: "POST",
        url: "admin.php",
        dataType: 'json',
        data: {catalogDelName: name},
        success : function(text){
            if (text == "suckcess") {
                formSuccess("#deleteCatalogForm");
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

// DELETE FILE

$("#deleteFileForm").validator().on("submit", function (event) {
    if (event.isDefaultPrevented()) {
        submitMSG(false, "Выберите каталог для удаления");
    } else {
        event.preventDefault();
        submitDeleteFileForm();
    }
});

$("#deleteFileList a").click(function(e){
    e.preventDefault(); // cancel the link behaviour
    var selectCatalog = $(this).text();
    $("#deleteFileList").dropdown("toggle");
    $("#deleteFileButton").text(selectCatalog);
});

function submitDeleteFileForm() {
    var name = $("#deleteFileButton").text();
 
    $.ajax({
        type: "POST",
        url: "admin.php",
        dataType: 'json',
        data: {fileDelName: name},
        success : function(text){
            if (text == "suckcess") {
                formSuccess("#deleteFileForm");
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

// UPLOAD FILE

$("#uploadFileList a").click(function(e){
    e.preventDefault(); // cancel the link behaviour
    var selectCatalog = $(this).text();
    $("#uploadFileList").dropdown("toggle");
    $("#uploadFileButton").text(selectCatalog);
    // $("#catalogUploadFile").text(selectCatalog);
    document.getElementById("catalogUploadFile").setAttribute("value", selectCatalog);
});

// function submitUploadFileForm() {
//     var name = $("#uploadFileButton").text();
 
//     $.ajax({
//         type: "POST",
//         url: "admin.php",
//         dataType: 'json',
//         data: {catalogUploadName: name},
//         success : function(text){
//             if (text == "suckcess") {
//                 formSuccess("#uploadFileForm");
//             }

//             if (text == "invalid") {
//               formFail();
//             }
//         }
//     });
// }
