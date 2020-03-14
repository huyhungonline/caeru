$(document).ready(function() {

    // Get the type of the message
    $.fn.messageType = function() {
        if (this.parent().hasClass('save_green'))
            return 'success';
        else if (this.parent().hasClass('save_yellow'))
            return 'warning';
        else
            return 'error';
    }

    // Check type and show the message
    function showMessage(type, message) {
        var parent;
        if (type === 'error') {
            parent = $('.alert_innner > .alert_box.save_red');
            message = '入力に誤りがあります';
        } else if (type === 'warning') {
            parent = $('.alert_innner > .alert_box.save_yellow');
        } else {
            parent = $('.alert_innner > .alert_box.save_green');
        }

        // Show the message
        var messageElement = '<span class="alert_content">' + message + '</span>';
        parent.html(parent.html() + messageElement);

        parent.slideDown(500).delay(500).fadeOut('slow', function() {
            $(this).children('span.alert_content').remove();
        });
    }


    // This function will be attach to the dom object so that it can be used later by other javascript modules.
    // There are three types: success, warning, error
    document.caeru_alert = function caeru_alert(type, message) {
        var currentMessage = $('.alert_innner > .alert_box > .alert_content');

        if (currentMessage.exists() && currentMessage.is(':visible')) {

            if (currentMessage.messageType() !== 'error') {
                if (type === 'error') {
                    $('.alert_innner > .alert_box').finish();
                    showMessage(type, message);
                }
            }

        } else {
            showMessage(type, message);
        }
    };

    // This part handle the message come from the server. We expect only one message at a given point.

    // Detect the messages from the server.
    var messages = $('.alert_innner > .alert_box > .alert_content');

    // Show the message if exists.
    if (messages.exists()) {
        messages.first().parent().slideDown(500).delay(2000).fadeOut();
    }
});