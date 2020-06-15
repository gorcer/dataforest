chartPallete = [
    '#4dc9f6',
    '#f67019',
    '#f53794',
    '#537bc4',
    '#acc236',
    '#166a8f',
    '#00a950',
    '#58595b',
    '#8549ba',

    '#8da9f6',
    '#06b019',
    '#05b794',
    '#a3dbc4',
    '#0c3236',
    '#e6aa8f',
    '#b03950',
    '#f8c95b',
    '#f589ba'
];


$('#inp-type').on('change', function (e) {
    $('.tab-content .tab-pane').hide();
    $('.tab-content .tab-pane#'+$(this).val()).show();
});


$('#tryIt').click(function(e) {
    var formData = $('#collectorForm').serialize();

    $('#tryIt').hide();
    $('#tryIt-loading').removeClass('d-none');
    $('#tryIt-loading').show();

    $('#collectorForm .result').hide();
    $.ajax({
        url: testUrl,
        type: 'POST',
        dataType: 'json',
        data: formData,
        success: function (data) {


            $('#collectorForm .result').removeClass('d-none');
            $('#collectorForm .result').show();
            $('#collectorForm .result .resultTable').html(data.data);

            if (data.status == 'ok') {
                $('#collectorForm #save').removeClass('d-none');
            }

            if (data.status == 'problem') {
                $('#collectorForm .problem').show();
                $('#collectorForm .problem').text(data.problem);
            } else {
                $('#collectorForm .problem').hide();
            }
        }
    }).fail(function() {
        console.log( "error" );
    }).always(function( data ){
        $('#tryIt-loading').hide();
        $('#tryIt').show();
    });;
});

$('#copy2clipboard').click(function(){

    refreshLink();
    $('#reportLink').select();
    document.execCommand('copy');


    $('#copied').removeClass('d-none');
    $('#copied').attr('opacity', 1);
    $( "#copied" ).animate({
        opacity: 0
    }, 1000);

});

refreshGraph = function() {
    myChart.data.datasets=[];
    $('#selectField').val().forEach(function(elem){
        if (dataset[elem])
            myChart.data.datasets.push(dataset[elem]);
    });
    myChart.update();
    refreshLink();

}

refreshLink = function() {

    var link = reportLink + '?' + $('#selectField').serialize() +'&'+ $('#toolBox').serialize();
    link = link.replace('%group%', $('#grouping').val());
    link = link + '&period=' + $('#period').val();
 //   link = link+'&start='+start.format('YYYY-MM-DD')+'&end=' + end.format('YYYY-MM-DD');

    $('#reportLink').text(link);
    return link;
}


if ($('#selectField').length > 0) {
    $('#selectField').selectpicker();
    $('#selectField').change(function(){
        refreshGraph();
    });

}

$('#grouping, #period').change(function(){
    var link = refreshLink();

    // Для IFRAME
    let searchParams = new URLSearchParams(window.location.search);
    if (searchParams.has('cl')) {
        link = link + '&cl=1';
    }

    window.location = link;
});

// init datepicker
//$('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));


function changeDatePicker(startIn, endIn) {
    start = startIn;
    end = endIn;

    $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
    var link = refreshLink();

    window.location = link;
}
/*
$('#reportrange').daterangepicker({
}, changeDatePicker);
*/
