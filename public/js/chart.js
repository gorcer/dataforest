refreshGraph = function() {
    myChart.data.datasets=[];
    $('#selectField').val().forEach(function(elem){

        myChart.data.datasets.push(dataset[elem]);
    });
    myChart.update();
    refreshLink();

}

refreshLink = function() {

    var link = reportLink + '?' + $('#selectField').serialize();
    link = link.replace('%group%', $('#grouping').val());
    $('#reportLink').text(link);
    return link;
}


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

/*
$('#selectField').multiselect({
    onChange: function(option, checked, select) {
        refreshGraph();
    },
    buttonClass: 'btn btn-outline-dark'
});*/

$('#selectField').selectpicker();
$('#selectField').change(function(){
    refreshGraph();
});

$('#grouping').change(function(){
   var link = refreshLink();
   window.location = link;
});