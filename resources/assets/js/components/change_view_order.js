$(document).ready(function() {
    // block multiple submitting
    var submitting = false;

    $(document).on('focus', 'input.view_order', function(){
        $(this).on('keyup', function(e){
            if(e.keyCode == 13) {
                if (!submitting) {
                    submitting = true;
                    var input_box = this;
                    var type = $('input[name="object_type"]').val();
                    var data = {
                        'from' : $(this).next().val(),
                        'to' : $(this).val(),
                        'type': type,
                        'page': $('input[name="current_page"]').val(),
                    };

                    if (type === '2') {
                        data['current_work_location'] = $('input[name="current_work_location"]').val();
                    }

                    axios.post($.companyCodeIncludedUrl('/change_view_order'), data).then( response => {
                        $('table.table_with_fixed_header').html(response.data);
                        submitting = false;
                    }).catch( error => {
                        var parent = $(input_box).parent();
                        parent.addClass('error');
                        $(parent).find('span.tool_error').text('Invalid number!');
                        submitting = false;
                    })
                }
            }
        });
    });
});