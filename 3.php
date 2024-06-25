$(document).ready(function() {
    $.ajax({
        url: 'load_urls.php', // Replace with your server-side script to load URLs
        success: function(response) {
            var urls = response.split('\n');
            urls.forEach(function(url) {
                if (url.trim() !== '') {
                    $('#youtubelist').append('<li><a href="' + url + '">' + url + '</a></li>');
                }
            });
        },
        error: function(xhr, status, error) {
            alert('Error loading URLs');
            console.error(xhr, status, error);
        }
    });
});
