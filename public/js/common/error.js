function errorHandler(errors) {
    for(let name in errors) {
        let messages = errors[name];
        if(Array.isArray(messages)) {
            setErrorMessage(name, messages);
        }else if(typeof messages === "string"){
            console.log(name + ": " + messages);
        }
    }
}

function setErrorMessage(name, messages) {
    addInputError(name, messages);
}

function addInputError(id, messages) {
    let html = "";
    for(let i in messages) {
        html += "<div class=\"ui error message\">\n" +
            "<p>" + messages[i] + "</p>\n" +
            "</div>";
    }
    $("#" + id).parents(".ui.form").addClass("error");
    $("#" + id).parent(".field").addClass("error").append(html);
}

function removeInputError(id) {
    $("#" + id).parents(".ui.form").removeClass("error");
    $("#" + id).parent(".field").removeClass("error").children(".ui.error.message").remove();
}