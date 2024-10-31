function PDFtoIMG_delete_file(rnd)
{
    jQuery(document).ready(function($) 
    {
        var fileReference = jQuery.trim(jQuery("#delete_file_" + rnd).val());
        var contact_form_nonce = jQuery.trim(jQuery("#contact_form_nonce").val());

        var answer = confirm('Are you sure you want to delete "' + fileReference + '"?');

        if (answer) 
        {
            jQuery.ajax({
                type: "POST",
                url: ajaxurl,
                data: {
                    action: 'PDFtoIMG_delete_file',
                    filename : fileReference,
                    contact_form_nonce : contact_form_nonce
                },
                dataType: "text",

                success: function(response) 
                {
                    location.reload();
                },
        
                error: function(error) 
                {
                    console.log(error);
                }
            });
        }
     });
}

jQuery(document).ready(function($) 
{
    $("#uploadTrigger").click(function() 
    {
        $("#uploaded_files").click();
    });

    $("#uploaded_files").change(function() 
    {
        var fileNames = [];
        var input = this;
        for (var i = 0; i < input.files.length; i++) 
        {
            fileNames.push(input.files[i].name);
        }

        $("#fileNames").text(fileNames.join(', '));
    });
});
