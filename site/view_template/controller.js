console.log("script works");
        $(document).ready(function() {

            console.log("ready");
            // Collect data on page load

            // Make an HTTP POST request
            $.ajax({
                url: '/apis/proxy.php', // Replace with your server endpoint
                method: 'POST',
                data: { url: "http://api:80/database/messages/2.php", user: "root", password: "password" },
                
                success: function(response) {
                    // Display the response from the server
                    console.log("success");
                    $('#contents').text('Server Response: ' + response);
                },
                error: function(error) {
                    console.log("error");
                    $('#contents').text('Error: ' + error.statusText);
                }
            });
        });
