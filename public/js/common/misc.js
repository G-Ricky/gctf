function openLoader(message = "") {
    $("#global-loader").children(".loader").html(message);
    $("#global-loader").addClass("active");
}
function closeLoader() {
    $("#global-loader").removeClass("active");
}