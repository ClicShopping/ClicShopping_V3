$(document).ready(function() {
    // Initialize the clipboard for result button
    var clipboardResult = new ClipboardJS("#copyResultButton");

    // Handler for when the result button is clicked
    clipboardResult.on("success", function(e) {
        // Show a tooltip indicating that the text was copied
        $(e.trigger).tooltip({title: "Copied!", placement: "bottom", trigger: "manual"}).tooltip("show");
        setTimeout(function() {
            $(e.trigger).tooltip("hide");
        }, 1000);
        e.clearSelection();
    });

    // Initialize the clipboard for HTML button
    var clipboardHTML = new ClipboardJS("#copyHTMLButton", {
        target: function() {
            return document.querySelector("#chatGpt-output");
        }
    });

    // Handler for when the HTML button is clicked
    clipboardHTML.on("success", function(e) {
        // Show a tooltip indicating that the HTML was copied
        $(e.trigger).tooltip({title: "Copied HTML!", placement: "bottom", trigger: "manual"}).tooltip("show");
        setTimeout(function() {
            $(e.trigger).tooltip("hide");
        }, 1000);
        e.clearSelection();
    });

    $("#sendGpt").click(function() {
        let message = $("#messageGpt").val();
        let engine = $("#engine").val();
        let saveGptElement = document.querySelector("#saveGpt");
        let saveGpt = saveGptElement ? (saveGptElement.checked ? 1 : 0) : 0;

        // Use the declared variables here
        let data = {
            message: message,
            engine: engine,
            saveGpt: saveGpt,
        };

        $.post(url_chat, data, function(data) {
            $("#chatGpt-output").html(data);
            // Show the copy buttons after the chat message is generated
            $("#copyResultButton, #copyHTMLButton").removeClass("d-none");
        });
    });


    // Clear button functionality with debugging
    $("#clearGpt").click(function() {
        $("#messageGpt").val(""); // Clear the input field
        $("#chatGpt-output").html(""); // Clear the output field
    });

    // Adjust modal size and position on show
    $("#chatModal").on("show.bs.modal", function() {
        $(".modal-right .modal-dialog").css({
            "right": 0,
            "top": 0,
            "height": "100%",
            "margin": 0
        });
        $(".modal-right .modal-content").css({
            "height": "100%",
            "border": 0,
            "border-radius": 0
        });
        $(".modal-right .modal-body").css({
            "max-height": "calc(100% - 56px)",
            "overflow-y": "auto"
        });
    });
});