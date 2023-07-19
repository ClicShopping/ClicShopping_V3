CKEDITOR.editorConfig = function( config ) {
    // Define changes to default configuration here. For example:

    config.defaultLanguage = 'fr';
    config.entities_latin = false;
    config.basicEntities = false;
    config.entities_greek = false;

    config.entities_additional = '';
    config.entities = false;

//Language spell
    config.scayt_autoStartup = true;
    config.enterMode = CKEDITOR.ENTER_BR;

    config.font_names =
        'Arial/Arial;' +
        'Century Gothic;' +
        'Comic Sans MS;' +
        'Courrier New;' +
        'Tahoma;' +
        'Helvetica;' +
        'Times New Roman/Times New Roman, Times, serif;' +
        'Verdana;';
    /* extra plugin
      config.extraPlugins = 'featurette';
    */

    // The toolbar groups arrangement, optimized for a single toolbar row.
    config.toolbarGroups = [
        { name: 'document',    groups: [ 'mode', 'document', 'doctools' ] },
        // On the basic preset, clipboard and undo is handled by keyboard.
        // Uncomment the following line to enable them on the toolbar as well.
        { name: 'clipboard',   groups: [ 'clipboard', 'undo' ] },
        { name: 'editing',     groups: [ 'find', 'selection', 'spellchecker' ] },
        //    { name: 'forms' },
        { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
        { name: 'paragraph',   groups: [ 'list', 'indent', 'blocks', 'align' ] },
        { name: 'links' },
        { name: 'insert'},
        { name: 'styles' },
        { name: 'colors' },
        { name: 'tools'},
        { name: 'others' }
    ];

// The default plugins included in the basic setup define some buttons that
// we don't want too have in a basic editor. We remove them here.
    config.removeButtons = 'Save,Subscript,Superscript,Iframe,PageBreak';

// Considering that the basic setup doesn't provide pasting cleanup features,
// it's recommended to force everything to be plain text.
    config.forcePasteAsPlainText = true;

    config.allowedContent = true;

// toolbar for image download only
    config.toolbar = 'Image';
    config.toolbar_Image =
        [
            ['Source','-','Image']
        ];

// Add ChatGPT plugin to toolbar
    config.extraPlugins = 'chatgpt';

    config.allowedContent = true;
};

CKEDITOR.plugins.add('chatgpt', {
    init: function(editor) {
        editor.addCommand('chatgptDialog', new CKEDITOR.dialogCommand('chatgptDialog'));

        editor.ui.addButton('chatgpt', {
            label: titleGpt,
            command: 'chatgptDialog',
            toolbar: 'insert,10',
            icon:  'Iframe', //Gpt
        });

        CKEDITOR.dialog.add('chatgptDialog', this.path + 'dialogs/chatgpt.js');
    }
});