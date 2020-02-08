var ilVars = new function() {
    var parent = this;
    this.popupTarget = '#addInnerLinksPopup',
    this.fancyboxOptions = {
        'type'        : 'inline',
        'padding'     : 0,
        'margin'      : 10,
        'maxWidth'    : 620,
        'minWidth'    : 280,
        'width'       : 620,
        'height'      : 'auto',
        'autoHeight'  : true,
        'fitToView'   : true,
        'autoSize'    : true,
        'autoCenter'  : false,
        'modal'       : false,
        'href' 	    : parent.popupTarget,
        'afterClose'    : function() {
            resetBranchs(); 
            $(parent.popupTarget + ' *[data-popup-action="accept"]').unbind('click');
        },
        helpers: {
            overlay: {
                locked: false
            }
        }
    }
};

CKEDITOR.plugins.add( 'internallinks', {
    icons: 'internallinks',
    init: function( editor ) {
        editor.addCommand('internallinksDialog', {
            exec: internalLinkDialog,
            allowedContent: 'a(internal-link)[!data-internallinks-id, !data-internallinks-type]'
        });
        editor.addCommand('internallinksRemoveDialog', {
            exec: internallinksRemoveDialog,
            allowedContent: 'a(internal-link)[!data-internallinks-id, !data-internallinks-type]'
        });
        editor.ui.addButton( 'Internallinks', {
            label: 'Vínculo Interno',
            command: 'internallinksDialog'
        });
        if ( editor.contextMenu ) {
            editor.addMenuGroup( 'ilGroup' );
            editor.addMenuItem( 'ilItem', {
                label: 'Editar vínculo interno',
                icon: this.path + 'icons/internallinks.png',
                command: 'internallinksDialog',
                group: 'ilGroup'
            });
            editor.addMenuItem( 'ilrItem', {
                label: 'Eliminar vínculo interno',
                icon: this.path + 'icons/internallinks.png',
                command: 'internallinksRemoveDialog',
                group: 'ilGroup'
            });

            editor.contextMenu.addListener( function( element ) {
                var item =  element.getAscendant( 'a', true );
                if ( item && item.data( 'internallinks-id' ) != null && item.data( 'internallinks-type' ) != null) {
                    return {
                        ilItem: CKEDITOR.TRISTATE_OFF,
                        ilrItem: CKEDITOR.TRISTATE_OFF
                    };
                }
            });            
        }
    }
});

function internalLinkDialog(editor) {
    TreeViewMenu.init();
    var selection = editor.getSelection(),
        element = selection.getStartElement(),
        insertMode = false;

    if ( element )
        element = element.getAscendant( 'a', true );
    if ( !element || element.getName() != 'a' || element.data( 'internallinks-id' ) == null || element.data( 'internallinks-type' ) == null) {
        element = editor.document.createElement( 'a' );
        insertMode = true;
    } else {
        setBranch(element.data( 'internallinks-id' ), element.data( 'internallinks-type' ));
    }

    $.fancybox(ilVars.fancyboxOptions);

    $(ilVars.popupTarget + ' *[data-popup-action="accept"]').click(function() {
        var categoryUri = $(ilVars.popupTarget).data('internal-link-uri-category');
        var articleUri = $(ilVars.popupTarget).data('internal-link-uri-article');
        $(ilVars.popupTarget +' .treeView-branch.selected').each(function() {
            var type = $(this).find('.treeView-check-selected').data('internal-link-type');
            var id = $(this).find('.treeView-check-selected').data('internal-link-id');
            var title = $(this).find('.treeView-check-selected').data('internal-link-title');
            var uri = null;
            if (type == 'category') {
                uri = categoryUri;
            } else {
                uri = articleUri;
            }

            uri.replace('_slug_', id);

            element.setAttribute('data-internallinks-id', id);
            element.setAttribute('data-internallinks-type', type);
            element.setAttribute('class', 'internal-link');
            element.appendBogus(true);
            element.setText(title);

            if (insertMode) {
                var text = selection.getSelectedText();
                if (text != '') {
                    element.setText(text);
                }
                editor.insertElement(element);
            } else {
                editor.updateElement(element);
            }

        });
        $.fancybox.close();
    });
}

function internallinksRemoveDialog(editor) {
    var selection = editor.getSelection(),
        element = selection.getStartElement();

    if ( element || element.getName() == 'a' || element.data( 'internallinks-id' ) != null || element.data( 'internallinks-type' ) != null) {
        var text = element.getText();

        element.remove();
        editor.insertText(text);
    }
}

var resetBranchs = function() {
    $(ilVars.popupTarget +' .treeView-branch').not('#treeView-main').each(function() {
        if ($(this).hasClass('selected')) {
            toggleBranch($(this));
        }
        var sCollapsable = $(this).find('.collapsable').first();
        if (isCollapsed(sCollapsable) == false) {
            toogleCollapse(sCollapsable);
        } 		
    });
}

var setBranch = function(id, type) {
    var branches = $(ilVars.popupTarget +' .treeView-branch');
    branches.each(function() {
        var branch = $(this);
        var parentBranch = branch.parents('.treeView-branch');
        var check = $(this).find('.treeView-check-selected');
        if ($(check).data('internal-link-id') == id && $(check).data('internal-link-type') == type) {
            toggleBranch($(this));
            parentBranch = branch.parents('li').not(':first').first();
            while (parentBranch.length > 0) {
                var sCollapsable = parentBranch.find('.collapsable').first();
                if (isCollapsed(sCollapsable) == true) {
                        toogleCollapse(sCollapsable);
                } 				
                parentBranch = parentBranch.parents('li').first();
            }
        }
    }); 
    $.fancybox.update()
}

var toggleBranch = function(branch) {
    $(branch).toggleClass('selected');
    var check = $(branch).find('.treeView-check-selected');
        if ($(branch).hasClass('selected')) {
            check.prop('checked', true);
        } else {
            check.prop('checked', false);
        }
}