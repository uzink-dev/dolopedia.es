$(window).resize(function(){
    setFixedElements();
    setDotDotDot();
});

$(window).load(function(){
    var body_h = $('body').height();
    var window_h = $(window).height();

//    if(body_h < window_h){
//        $('footer').css('margin-top',window_h - body_h);
//    }
});

$(function() {
    initializeArticleSearcher();
    setStars();
    setFixedElements();
    setDotDotDot();
    setFancyBox();
    setLaunchers();
    ActivityLoader.init();
    TreeViewMenu.init();
    AjaxFormHandler.init();
    Filtering.init();
    setCollapsables();
    setTreeActions();
    setAjaxContentLoader();
    initializeFormControls();
    initTabs();
    deleteConfirmation();
    initizalizeFilters();

    $('*[data-toggle="tooltip"]').tooltip();

    $('.style-select').selectBox();

    $('.select2-control').select2();

    $('*[data-toggle="team-search"]').on('change textInput input', function() {
      var $input = $(this);

      if ($input.val().length > 2) {
        $('.treeView').unmark({
          done: function() {
            $('.treeView').mark($input.val(), {
              done: function(counter) {
                if (counter) {
                  $('.treeView').find('mark').each(function() {
                    var $element = $(this),
                        $parentElement = $($element.parent().closest('li:not(.treeView-article)'));

                    if ($element.closest('div[data-tree-type="category"]').length)
                      $parentElement = $($parentElement.parent().closest('li:not(.treeView-article)'));

                    while($parentElement.length > 0) {
                      if ($parentElement.find('ul.collapsed').length) toogleCollapse($parentElement.find('.collapsable'));
                      $parentElement = $($parentElement.parent().closest('li:not(.treeView-article)'));
                    }
                  });
                }
              }
            });
          }
        });
      } else {
        $('.treeView').unmark();
      }
    });

    if($('.datetimepicker').length > 0){
        $('.datetimepicker').datetimepicker({
            language: 'es-ES',
            weekStart: 1
        });
    }

    $('.hidde-left-bar').click(function(e) {
        e.preventDefault();
        var leftcolumn = $('.left-column');
        if($(leftcolumn).is(":visible")){
            $(leftcolumn).hide();
            $('.main-content').css('display','block');
            $('.main-content').css('margin-left','-15px');
            $('.main-content').css('width','auto');
        }else{
            $(leftcolumn).show();
            $('.main-content').css('display','table-cell');
            $('.main-content').css('margin-left',0);
            $('.main-content').css('width','100%');
        }
    });

    /* Open and hide */
    $('.show-and-hide').click(function(e){
        e.preventDefault();
        var href = $(this).attr('href');
        var hide = $(this).data('hide');
        $(hide).hide();
        $(href).show();
    });

    /* Show actions popups */
    $('*[data-popup]').click(function(e){
         e.preventDefault();
        var popupId = $(this).data('popup');

        $.fancybox.open({
            href:popupId,
            padding:0,
            fitToView	: false,
            autoSize	: true,
            closeClick	: false,
            openEffect	: 'none',
            closeEffect	: 'none',
            helpers: {
                overlay: {
                    locked: false
                }
            }
        });
    });


    $('select[data-sort-select]').change(function () {
        var arr_val = $(this).val().split(' ');

        if (!arr_val || (arr_val.constructor !== Array && arr_val.constructor !== Object)) {
            return false;
        } else {
            if ((0 in arr_val) && (1 in arr_val)){
                $('[name="sort"]').val(arr_val[0]);
                $('[name="direction"]').val(arr_val[1]);
            }else{
                $('[name="sort"]').val('');
                $('[name="direction"]').val('');
            }
        }

        $(this).closest('form').attr('action', document.URL.split('?')[0])
        $(this).closest('form').submit();
    });
});

function deleteConfirmation()
{
    $('*[data-delete-confirmation]').each(function() {
        $(this).click(function(event) {
            event.preventDefault();
            var url = $(this).data('delete-confirmation');
            var title = $('#popupDeleteConfirmationData #popupTitle').html();
            var message = $(this).data('delete-confirmation-message');;
            var okButton = $('#popupDeleteConfirmationData #popupOkButton').html();
            var cancelButton = $('#popupDeleteConfirmationData #popupCancelButton').html();
            var onOk = function() {
                window.location.href = url;
            }

            alertify.confirm(title, message, onOk, null)
                    .setting('closable', false)
                    .setting('labels', {ok: okButton, cancel: cancelButton});
        });
    });
}

function initTabs()
{
    $('.nav-tabs a').click(function (e) {
        e.preventDefault();
        $(this).tab('show');
    });

    // Adding tab persistence
    $('.nav-tabs > li > a').each(function () {
        var identifier = $(this).attr('href');
        $(identifier).find('.paginator-link').each(function() {
             var url = $(this).attr('href');
             $(this).attr('href', url + identifier);
        });
    });

    // Store the currently selected tab in the hash value
    $('.nav-tabs > li > a').on('shown.bs.tab', function (e) {
        var identifier = $(e.target).attr("href").substr(1);
        if(history.pushState) {
            history.pushState(null, null, '#' + identifier);
        } else {
            window.location.hash = identifier;
        }
    });

    // on load of the page: switch to the currently selected tab
    var hash = window.location.hash;
    var tab = getParameterByName('tab');
    if (hash != '') $('a[href="' + hash + '"]').tab('show');
    else if (tab != '') $('a[href="#' + tab + '"]').tab('show');
}

function getParameterByName(name) {
    name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
    var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
    results = regex.exec(location.search);
    return results == null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
}

function initializeArticleSearcher() {
    $('#articleSearch').submit(function (event){
        event.preventDefault();
        var engine = $('#searchEngine').find('option:selected');
        var engineUrl =  engine.data('search-url');
        var engineTarget =  engine.data('search-target');
        var keyword = $('#searchedKeyword').val();
        if (keyword != '') {
            var performedUrl = engineUrl.replace('_keyword_', keyword);
            window.open(performedUrl, engineTarget);
        }
    });

    $('.tree-search').each(function() {
      var dropdownButton = $(this).find('.dropdown-toggle'),
          typeSelect = $(this).find('#type');
      $('.dropdown-menu a').click(function() {
        var value = $(this).data('value'),
            label = $(this).text();
        dropdownButton.find('.text').text(label);
        typeSelect.val(value);
      });
    });
}

function setFixedElements(){
    if($('.index-box').length>0 || $('.goes-to-bottom').length>0){
        if(window.innerWidth <= 990){
            $('.goes-to-bottom').trigger('detach.ScrollToFixed');
            $('.index-box').trigger('detach.ScrollToFixed');
            $('.goes-to-bottom').scrollToFixed({
                bottom: 0,
                limit: $('.columns-container').offset().top+$('.columns-container').height()-25,
                dontSetWidth: true,
                offsets: false,
                preFixed: function(){
                    $(this).find('.right-tabs').css('margin-left',0);
                    $(this).find('.right-tabs').css('margin-right',0);
                },
                postFixed : function(){
                    $(this).find('.right-tabs').css('margin-left','-15px');
                    $(this).find('.right-tabs').css('margin-right','-15px');
                }
            });
        }else{
            $('.goes-to-bottom').trigger('detach.ScrollToFixed');
            $('.index-box').trigger('detach.ScrollToFixed');
            $('.index-box').scrollToFixed({
                marginTop: 20,
                dontSetWidth: true,
                limit: $('#article-body').offset().top +  $('#article-body').outerHeight(false) - $('.index-box').outerHeight(true)-20,
                preFixed : function(){
                    $(this).removeClass('top-auto');
                },
                postFixed : function(){
                    $(this).addClass('top-auto');
                }
            });
        }
    }
}

function setDotDotDot(){
    $('.link-card-info-content').dotdotdot();
    $('.jEllipsis').dotdotdot();
}

//<editor-fold desc="FancyBox">
function setFancyBox() {
    if($('.popupLauncher').length>0){
        $(".popupLauncher").fancybox({
            type        : 'inline',
            padding     : 0,
            margin      : 10,
            width	    : '100%',
            maxWidth    : 620,
            minWidth    : 280,
            height      : 'auto',
            fitToView	: false,
            autoSize	: false,
            modal       : true
        });
    }
    if($('.alertLauncher').length > 0){
        $(".alertLauncher").fancybox({
            type        : 'inline',
            padding     : 0,
            margin      : 0,
            width	    : '100%',
            maxWidth    : 520,
            minWidth    : 280,
            height      : 'auto',
            fitToView	: false,
            autoSize	: false,
            modal       : true
        });
    }
    if ($('.confirmation-popup').length > 0) {
        $('.confirmation-popup').fancybox({
            type        : 'inline',
            padding     : 0,
            margin      : 0,
            width	    : '100%',
            maxWidth    : 520,
            minWidth    : 280,
            height      : 'auto',
            fitToView	: false,
            autoSize	: false,
            modal       : true,
            beforeLoad  : function() {
                var popupId = $(this.element).attr('href');
                var removeUrl = $(this.element).data('confirmation-popup-url');
                $(popupId).find('#removeConfirmationAction').attr('href', removeUrl);
            },
            afterClose  : function() {
                var popupId = $(this.element).attr('href');
                $(popupId).find('#removeConfirmationAction').attr('href', '#');
            }
        });
    }
}

function openFancyBox(hrefPopup){
    return $.fancybox({
        type        : 'inline',
        padding     : 0,
        margin      : 10,
        width	    : '100%',
        maxWidth    : 620,
        minWidth    : 280,
        height      : 'auto',
        fitToView	: false,
        autoSize	: false,
        modal       : true,
        href 		: hrefPopup
    });
}
//</editor-fold>

//<editor-fold desc="Launchers">
function setLaunchers() {
    $('*[data-extended-info]').change(function() {
        var value = $(this).val();
        var id = $(this).data('extended-info');
        if (value == $(this).data('extended-on')) {
            $('#' + id).show();
        } else {
            $('#' + id).hide();
            $('#' + id).find('input').val("");
            $('#' + id).find('select').val("");
        }
    });
    $('*[data-extended-value]').change(function() {
        var value = $(this).val();
        var id = '#' + $(this).data('extended-value');
        var extended = $(id);
        if (value != '') {
            extended.find('.extended-value').each(function() {
                $(this).hide();
            });
            extended.find('#' + value).show();
            extended.show();
        } else {
            extended.hide();
        }
    });

    $('*[data-extended-info]').change();

    $('*[data-action-favourite]').click(function(event) {
        event.preventDefault();
        var favouriteUrl = $(this).data('action-favourite');
        var element = $(this);
        var icon = $(this).find('span');
        var iconClass = icon.attr('class');
        icon.removeClass(iconClass);
        icon.addClass('spinner icon');
        var spinner = setSpinner(icon, 'icon');
        $.post(
            favouriteUrl,
            null,
            function(data) {
                if (data) {
                    element.addClass('btn-gold');
                    element.removeClass('btn-light');
                } else {
                    element.removeClass('btn-gold');
                    element.addClass('btn-light');
                }
            }
        )
        .always(function () {
            spinner.stop();
            icon.removeClass('spinner icon');
            icon.addClass(iconClass);
        });
    });

    var handleFollowToggle = function(element) {
        var item = element.closest('.titled-image');
        var url = element.data('action-toggle-follow');
        var deleteItem = element.data('follow-delete-item');
        var following = element.data('action-following');
        var icon = element.find('span');
        var iconClass = icon.attr('class');
        var spinner = null;

        var setIconSpinner = function() {
            icon.css('width', icon.width());
            icon.css('height', icon.height());
            icon.removeClass(iconClass);
            icon.addClass('spinner');
            spinner = setSpinner(icon, 'icon');
        }

        var toggleFollow = function() {
            setIconSpinner();
            $.get(
                url,
                null,
                function(data) {
                    if (data.status) {
                        if (!deleteItem) {
                            var newItem = $(data.html);
                            item.replaceWith(newItem);
                            newItem.find('*[data-toggle]').tooltip();
                            newItem.find('*[data-action-toggle-follow]').click(function() {
                                handleFollowToggle($(this))
                            });
                        } else {
                            item.closest('.user-box').remove();
                        }
                    }
                })
                .always(function () {
                    spinner.stop();
                });
        }

        if (following) {
            showDeleteConfirmation(element, toggleFollow);
        } else {
            toggleFollow();
        }
    }

    $('*[data-action-toggle-follow]').click(function(event) {
        event.preventDefault();
        handleFollowToggle($(this));
    });

    $('*[data-delete-confirmation-callback]').click(function(event) {
        event.preventDefault();
        var url = $(this).data('delete-confirmation-callback');
        var urlCallback = function() {
            window.location.href = url
        }
        showDeleteConfirmation($(this), urlCallback);
    });

    var showDeleteConfirmation = function(element, callback) {
        var title = $('#popupDeleteConfirmationData #popupTitle').html();
        var message = element.data('delete-confirmation-message');;
        var okButton = $('#popupDeleteConfirmationData #popupOkButton').html();
        var cancelButton = $('#popupDeleteConfirmationData #popupCancelButton').html();

        alertify.confirm(title, message, callback, null)
            .setting('closable', false)
            .setting('labels', {ok: okButton, cancel: cancelButton});
    }
}


//</editor-fold>

//<editor-fold desc="Filtering Handling">
function Filtering () { }

Filtering.filters = [];

Filtering.init = function () {
    $('*[data-filter-field]').each(function () {
        var field = $(this).data('filter-field');
        Filtering.initElement($(this), field);
    });
}

Filtering.initElement = function (element, field) {
    var elementType = element.prop('tagName');
    switch(elementType) {
        case 'UL':
            element.find('a').each(function() {
                var linkUri = $(this).attr('href'),
                    filterValue = Filtering.getParameterByName(linkUri, field);
                $(this).click(function(event) {
                    event.preventDefault();
                    Filtering.updateUrl(field, filterValue);
                })
            });
            break;

        case 'SELECT':
        case 'INPUT':
            element.change(function (event) {
                Filtering.updateUrl(field, element.val());
            });
            break;

        default:
            break;
    }
}

Filtering.updateUrl = function(field, value) {
    var uri = window.location.href;
    uri = Filtering.updateQueryStringParameter(uri, field, value);
    window.open(uri, "_self");
}

Filtering.updateQueryStringParameter = function(uri, key, value) {
    var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
    var separator = uri.indexOf('?') !== -1 ? "&" : "?";
    if (uri.match(re)) {
        return uri.replace(re, '$1' + key + "=" + value + '$2');
    }
    else {
        var hash = '',
            chunks = uri.split('#');

        uri = chunks[0];
        if (chunks.length > 1) hash = '#' + chunks[1];
        return uri + separator + key + "=" + value + hash;
    }
}

Filtering.getParameterByName = function (uri, name) {
    name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
    var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
        results = regex.exec(uri);
    return results == null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
}
//</editor-fold>

//<editor-fold desc="Tree Handling (Categories & Articles)">

function HashHandler () {}

HashHandler.items = function() {
    var cleanHash = window.location.hash.replace('#', '');
    if (cleanHash == '') return [];
    else return cleanHash.split('_');
};

HashHandler.update = function(items) {
    var cleanHash = items.join('_');
    if (items.length == 0) window.location.hash = '';
    else window.location.hash = '#' + cleanHash;
    return false;
};

HashHandler.add = function(id) {
    if (id != null) {
        var items = HashHandler.items(),
          newItem = id,
          existsInHash = false;

        items.some(function(element, index, array) {
            if (newItem == element) {
                existsInHash = true;
                return true;
            }
        });

        if(!existsInHash) {
            items.push(newItem);
            HashHandler.update(items);
        }
    }
};

HashHandler.remove = function(id) {
    if (id != null) {
        var items = HashHandler.items(),
          removedItem =  id;

        items.some(function(element, index, array) {
            if (removedItem == element) {
                items.splice(index, 1);
                return true;
            }
        });

        HashHandler.update(items);
    }
};

HashHandler.expandTree = function() {
    var items = HashHandler.items();
    items.forEach(function(current, index, array) {
        var item = $('*[data-tree-type="category"][data-tree-id="' + current + '"] .collapsable');
        toogleCollapse(item);
    });
};

function TreeViewMenu () { }

TreeViewMenu.init = function () {
    // Actions related element type
    var actions = {
        'root' : ['newCategory'],
        'category' : ['show', 'edit', 'newCategory', 'newArticle', 'delete'],
        'article' : ['show', 'edit', 'delete']
    };

    var item = {
        'type' : null,
        'id' : null,
        'slug' : null
    }

    $('.menuLauncher').click(function() {
        var branch = $(this).closest('.treeView-branch');
        branch.addClass('selected');
        $('.treeView-menu').data('id', branch.attr('id'));
        item['type'] = branch.data('tree-type');
        item['id'] = branch.data('tree-id');
        item['slug'] = branch.data('tree-slug');
        initTreeViewMenu(actions[item['type']]);
        TreeViewMenu.refreshPositionButtons(branch);

        $('.treeView-menu-overlay').show();
        $('.treeView-menu-container').slideDown({direction: "up"}, 3000);
    })
    $('.treeView-menu-overlay').click(function() { TreeViewMenu.close(); });
    $('.treeView-check-selected').unbind();
    $('.treeView-check-selected').click(function(event) {
        toogleBranchSelected($(this));
    })
    $('.popup .treeView-text').click(function() {
        var inputDiv = $(this).next('.treeView-check');
        var input = inputDiv.find('.treeView-check-selected');
        input.click();
    })

    var initTreeViewMenu = function(actions) {
        $(window).resize(function () {
            if(this.resizeTO) clearTimeout(this.resizeTO);
            this.resizeTO = setTimeout(function() {
                $(this).trigger('resizeEnd');
            }, 500);
        });
        $(window).bind('resizeEnd', function() {
            if ($(window).width() > 1024) TreeViewMenu.close();
        });
        $('.treeView-menu .treeView-menu-action').each(function () {
            var action = $(this).data('tree-action');
            var performedUrl = null;
            if (actions.indexOf(action) == -1) {
                $(this).addClass('hidden');
            } else {
                performedUrl = getPerformedUrl($(this).data(item['type'] + '-url'));
                if (action == 'delete') {
                    $(this).data('tree-action-url', performedUrl);
                    var branchId = $('.treeView-menu').data('id');
                    $(this).data('tree-branch', branchId);
                } else {
                    $(this).attr('href', performedUrl);
                }
            }
            if ((action == 'up' || action == 'down') && item['type'] != 'root') {
                performedUrl = getPerformedUrl($(this).data(item['type'] + '-url'));
                $(this).data('tree-position-url', performedUrl);
                var branchId = $('.treeView-menu').data('id');
                $(this).data('tree-branch', branchId);
            }
        })
    }

    var getPerformedUrl = function(url) {
        var performedUrl = url;
        var identifier = url.match("__(.*)__");
        if (identifier != null) {
            identifier = identifier[1];
            var resource = item[identifier];
            performedUrl = url.replace('__' + identifier + '__', resource);
        }

        return performedUrl;
    }

    HashHandler.expandTree();
};

TreeViewMenu.close = function () {
    $('.treeView-menu-overlay').hide();
    $('.treeView-menu-container').slideUp({direction: "down", complete: function() {TreeViewMenu.reset();}}, 3000);
    var id = '#' + $('.treeView-menu').data('id');
    $('.treeView-menu').removeData('id');
    $('.treeView-menu').removeData('data-item-id');
    $('.treeView-menu').removeData('data-item-slug');
    $('.treeView-menu').removeData('data-item-type');
    $(id).removeClass('selected');
}

TreeViewMenu.reset = function () {
    $('.treeView-menu .treeView-menu-action').each(function () {
        $(this).removeClass('hidden');
    })
}

TreeViewMenu.refreshPositionButtons = function(branch) {
    var upButton = branch.data('tree-postion-up');
    var downButton = branch.data('tree-postion-down');

    if (upButton) {
        $('.treeView-menu .treeView-menu-action[data-tree-action=up]').removeClass('hidden');
    } else {
        $('.treeView-menu .treeView-menu-action[data-tree-action=up]').addClass('hidden');
    }
    if (downButton) {
        $('.treeView-menu .treeView-menu-action[data-tree-action=down]').removeClass('hidden');
    } else {
        $('.treeView-menu .treeView-menu-action[data-tree-action=down]').addClass('hidden');
    }
}
//</editor-fold>

//<editor-fold desc="Activity Loader">
function ActivityLoader () {}

ActivityLoader.init = function () {
    $('*[data-activity-loader]').each(function() {
        var showMore = $(this);
        var wrapperId = showMore.data('activity-loader');
        var wrapper = $(wrapperId);
        var url = showMore.data('activity-loader-url');
        showMore.click(function (event) {
            var page,
                urlSplit,
                performedUrl,
                spinnerWrapper,
                spinner;
            event.preventDefault();

            page = wrapper.data('activity-loader-page');
            page += 1;

            urlSplit = url.split('/');
            urlSplit[urlSplit.length - 1] = page;
            performedUrl = urlSplit.join('/');

            spinnerWrapper = $('#activitySpinner');
            spinner = setSpinner(spinnerWrapper, 'mini');
            spinnerWrapper.removeClass('hidden');

            $.get(
                performedUrl,
                null,
                function (data) {
                    $(data.html).hide().appendTo(wrapperId).fadeIn('slow');
                    if (data.last) {
                        showMore.addClass('hidden');
                    } else {
                        wrapper.data('activity-loader-page', page);
                    }
                }
            ).always(function() {
                spinnerWrapper.addClass('hidden');
                spinner.stop();
            });
        });
    })
};
//</editor-fold>

function toogleBranchSelected(me) {
    var root = me.closest('.treeView-main');
    var branches = root.find('.treeView-branch');
    branches.each(function() {
        if ($(this).hasClass('selected')) {
            $(this).removeClass('selected');
            var check = $(this).find('.treeView-check-selected');
            check.attr('checked', false);
        }
    });
    if (me[0].checked) me.closest('.treeView-branch').toggleClass('selected');
}

function setCollapsables() {
    $('.collapsable').click(function(event) {
        // Only one branch are deployed simultaneously
        // When a branch is deployed, the others close
        // collapseSiblings($(this));
        toogleCollapse($(this));
    });
}



function toogleCollapse($element) {
    var branch = $element.parent('.treeView-branch');
    var list = branch.next('ul');
    var launcherIcon = $element.children('.treeView-icon');
    list.toggleClass('collapsed');

    if ($element.closest('.popup').length == 0) {
        if (list.hasClass('collapsed')) HashHandler.remove( branch.data('tree-id'));
        else HashHandler.add(branch.data('tree-id'));
    }

    launcherIcon.toggleClass('icon-tree-collapse');
    launcherIcon.toggleClass('icon-tree-expand');
    launcherIcon.toggleClass('treeView-icon-collapsed');
    // $.fancybox.update()
};

function isCollapsed($element) {
    var branch = $element.parent('.treeView-branch');
    var list = branch.next('ul');

    var collapsed = list.hasClass('collapsed')
    return collapsed;
}

function collapseSiblings(collapsable) {
    var currentElement = collapsable.closest('li');
    var siblings = currentElement.siblings().not(currentElement);
    siblings.each(function() {
        var sCollapsable = $(this).find('.collapsable').first();
        if (isCollapsed(sCollapsable) == false) {
            toogleCollapse(sCollapsable);
        }
    });
}

function setTreeActions() {
    var runningAction = false;
    var spinner = {
        'main' : null,
        'overlay' : null
    };

    // Position Handling
    $('*[data-tree-position-modifier]').click(function() {
        if (runningAction == false) {
            runningAction = true;
            var uri = $(this).data('tree-position-url');
            var action = $(this).data('tree-position-modifier');
            var branchId = $(this).data('tree-branch');
            var branch = $('#' + branchId).closest('li');
            setSpinners(branch);
            $.get(uri, function() {
                if (action == 'up') {
                    var swapElement = branch.prev();
                    swapElements(swapElement, branch);
                } else {
                    var swapElement = branch.next();
                    swapElements(branch, swapElement);
                }

                refreshPositionButtons(branch);
                TreeViewMenu.refreshPositionButtons($('#' + branchId));
            }).always(function() {
                    stopSpinners();
                    runningAction = false;
                });
        }
    });

    // Delete Handling
    $('*[data-tree-action]').click(function(event) {
        if (runningAction == false) {
            var action = $(this).data('tree-action');
            var uri = $(this).data('tree-action-url');
            var branchId = $(this).data('tree-branch');
            var branch = $('#' + branchId).closest('li');
            if (action == 'delete') {
                event.preventDefault();
                elements = $(branch).find('li');
                if (elements.length == 0) {
                    var callback = function() {
                        runningAction = true;
                        setSpinners(branch);
                        $.get(uri, function() {
                            var branchClass = '.' + $(branch).attr("class");
                            var siblings = $(branch).siblings(branchClass);
                            $(branch).remove();
                            TreeViewMenu.close();
                            refreshPositionButtons(branch, siblings);
                        }).always(function() {
                                stopSpinners();
                                runningAction = false;
                            });
                    }
                    launchPrevent('#deleteWarning', callback);
                } else {
                    $('#deleteForbidden').modal();
                }
            }
        }
    });

    var setSpinners = function(branch) {
        spinner['main'] = setBranchSpinner(branch);
        spinner['overlay'] = setOverlaySpinner();
    }

    var stopSpinners = function() {
        spinner['main'].stop();
        var spinnerElement = $('.treeView-menu-overlay').find('.spinner').first();
        spinnerElement.addClass('hidden');
        spinner['overlay'].stop();
    }

    var setBranchSpinner = function(branch) {
        var spinnerElement = $(branch).find('.spinner').first();
        var spinner = setSpinner(spinnerElement, 'small');

        return spinner;
    }

    var setOverlaySpinner = function() {
        var spinnerElement = $('.treeView-menu-overlay').find('.spinner').first();
        spinnerElement.removeClass('hidden');
        var spinner = setSpinner(spinnerElement, 'small');

        return spinner;
    }

    var getSiblings = function(branch) {
        var branchClass = '.' + $(branch).attr("class");
        var parent = $(branch).parent();
        var siblings = parent.children(branchClass);

        return siblings;
    }

    var refreshPositionButtons = function(branch, siblings) {
        if(typeof siblings === "undefined") {
            siblings = getSiblings(branch);
        }

        if (siblings.length <= 1) {
            siblings.first().find('.position-actions').first().addClass('hidden');
        } else {
            siblings.first().find('.position-actions').first().removeClass('hidden');
        }

        siblings.first().find('[data-tree-position-modifier="up"]').first().addClass('hidden');
        siblings.first().next().find('[data-tree-position-modifier="up"]').first().removeClass('hidden');
        siblings.first().find('.treeView-branch').first().data('tree-postion-up', false);
        siblings.first().next().find('.treeView-branch').first().data('tree-postion-up', true);

        siblings.last().find('[data-tree-position-modifier="down"]').first().addClass('hidden');
        siblings.last().prev().find('[data-tree-position-modifier="down"]').first().removeClass('hidden');
        siblings.last().find('.treeView-branch').first().data('tree-postion-down', false);
        siblings.last().prev().find('.treeView-branch').first().data('tree-postion-down', true);

    }

    /**
     *  Muestra una pantalla de confirmación
     *
     *  @param target {string} ID del div modal de confirmación
     *  @param callback {function} Función que se ejecutará en caso afirmativo
     */
    var launchPrevent = function(target, callback) {
        elementTarget = $(target);
        elementTarget.modal();

        elementTarget > $('*[data-ajax-action="accept"]').click(function($event) {
            elementTarget.modal('hide');
            $(this).unbind('click');
            callback();
        });
    }
}

/**
 * @param siblings {jQuery} List of sibling elements to act upon
 * @param subjectIndex {int} Index of the item to be moved
 * @param objectIndex {int} Index of the item to move subject after
 */
var swapElements = function(subject, object) {
    // Insert subject after object
    subject.insertAfter(object);
}

function setStars(){
    if($('.rate-stars').length > 0){
        $('.rate-stars').raty({
            path: '',
            number: 5,
            score: function() {
                return $(this).attr('data-raty-score');
            },
            hints: ['1', '2', '3', '4', '5'],
            half: true,
            click: function(score, event) {
                var addRateUrl = $(this).attr('data-raty-url');
                var scorePanel = $(this).parent().find('.rate-score');
                if (addRateUrl != '') {
                    $.post(
                        addRateUrl + '?rate=' + score,
                        null,
                        function(data) {
                            scorePanel.html(data);
                        }
                    );
                } else {
                    var score = scorePanel.html();
                    $(this).raty('score', score );
                    return false;
                }
            }
        });
    }
}

function setAjaxContentLoader() {
    // Inicializamos los selectores que modifican contenido
    // data-ajax-target: Lugar donde insertaremos el contenido
    $('select[data-ajax-target]').each(function() {
        // Almacenamos el valor actual del selector en data-ajax-current
        $selectedValue = $(this).val();
        $(this).data('ajax-current', $selectedValue);
    });

    // Controlamos los cambios del selector
    $('select[data-ajax-target]').change(function($event) {
        var $selectedValue = $(this).val();
        var $target = $(this).data('ajax-target');
        var $prevent = $(this).data('ajax-prevent');

        if ($($target).is(':empty')) {
            if ($selectedValue != '') {
                // Si no tiene contenido y no seleccionamos vacio, cargamos el contenido
                loadAjaxContent($(this), $target);
            }
        } else {
            // Almacemamos en la ventana modal a donde queremos cambiar
            $($prevent).data('ajax-change-to', $(this).val());
            // Devolvemos el selector al valor previo
            $current = $(this).data('ajax-current');
            $(this).val($current);
            // Mostramos la modal de advertencia
            initAjaxPrevent($($prevent), $(this), $target);
            $($prevent).modal();
        }
    });
}

function loadAjaxContent($launcherElement, $target) {
    var $selectedValue = $launcherElement.val();
    var $form = $launcherElement.closest('form');
    var $url = $form.attr('action');
    var $type = $form.attr('method');
    var $data = {};
    $data[$launcherElement.attr('name')] = $selectedValue;

    $($target).addClass('spinner');
    var $spinner = setSpinner($target);

    $.ajax({
        url : $url,
        type: $type,
        data : $data,
        success: function(html) {
            $spinner.stop();
            $($target).removeClass('spinner');
            $($launcherElement.data('ajax-target')).replaceWith(
                $(html).find($launcherElement.data('ajax-target'))
            );
        }
    });

    $launcherElement.data('ajax-current', $selectedValue);
}

function initAjaxPrevent($prevent, $launcherElement, $target) {
    $prevent > $('*[data-ajax-action="accept"]').click(function($event) {
        $prevent.modal('hide');
        var $value = $($prevent).data('ajax-change-to');
        $($prevent).data('ajax-change-to', '');
        $($target).html('');
        $launcherElement.val($value);
        $(this).data('ajax-current', $value);
        $launcherElement.change();
        $(this).unbind('click');
    });

    $prevent > $('*[data-ajax-action="cancel"]').click(function($event) {
        $($prevent).data('ajax-change-to', '');
        $prevent.modal('hide');
        $event.preventDefault();
        $(this).unbind('click');
    });
}

function setSpinner(element, size) {
    var opts;
    if(typeof size === "undefined") {
        size = 'normal';
    }

    if (size == 'normal') {
        opts = {
            lines: 9, // The number of lines to draw
            length: 6, // The length of each line
            width: 6, // The line thickness
            radius: 12, // The radius of the inner circle
            corners: 1, // Corner roundness (0..1)
            rotate: 0, // The rotation offset
            direction: 1, // 1: clockwise, -1: counterclockwise
            color: '#000', // #rgb or #rrggbb or array of colors
            speed: 1.1, // Rounds per second
            trail: 51, // Afterglow percentage
            shadow: false, // Whether to render a shadow
            hwaccel: false, // Whether to use hardware acceleration
            className: 'spinner', // The CSS class to assign to the spinner
            zIndex: 2e9, // The z-index (defaults to 2000000000)
            top: '50%', // Top position relative to parent
            left: '50%' // Left position relative to parent
        };
    } else {
        opts = {
            lines: 7, // The number of lines to draw
            length: 2, // The length of each line
            width: 3, // The line thickness
            radius: 5, // The radius of the inner circle
            corners: 1, // Corner roundness (0..1)
            rotate: 0, // The rotation offset
            direction: 1, // 1: clockwise, -1: counterclockwise
            color: '#000', // #rgb or #rrggbb or array of colors
            speed: 1, // Rounds per second
            trail: 50, // Afterglow percentage
            shadow: false, // Whether to render a shadow
            hwaccel: false, // Whether to use hardware acceleration
            className: 'spinner', // The CSS class to assign to the spinner
            zIndex: 2e9, // The z-index (defaults to 2000000000)
            top: '50%', // Top position relative to parent
            left: '50%' // Left position relative to parent
        };
    }

    var target = $(element);
    var spinner = new Spinner(opts).spin();
    target.html(spinner.el);

    return spinner;
}

function initizalizeFilters() {
    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        var contentID = $(e.target).attr('href');
        var listID = $(contentID).data('filtered-list');
        if ($(listID).data('isotope')) {
            var filterKey = $(contentID).find('.solicitudes__filtros__item--active').data('filter-item');
            $(listID).isotope('reloadItems').isotope({ filter: filterKey });
        }
    })

    $('*[data-filtered-list]').each(function() {
        var wrapper     = $(this),
            containerId = $(this).data('filtered-list'),
            container   = $(containerId),
            itemId      = container.data('filtered-list-item');

        if (container.data('isotope')) container.isotope('destroy');

        container.isotope({
            // options
            itemSelector: itemId,
            layoutMode: 'vertical'
        });



        var isotope = container.data('isotope');

        wrapper.find('*[data-filter-item]').each(function() {
            var filterKey = $(this).data('filter-item');
            $(this).click(function(e) {
                container.isotope({ filter: filterKey });
                wrapper.find('*[data-filter-item]').removeClass('solicitudes__filtros__item--active');
                $(this).addClass('solicitudes__filtros__item--active');
                e.preventDefault();
            });

            var itemCount   = 0,
                label       = $(this).text();

            if (filterKey == '*') itemCount = isotope.items.length;
            else {
                var className = filterKey.replace('.', '');
                aux = jQuery.grep(isotope.items, function( item, index ) {
                    return $(item.element).hasClass(className);
                });
                itemCount = aux.length;
            }

            label += ' (' + itemCount + ')';
            $(this).text(label);
        });
    });
}

function initializeFormControls() {
    var actions = ['save', 'save-and-exit', 'revise', 'validate', 'publish', 'delete'];

    $.each(actions, function(index, value) {
        $('*[data-form-action="' + value + '"]').click(function() {
            var form = $(this).closest('form');
            var action = $(this).data('form-action');
            var redirectUrl = $(this).data('form-url');

            form.find('input[data-form-sender="action"]').val(action);
            form.find('input[data-form-sender="target"]').val(redirectUrl);
            $(this).closest('form').submit();
        });
    });

    $('*[data-form-action="preview"]').click(function() {
        var targetForm = '#' + $(this).data('form-id');
        var targetUrl = $(this).data('form-url');
        var form = $(targetForm);

        form.attr('target', '_blank').attr('action', targetUrl).submit().attr('target', '_self').attr('action', '');
    });

    $('#article-comments').find('form').submit(commentFormHandler);

    function commentFormHandler(event) {
        event.preventDefault();
        var actionUrl = $(this).attr('action');
        var spinnerElement = $(this).find('.spinner');
        var spinner = setSpinner(spinnerElement, 'small');
        $.post(
            actionUrl,
            $(this).serialize(),
            function(data) {
                $('#article-comments').html(data);
                $('#article-comments').find('form').submit(commentFormHandler);
            }
        ).always(function() {
            spinner.stop();
        });
    }

    $('#category-selector').each(function() {
        var popupTarget = '#category-selector-popup',
            fancyboxOptions = {
            type        : 'inline',
            padding     : 0,
            margin      : 10,
            maxWidth    : 620,
            minWidth    : 280,
            width       : 620,
            height      : 'auto',
            autoHeight  : true,
            fitToView   : true,
            autoSize    : true,
            autoCenter  : false,
            modal       : false,
            href 	      : popupTarget,
            afterClose  : function() {
                resetBranchs();
                $(popupTarget + ' *[data-popup-action="accept"]').unbind('click');
            },
            helpers: {
                overlay: {
                    locked: false
                }
            }
        };

        var resetBranchs = function() {
            $(popupTarget +' .treeView-branch').not('#treeView-main').each(function() {
                if ($(this).hasClass('selected')) {
                    toggleBranch($(this));
                }
                var sCollapsable = $(this).find('.collapsable').first();
                if (isCollapsed(sCollapsable) == false) {
                    toogleCollapse(sCollapsable);
                }
            });
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

        $(this).find('.selector-popup-launcher').click(function() {
            $.fancybox(fancyboxOptions);

            $(popupTarget + ' *[data-popup-action="accept"]').click(function() {
                var selected = false,
                    categorySelector = $('#category-selector');
                $(popupTarget +' .treeView-branch.selected').each(function() {
                    var id = $(this).find('.treeView-check-selected').data('internal-link-id');
                    var title = $(this).find('.treeView-check-selected').data('internal-link-title');

                    categorySelector.find('#category-selector-id').val(id).trigger('change');
                    categorySelector.find('#category-selector-title').val(title).trigger('change');
                    selected = true;
                });
                if (!selected) {
                    categorySelector.find('#category-selector-id').val('').trigger('change');
                    categorySelector.find('#category-selector-title').val('').trigger('change');
                }
                $.fancybox.close();
            });
        });
    });
}

// Tree Handling (Categories and Articles)
function AjaxFormHandler () { }

AjaxFormHandler.init = function () {
    $('.formAjax').each(function() {
        var completePopup = $(this).data('formajax-complete'),
            spinner = null;

        var options = {
            type:          'post',
            dataType:      'json',
            beforeSubmit:  preRequest,
            success:       postRequest
        };

        var resetForm = function (form) {
            form.find('input').each(function() {
                if ($(this).attr('type') == 'text') {
                    $(this).val('');
                }
            });
            form.find('textarea').each(function() {
                $(this).val('');
            });
            form.find('.fileinput').each(function() {
                $(this).fileinput('clear');
            });
        }

        $(this).find('form').ajaxForm(options);

        function preRequest(formData, jqForm, options) {
            var spinnerElement = jqForm.find('.spinner');
            spinner = setSpinner(spinnerElement, 'small');
        }

        function postRequest(responseText, statusText, xhr, $form) {
            $.fancybox.close();
            if (completePopup != null) openFancyBox(completePopup);
            spinner.stop();
            resetForm($form);
        }
    });
}