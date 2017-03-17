var json = $('.json');

for(var i = 0; i < json.length; i++) {
    var text = $(json[i]).text();
    var data = JSON.parse(text);
    $(json[i]).jsonViewer(data);
}