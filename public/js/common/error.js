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

function handleError(jqXHR, textStatus, error) {
    if(textStatus !== "parsererror" && jqXHR.status === 422) {
        let messages = "";
        let response = JSON.parse(jqXHR.responseText);
        for(let key in response.errors) {
            let error = response.errors[key];
            for(let i = 0;i < error.length;++i) {
                messages += "<p>" + error[i] + "</p>";
            }
        }

        tip.error(messages);
        return;
    } else if(jqXHR.readyState === 0) {
        tip.error("网络未连接！");
    } else {
        tip.error("未知错误！");
    }
}