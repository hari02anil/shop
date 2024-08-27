$(document).ready(function() {
    // Check username availability
    $('#username').on('input', function() {
        var username = $(this).val();
        if (username !== '') {
            $.ajax({
                url: 'check_availability.php',
                method: 'POST',
                data: { username: username },
                success: function(response) {
                    var data = JSON.parse(response);
                    if (data.status == 'error') {
                        $('#username-status').text(data.message).css('color', 'red');
                        $('#registerButton').prop('disabled', true);
                        console.log("change it");
                    } else {
                        $('#username-status').text(data.message).css('color', 'green');
                        $('#registerButton').prop('disabled', false);
                    }
                }
            });
        }
    });

    // Check email availability
    $('#email').on('input', function() {
        var email = $(this).val();
        if (email !== '') {
            $.ajax({
                url: 'check_availability.php',
                method: 'POST',
                data: { email: email },
                success: function(response) {
                    var data = JSON.parse(response);
                    if (data.status === 'error') {
                        $('#email-status').text(data.message).css('color', 'red');
                        $('#registerButton').prop('disabled', true);
                    } else {
                        $('#email-status').text(data.message).css('color', 'green');
                        $('#registerButton').prop('disabled', false);
                    }
                }
            });
        }
    });
});