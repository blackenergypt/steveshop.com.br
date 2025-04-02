$('.nav-drop .drop-link').on('click', function (e) {
    e.preventDefault();

    var $this = $(this);
    var id = $this.attr('id');

    var div = id.split('-')[1];
    var nav = $(`#nav-${div}`);

    $this.toggleClass('actived');
    nav.toggle();

    return false;
});

var editor = CodeMirror.fromTextArea(document.getElementById('editor'), {
    lineNumbers: true,
    theme: 'dracula',
    mode: 'text/html',
    tabSize: 4,
    indentUnit: 4,
    autoCloseTags: true,
    lineWrapping: true,
    scrollbarStyle: 'overlay',
    dragDrop: true,
    extraKeys: {
        "Ctrl-Space": "autocomplete",
        "Ctrl-S": save,
    },
});

editor.setSize(null, '82%');

function save() {

    let content = editor.getValue();
    let value = $('input[name=value]').val();

    if(value === '')
        return toastr.warning('Você ainda não escolheu um arquivo', 'Ops...');

    if(content === '')
        return toastr.warning('Você não pode deixar o arquivo em branco', 'Ops...');

    $.ajax({
        url: '/home/save',
        method: 'POST',
        data: { value, content },
        complete: function () {
            toastr.success('Arquivo salvo com sucesso!');
        }
    });
}

function set(content, filename, type = 'text/html') {
    $('#editor_title').html(filename);

    editor.setOption('mode', type);
    editor.setValue(content);
}