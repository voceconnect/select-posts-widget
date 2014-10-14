/*global jQuery,ajaxurl */

var setSpwSortable; //declare it in the global namespace so that we can use it externally

jQuery(document).ready(function ($) {

    /**
     * Set the global namespaced setSpwSortable to our function
     */
    setSpwSortable = function () {
        $(".selected-posts").sortable({
            stop: function () {
                ids = [];
                $(this).find('.selected-post').each(function () {
                    ids.push($(this).data('post-id'));
                });
                $(this).closest('.spw-form').find('.post-list').val(JSON.stringify(ids));
                triggerChange($(this));

            }
        });
    };
    var triggerChange = function ($element) {
            /* trigger the change event for the theme customizer to save */
            $element.closest('.widget-content').find('input.title').change();
        },
        ids = [],
        $widgetsHolderWrap = $('.widgets-holder-wrap, #accordion-panel-widgets'),  /* accommodate theme customizer and widget screen */
        addPost = function ($container, id) {
            var posts = [],
                $parent = $container.closest('.spw-form'),
                $noSelected = $parent.find('.spw-no-selected');
            $container.find('.post-list');
            if ($container.val()) {
                posts = JSON.parse($container.val());
            }
            if (posts && !$.isArray(posts)) { //prevent any issues with "null" literal being in the input container
                posts = [];
            }
            posts.push(id);
            $noSelected.hide();
            $container.val(JSON.stringify(posts));
            triggerChange($container);


        },
        updatePosts = function ($parent) {
            var order = [],
                $noSelected = $parent.find('.spw-no-selected');
            $parent.find('.selected-post:visible').each(function () {
                order.push($(this).data('post-id'));
            });
            if (order.length <= 0) {
                $noSelected.show();
            }
            $parent.find('.post-list').val(JSON.stringify(order));
            triggerChange($parent);


        },
        spwAttachEvents = function () {
            $widgetsHolderWrap.on('click', '.spw-plus', function () {
                var $form = $(this).closest('.spw-form'),
                    $parent = $(this).closest('.search-result'),
                    $postList = $form.find('.post-list'),
                    postId = $parent.data('post-id'),
                    $selectedPosts = $form.find('.selected-posts');
                addPost($postList, postId);

                $parent.addClass('selected-post').removeClass('search-result');
                $parent.find('.spw-plus').html('-').addClass('spw-minus').removeClass('spw-plus');

                $selectedPosts.append($parent);


            });
            $widgetsHolderWrap.on('click', '.spw-minus', function () {
                var $form = $(this).closest('.spw-form'),
                    $parent = $(this).closest('.selected-post');
                $parent.fadeOut(function () {
                    updatePosts($form);

                });


            });

            // unbind all keydown events that trigger a save on the theme customizer and clear out the spw-search contents
            $('.spw-search').closest('.widget-content').each(function () {
                $(this).unbind('keydown');
            });

            $widgetsHolderWrap.on('keydown', '.spw-search', function (e) {
                $(this).css('background', '');
                $(this).css('border', '');
                if (e.which === 13) {
                    e.preventDefault();
                    var $form = $(this).closest('.spw-form');
                    $form.find('.spw-search-button').click();
                }
            });


            $widgetsHolderWrap.on('click', '.spw-search-button', function () {

                var $form = $(this).closest('.spw-form'),
                    $spinner = $form.find('.loading'),
                    query = $form.find('.spw-search').val(),
                    $searchResults = $form.find('.search-results'),
                    $nonce = $form.find('.security'),
                    nonce = $nonce.val();
                $searchResults.html('');
                if (!query) {
                    $form.find('.spw-search').css('border', '1px solid #FF0000');
                    $form.find('.spw-search').css('background', '#ffece8');
                    return;
                }
                var $postList = $form.find('.post-list');
                $spinner.show();
                var data = {
                    action         : 'spw_search',
                    query          : query,
                    alreadySelected: $postList.val(),
                    security       : nonce
                };
                $.post(ajaxurl, data, function (response) {
                    $searchResults.html(response);
                    $spinner.hide();
                });
            });
        };


    spwAttachEvents();
    setSpwSortable();
});