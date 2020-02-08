$('a._method_post').on('click', function (event) {
    event.preventDefault();

    // TODO: Merge form in twig

    var middleForm = '<form action="' + $(this).prop('href') + '" method=post></form>';
    $(middleForm).submit();

});

$(function() {
    initCollectionHandler();
    initShowLauncher();

    $('.datepicker').each(function() {
        $(this).datetimepicker({
            weekStart: 1,
            pickTime: false,
            language: 'es'
        });
    });
    
    $('.datetime_picker').each(function() {
        $(this).datetimepicker({
            weekStart: 1,
            pickTime: true,
            language: 'es'
        });
    });
});

function initCollectionHandler() {
    $('*[data-collection-prototype]').each(function (){
        var $addTagLink = $('<a class="btn btn-primary" href="#"><span class="glyphicon glyphicon-plus"></span> Añadir Página</a>');
        var $newLinkLi = $('<div class="form-group"></div>').append($addTagLink);
        var $collectionHolder;

        // Get the ul that holds the collection of tags
        $collectionHolder = $(this);

        var $collectionElements = $collectionHolder.find('.panel');
        $collectionElements.each(function() {
            removeElementForm($(this));
        });

        // add the "add a tag" anchor and li to the tags ul
        $collectionHolder.parent('div').append($addTagLink);

        // count the current form inputs we have (e.g. 2), use that as the new
        // index when inserting a new item (e.g. 2)
        $collectionHolder.data('index', $collectionHolder.find(':input').length);

        $addTagLink.on('click', function(e) {
            // prevent the link from creating a "#" on the URL
            e.preventDefault();

            // add a new tag form (see next code block)
            addElementForm($collectionHolder, $newLinkLi);
        });
    });

    function addElementForm($collectionHolder, $newLinkLi) {
        // Get the data-prototype explained earlier
        var prototype = $collectionHolder.data('collection-prototype');

        // get the new index
        var index = $collectionHolder.data('collection-index');

        // Replace '__name__' in the prototype's HTML to
        // instead be a number based on how many items we have
        var newForm = prototype.replace(/__name__/g, index);

        // increase the index with one for the next item
        $collectionHolder.data('collection-index', index + 1);

        // Display the form in the page in an li, before the "Add a tag" link li
        $collectionHolder.append(newForm);

        var $lastElement = $collectionHolder.find('.panel').last();
        removeElementForm($lastElement);
        setShowLauncher($lastElement);
    }

    function removeElementForm($collectionElement) {
        var $removeForm = $('<button type="button" class="close" data-dismiss="delete"><span aria-hidden="true">&times;</span><span class="sr-only">Eliminar</span></button>');
        $collectionElement.find('.panel-heading h4').append($removeForm);
        $removeForm.on('click', function(e) {
            e.preventDefault();
            $collectionElement.remove();
        });
    }
}

function initShowLauncher() {
    $('*[data-show-launcher]').each(function (){
        var $wrapper = $(this).parent('div');
        setShowLauncher($wrapper);
    });
}

function setShowLauncher($element) {
    $element.find('*[data-show-launcher]').each(function (){
        var $wrapper = $element.parent('div');
        hideElements($wrapper);

        $(this).change(function() {
            hideElements($wrapper);
            var $value = $(this).val();
            if ($value != '') {
                var $toShowElement = $wrapper.find('*[data-show-on="' + $value + '"]');
                if ($toShowElement.length) $toShowElement.parent('.form-group').removeClass('hidden');
            }
        })
        $(this).change();
    });
}

function hideElements($wrapper) {
    var $elements = $wrapper.find('*[data-show-on]');

    $elements.each(function () {
        $(this).parent('.form-group').addClass('hidden');
    });
}
