/*!
 * jQuery Ajax Code for Clockwork SMS
 */
jQuery(document).ready(function($) {

    $("#submit").click(function() {

        var name = $("#phone").val();

        $.ajax({
            type: 'POST',
            url: clockworkajax.ajaxurl,
            data: {
                "action": "clockwork_send_sms",
                "phone": name,
                "clockworkNonce": clockworkajax.clockworkNonce
            },
            beforeSend: function() {
                $("#result").empty();
                $("#result").html('<img src="' + clockworkajax.loading + '" />');
            },
            success: function(data) {
                $("#result").html(data);
            }
        });
    });
});
