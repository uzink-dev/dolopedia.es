var multimediaVars = new function() {
    var parent = this;
    var editor;
    var insertMode;
    var spinner;
    this.popupTarget = '#addMultimediaPopup';
    this.formTarget = '#multimediaUploadPopup';

    this.fancyboxOptions = {
        'type'        : 'inline',
        'padding'     : 0,
        'margin'      : 10,
        'width'	    : '100%',
        'maxWidth'    : 620,
        'minWidth'    : 280,
        'height'      : 'auto',
        'fitToView'   : false,
        'autoSize'    : false,
        'modal'       : true,
        'href'        : parent.popupTarget,
        'afterClose'    : function() {
            $(multimediaVars.popupTarget + ' .btn-file :file').off();
        },
        helpers: {
            overlay: {
                locked: false
            }
        }
    };

    this.fileUpload = {
        uploadOptions : { dataType : 'json' },
        submitOptions : { dataType : 'json' },
        before        : function(){
            var spinnerElement = $(multimediaVars.popupTarget).find('.spinner');
            spinner = setSpinner(spinnerElement, 'small');
        },
        beforeSubmit  : function(uploadData){
            spinner.stop();
            if ( typeof uploadData['basename'] != 'undefined' ) insertElement(multimediaVars.editor, uploadData);
            $.fancybox.close();
        }
    };
};

CKEDITOR.plugins.add( 'multimedia', {
    icons: 'multimedia',
    init: function( editor ) {
        editor.addCommand('multimediaDialog', {
            exec: multimediaDialog,
            allowedContent: 'a(download-link)[!data-multimedia-name,href,target,download]'
        });
        editor.addCommand('multimediaRemoveDialog', {
            exec: multimediaRemoveDialog,
            allowedContent: 'a(download-link)[!data-multimedia-name,href,target,download]'
        });
        editor.ui.addButton( 'Multimedia', {
            label: 'Añadir Descarga',
            command: 'multimediaDialog'
        });
        if ( editor.contextMenu ) {
            editor.addMenuGroup( 'mGroup' );
            editor.addMenuItem( 'mItem', {
                label: 'Editar descarga',
                icon: this.path + 'icons/multimedia.png',
                command: 'multimediaDialog',
                group: 'mGroup'
            });
            editor.addMenuItem( 'mrItem', {
                label: 'Eliminar descarga',
                icon: this.path + 'icons/multimedia.png',
                command: 'multimediaRemoveDialog',
                group: 'mGroup'
            });

            editor.contextMenu.addListener( function( element ) {
                var item =  element.getAscendant( 'a', true );
                if ( item && element.data( 'multimedia-name' ) != null ) {
                    return {
                        mItem: CKEDITOR.TRISTATE_OFF,
                        mrItem: CKEDITOR.TRISTATE_OFF
                    };
                }
            });
        }

        $(multimediaVars.formTarget).fileUpload(multimediaVars.fileUpload);
    }
});

function multimediaDialog(editor) {
    $(multimediaVars.popupTarget).on('change', '.btn-file :file', function() {
        var input = $(this),
            numFiles = input.get(0).files ? input.get(0).files.length : 1,
            label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
        input.trigger('fileselect', [numFiles, label]);
    });

    $(multimediaVars.popupTarget + ' .btn-file :file').on('fileselect', function(event, numFiles, label) {

        var input = $(this).parents('.input-group').find(':text'),
            log = numFiles > 1 ? numFiles + ' files selected' : label;

        if( input.length ) {
            input.val(log);
        } else {
            if( log ) alert(log);
        }
    });

    multimediaVars.editor = editor;
    createElement(editor);
    $.fancybox(multimediaVars.fancyboxOptions);
}

function createElement(editor) {
    var selection = editor.getSelection(),
        element = selection.getStartElement(),
        insertMode = false;

    if ( element ) element = element.getAscendant( 'a', true );

    if ( !element || element.getName() != 'a' || element.data( 'multimedia-name' ) == null) {
        element = editor.document.createElement( 'a' );
        var text = selection.getSelectedText();
        if (text != '') {
            element.setText(text);
        }
        insertMode = true;
    } else {
        $(multimediaVars.popupTarget + ' #multimediaLabel').val(element.getText());
    }

    $(multimediaVars.popupTarget + ' #multimediaFileName').val(element.getAttribute('data-multimedia-name'));
    $(multimediaVars.popupTarget + ' #multimediaLabel').val(element.getText());

    multimediaVars.element = element;
    multimediaVars.insertMode = insertMode;
}

function insertElement(editor, uploadData) {
    var element = multimediaVars.element;
    var insertMode = multimediaVars.insertMode;

    var fileName = $(multimediaVars.popupTarget + ' #multimediaFileName').val();
    var label = $(multimediaVars.popupTarget + ' #multimediaLabel').val();
    var path = base_url + uploadData.path + '/' + uploadData.basename;

    element.setAttribute('class', 'download-link');
    element.setAttribute('data-multimedia-name', fileName);
    element.setAttribute('href', path);
    element.setAttribute('data-cke-saved-href', path);
    element.setAttribute('download', 'download');
    element.setAttribute('target', '_blank');
    element.appendBogus(true);
    if (label != '') {
        element.setText(label);
    } else {
        element.setText(fileName);
    }

    if (insertMode) {
        editor.insertElement(element);
    } else {
        editor.updateElement(element);
    }
}

function multimediaRemoveDialog(editor) {
    var selection = editor.getSelection(),
        element = selection.getStartElement();

    if ( element || element.getName() == 'a' || element.data( 'data-multimedia-name' ) != null) {
        var text = element.getText();

        element.remove();
        editor.insertText(text);
    }
}

function ucwords(str) {
    return (str + '')
        .replace(/^([a-z\u00E0-\u00FC])|\s+([a-z\u00E0-\u00FC])/g, function($1) {
            return $1.toUpperCase();
        });
}