$('#inp-type').on('change', function (e) {
    $('.tab-content .tab-pane').hide();
    $('.tab-content .tab-pane#'+$(this).val()).show();
});


$('#tryIt').click(function(e) {
    var formData = $('#addCollector').serialize();

    $('#tryIt').hide();
    $('#tryIt-loading').removeClass('d-none');
    $('#tryIt-loading').show();

    $('.createCollector .result').hide();
    $.ajax({
        url: '/collector/test',
        type: 'POST',
        dataType: 'json',
        data: formData,
        success: function (data) {


            $('.createCollector .result').removeClass('d-none');
            $('.createCollector .result').show();
            $('.createCollector .result .resultTable').html(data.data);

            if (data.status == 'ok') {
                $('.createCollector #save').removeClass('d-none');
            }

            if (data.status == 'problem') {
                $('.createCollector .problem').show();
                $('.createCollector .problem').text(data.problem);
            } else {
                $('.createCollector .problem').hide();
            }
        }
    }).fail(function() {
        console.log( "error" );
    }).always(function( data ){
        $('#tryIt-loading').hide();
        $('#tryIt').show();
    });;
});