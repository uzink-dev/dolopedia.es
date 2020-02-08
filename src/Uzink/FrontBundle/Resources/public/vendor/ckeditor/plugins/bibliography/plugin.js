var bibliographyVars = new function() {
    var parent = this;
    this.popupTarget = '#bibliographyPopup';
    this.entryListID = '#entriesShow';
    this.$entryList = $(this.entryListID);
    this.entryCollectionID = '#entriesCollection';
    this.$entryCollection = $(this.entryCollectionID);


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
            bibliographyEntry.resetPopup();
            $(parent.popupTarget + ' *[data-popup-action="accept"]').unbind('click');
        },
        helpers: {
            overlay: {
                locked: false
            }
        }
    };
};

var bibliographyEntry = new function() {
    var collectionTarget = '#entriesCollection';
    var showTarget = '#entriesShow';

    this.uid = null;
    this.title = null;
    this.author = null;
    this.publication = null;
    this.volume = null;
    this.pages = null;
    this.year = null;
    this.link = null;
    this.position = null;

    // Handling PopUp
    // Gets popup's values and assigns them to the class properties
    this.getPopup = function() {
        for (i in this) {
            if (typeof this[i] != 'function') {
                this[i] = $(bibliographyVars.popupTarget + ' #bibliography' + ucwords(i)).val();
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
                $(bibliographyVars.popupTarget + ' #bibliography' + ucwords(i)).val(aux);
            }
        }
    }

    // Cleans popup's fields, all values are removed
    this.resetPopup = function() {
        this.setPopup(null);
    };

    // Handling Entries
    // Inserts the needed entries when one new entry is created
    this.addEntry = function() {
        var item = this.makeCollectionEntry();
        $(collectionTarget).append(item);
        this.fillCollectionEntry(this.uid);

        item = this.makeShowEntry();
        $(showTarget).append(item);
    };

    // Updates the needed entries when one entry is modified
    this.updateEntry = function(uid) {
        this.fillCollectionEntry(uid);
        this.updateShowEntry(uid);
    };

    // Removes the needed entries when one entry is removed
    // ---------------------------------------------------------------------------------
    // Only removes the visible entry, the collection entry will removed by Symfony when
    // it detect that is an orphan entry
    this.removeEntry = function(uid) {
       $(showTarget + ' *[data-bibliography-uid="' + uid + '"]').remove();
    };

    // Fills the collection's entry with the class's properties values
    this.fillCollectionEntry = function(uid) {
        var itemId = $(collectionTarget + ' *[data-bibliography-uid="' + uid + '"]').attr('id');
        for (i in this) {
            if (typeof this[i] != 'function') {
                $('#' + itemId + '_' + i).val(this[i]);
            }
        }
    };

    this.updateShowEntry = function(uid) {
        var showEntry = this.makeShowEntry();
        $(showTarget + ' *[data-bibliography-uid="' + uid + '"]').replaceWith(showEntry);
    };

    // Gets values from entries collection and fills the class properties with them
    this.getValuesFromEntry = function(uid) {
        var itemId = $(collectionTarget + ' *[data-bibliography-uid="' + uid + '"]').attr('id');
        for (i in this) {
            if (typeof this[i] != 'function') {
                this[i] = $('#' + itemId + '_' + i).val();
            }
        }
    };

    // Makes the collection entries's HTML code and returns it
    this.makeCollectionEntry = function() {
        var prototype = $(collectionTarget).data('prototype');
        var index = $(collectionTarget + ' *[data-prototype-index]').last().data('prototype-index') + 1;
        var item = prototype;
        item = item.replace(/__name__/g, index);
        item = item.replace(/__uid__/g, this.uid);
        item = item.replace(/__position__/g, this.position);

        return item;

    };

    this.makeShowEntry = function() {
        var item = $(showTarget).data('prototype');
        var regex;
        for (var i in this) {
            if (typeof this[i] != 'function') {
                regex = new RegExp('__' + i + '__', 'g');
                item = item.replace(regex, this[i]);
            }
        }

        if (!this.link) {
          var $item = $(item);
          $item.find('a[href=""]').contents().unwrap();
          item = $item.prop('outerHTML');
        }

        return item;
    };

    // Utils
    this.generateUid = function (separator) {
        var delim = separator || "-";

        function S4() {
            return (((1 + Math.random()) * 0x10000) | 0).toString(16).substring(1);
        }

        return (S4() + S4() + delim + S4() + delim + S4() + delim + S4() + delim + S4() + S4() + S4());
    };

    this.getNextPosition = function() {
        var lastPosition = 0;
        $(collectionTarget + ' *[data-bibliography-uid]').each(function() {
            var position = $(this).data('bibliography-position');
            if (position > lastPosition) lastPosition = position;
        });

        return lastPosition + 1;
    }
};

CKEDITOR.plugins.add( 'bibliography', {
    icons: 'bibliography',
    init: function( editor ) {
        editor.addCommand('bibliographyDialog', {
            exec: bibliographyDialog,
            allowedContent: 'a(reference-link)[!data-bibliography-uid,href,name]'
        });
        editor.addCommand('bibliographyDeleteDialog', {
            exec: bibliographyDeleteDialog,
            allowedContent: 'a(reference-link)[!data-bibliography-uid,href,name]'
        });
        editor.ui.addButton( 'Bibliography', {
            label: 'Bibliografía',
            command: 'bibliographyDialog'
        });
        if ( editor.contextMenu ) {
            editor.addMenuGroup( 'bGroup' );
            editor.addMenuItems({
                bItem : {
                    label : 'Editar entrada',
                    icon : this.path + 'icons/bibliography.png',
                    command : 'bibliographyDialog',
                    group : 'bGroup',
                    order : 1
                },
                bdItem: {
                    label : 'Eliminar entrada',
                    icon : this.path + 'icons/bibliography.png',
                    command : 'bibliographyDeleteDialog',
                    group : 'bGroup',
                    order : 2
                }
            });

            editor.contextMenu.addListener( function( element ) {
                var item =  element.getAscendant( 'a', true );
                if ( item && element.data( 'bibliography-uid' ) != null ) {
                    return {
                        bItem: CKEDITOR.TRISTATE_OFF,
                        bdItem: CKEDITOR.TRISTATE_OFF
                    };
                }
            });
        }

        // Handle copy/paste bibliographic entries
        editor.on('paste', function(event) {
            if (event.name == 'paste') {
              var $pastedContent = $('<div>').append(event.data.dataValue),
                  bEntries = $('.reference-link', $pastedContent);

              $('*', $pastedContent).removeAttr('style');

              if (bEntries.length == 0) return;

              var bEntriesImported = {},
                  nextPosition = bibliographyEntry.getNextPosition();
              $('.reference-link', $pastedContent).each(function(index, entry) {
                var $entry = $(entry);

                var bEntry = bibliographyVars.$entryList.find('.reference-link-item[data-bibliography-uid="' + $entry.data('bibliography-uid') + '"]');
                if (bEntry.length == 0) {
                  bEntriesImported[$entry.data('bibliography-uid')] = {
                    originalUID: $entry.data('bibliography-uid'),
                    newUID: bibliographyEntry.generateUid(),
                    position: nextPosition,
                    hydrated: false
                  };
                  nextPosition++;
                }
              });

              if (Object.keys(bEntriesImported).length == 0) return;

              var spinner = new Spinner().spin(event.editor.container.$), results = [];
              $(event.editor.container.$).addClass('loading');

              function fetchEntryData (uid) {
                return $.ajax({
                  type: 'GET',
                  url: bibliographyVars.$entryCollection.data('entries-url').replace('__uid__', uid),
                  dataType: 'json',
                  success: function (xml) {}
                });
              }

              $.each(bEntriesImported, function (index, entry) {
                results.push(fetchEntryData(entry.originalUID));
              });

              $.when.apply(this, results).done(function () {
                var bEntriesData = [], requests = arguments;

                if (arguments.length == 0) return;
                if (arguments[0].hasOwnProperty('title')) requests = [arguments];

                $.each(requests, function (index, request) {
                  var data = request[0];

                  bibliographyEntry.uid = bEntriesImported[data.uid].newUID;
                  bibliographyEntry.position = bEntriesImported[data.uid].position;
                  bibliographyEntry.title = data.title;
                  bibliographyEntry.author = data.author;
                  bibliographyEntry.publication = data.publication;
                  bibliographyEntry.pages = data.pages;
                  bibliographyEntry.volume = data.volume;
                  bibliographyEntry.year = data.year;
                  bibliographyEntry.link = data.link;
                  bibliographyEntry.addEntry();

                  $pastedContent
                    .find('a[data-bibliography-uid="' + bEntriesImported[data.uid].originalUID + '"]')
                    .text(bEntriesImported[data.uid].position)
                    .attr('data-bibliography-uid', bEntriesImported[data.uid].newUID)
                    .attr('data-cke-saved-href', null)
                    .attr('data-cke-saved-name', null)
                    .prop('href', '#' + bEntriesImported[data.uid].newUID)
                    .prop('name', 'entry-' + bEntriesImported[data.uid].newUID);
                });

                event.editor.insertHtml($pastedContent.html());
                $(event.editor.container.$).removeClass('loading');
                spinner.stop();
              });

              return false;
            }
        }, editor.element.$);
    }
});

function bibliographyDialog(editor) {
    var selection = editor.getSelection(),
        element = selection.getStartElement(),
        insertMode = false;

    if ( element ) element = element.getAscendant( 'a', true );

    var link = editor.document.createElement( 'a' );
    if ( !element || element.getName() != 'a' || element.data( 'bibliography-uid' ) == null) {
        insertMode = true;
    } else {
        var uid = element.data( 'bibliography-uid' );
        link.setText(element.getText());
        bibliographyEntry.getValuesFromEntry(uid);
        bibliographyEntry.setPopup();
    }

    $.fancybox(bibliographyVars.fancyboxOptions);
    $(bibliographyVars.popupTarget + ' *[data-popup-action="accept"]').click(function() {
        bibliographyEntry.getPopup();

        if (bibliographyEntry.uid == '') bibliographyEntry.uid = bibliographyEntry.generateUid();
        if (bibliographyEntry.position == '') bibliographyEntry.position = bibliographyEntry.getNextPosition();

        link.setAttribute('class', 'reference-link');
        link.setAttribute('data-bibliography-uid', bibliographyEntry.uid);
        link.setAttribute('href', '#' + bibliographyEntry.uid);
        link.setAttribute('name', 'entry-' + bibliographyEntry.uid);

        if (insertMode) {
            link.setText(bibliographyEntry.position);
            editor.insertElement(link);
            bibliographyEntry.addEntry();
        } else {
            editor.updateElement(link);
            bibliographyEntry.updateEntry(bibliographyEntry.uid);
        }

        $.fancybox.close();
    });
}

function bibliographyDeleteDialog(editor) {
    var selection = editor.getSelection(),
        element = selection.getStartElement();

    if ( element || element.getName() == 'a' || element.data( 'bibliography-uid' ) != null) {
        var uid = element.data( 'bibliography-uid' );
        bibliographyEntry.removeEntry(uid);
        element.remove();
    }
}

function ucwords(str) {
    return (str + '')
        .replace(/^([a-z\u00E0-\u00FC])|\s+([a-z\u00E0-\u00FC])/g, function($1) {
            return $1.toUpperCase();
        });
}