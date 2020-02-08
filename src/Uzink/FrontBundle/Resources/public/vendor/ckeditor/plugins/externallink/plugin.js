var externallinkVars = new function() {
    var parent = this;
    var editor;
    var insertMode;
    var spinner;
    this.popupTarget = '#addExternalLinksPopup';

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
            $(parent.popupTarget + ' *[data-popup-action="accept"]').unbind('click');
            externallinkEntry.resetPopup();
            externallinkEntry.reset();
        },
        helpers: {
            overlay: {
                locked: false
            }
        }
    };
};

var externallinkEntry = new function() {
    this.url = null;
    this.content = null;
    this.target = null;

    // Handling PopUp
    // Gets popup's values and assigns them to the class properties
    this.getPopup = function() {
        for (var i in this) {
            if (typeof this[i] != 'function') {
                this[i] = $(externallinkVars.popupTarget + ' #externalLinks' + ucwords(i)).val();
            }
        }
    }

    // Gets values from the class properties and fills the popup with them
    this.setPopup = function(value) {
        var aux;

        for (i in this) {
            if (typeof this[i] != 'function') {
                if (typeof value === 'undefined') aux = this[i];
                else aux = value;
                $(externallinkVars.popupTarget + ' #externalLinks' + ucwords(i)).val(aux);
            }
        }
    }

    // Cleans popup's fields, all values are removed
    this.resetPopup = function() {
        this.setPopup(null);
    }

    this.reset = function() {
        this['url'] = null;
        this['target'] = null;
        this['content'] = null;
    }

    // Gets values from entries collection and fills the class properties with them
    this.getValuesFromElement = function(element) {
        this['url'] = element.data( 'externallink-href' );
        this['target'] = element.getAttribute('target');
        this['content'] = element.getText();
    }

    function ucwords(str) {
        return (str + '')
            .replace(/^([a-z\u00E0-\u00FC])|\s+([a-z\u00E0-\u00FC])/g, function($1) {
                return $1.toUpperCase();
            });
    }
}

CKEDITOR.plugins.add( 'externallink', {
    icons: 'externallink',
    init: function( editor ) {
        editor.addCommand('externallinkDialog', {
            exec: externallinkDialog,
            allowedContent: 'a(external-link)[!data-externallink-href,href,target]'
        });
        editor.addCommand('externallinkDialogRemove', {
            exec: externallinkDialogRemove,
            allowedContent: 'a(external-link)[!data-externallink-href,href,target]'
        });
        editor.ui.addButton( 'Externallink', {
            label: 'Añadir enlace externo',
            command: 'externallinkDialog'
        });
        if ( editor.contextMenu ) {
            editor.addMenuGroup( 'elGroup' );
            editor.addMenuItem( 'elItem', {
                label: 'Editar enlace externo',
                icon: this.path + 'icons/externallink.png',
                command: 'externallinkDialog',
                group: 'elGroup'
            });
            editor.addMenuItem( 'elrItem', {
                label: 'Eliminar enlace externo',
                icon: this.path + 'icons/externallink.png',
                command: 'externallinkDialogRemove',
                group: 'elGroup'
            });

            editor.contextMenu.addListener( function( element ) {
                var item =  element.getAscendant( 'a', true );
                if ( item && element.data( 'externallink-href' ) != null ) {
                    return {
                        elItem: CKEDITOR.TRISTATE_OFF,
                        elrItem: CKEDITOR.TRISTATE_OFF
                    };
                }
            });
        }
    }
});

function externallinkDialog(editor) {
    var selection = editor.getSelection(),
        element = selection.getStartElement(),
        insertMode = false;

    if ( element ) element = element.getAscendant( 'a', true );

    if ( !element || element.getName() != 'a' || element.data( 'externallink-href' ) == null) {
        insertMode = true;
        element = editor.document.createElement( 'a' );
        externallinkEntry.content = selection.getSelectedText();
    } else {
        externallinkEntry.getValuesFromElement(element);
    }

    externallinkEntry.setPopup();

    $.fancybox(externallinkVars.fancyboxOptions);
    $(externallinkVars.popupTarget + ' *[data-popup-action="accept"]').click(function() {
        externallinkEntry.getPopup();

        if (externallinkEntry.content) element.setText(externallinkEntry.content);
        else element.setText(externallinkEntry.url);
        element.setAttribute('class', 'external-link');
        element.setAttribute('data-externallink-href', externallinkEntry.url);
        element.setAttribute('href', externallinkEntry.url);
        element.setAttribute('data-cke-saved-href', externallinkEntry.url);
        element.setAttribute('target', externallinkEntry.target);

        if (insertMode) {
            editor.insertElement(element);
        } else {
            editor.updateElement(element);
        }

        $.fancybox.close();
    });
}

function externallinkDialogRemove(editor) {
    var selection = editor.getSelection(),
        element = selection.getStartElement();

    if ( element || element.getName() == 'a' || element.data( 'externallink-href' ) != null) {
        var text = element.getText();

        element.remove();
        editor.insertText(text);
    }
}
