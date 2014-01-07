/*global jQuery,ajaxurl */
var setSpwSortable;
jQuery(document).ready(function($){
    var ids = [];
    setSpwSortable = function(){
        $( ".selected-posts" ).sortable({
            stop: function(){
                ids = [];
                $(this).find('.selected-post').each(function(){
                    ids.push($(this).data('post-id'));
                });
                $(this).closest('.spw-form').find('.post-list').val(JSON.stringify(ids));

            },
        });
    };
    var addPost = function($container, id){
        var posts = [];
        $container.find('.post-list');
        if($container.val()){
            posts = JSON.parse($container.val());
        }
        posts.push(id);
        $container.val(JSON.stringify(posts));
    };
    var updatePosts = function($parent){
        var order = [];
        $parent.find('.selected-post:visible').each(function(){
            order.push ($(this).data('post-id'));
        });
        $parent.find('.post-list').val(JSON.stringify(order));

    };
    $('.widget').on('click', '.spw-plus', function(){
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
    $('.widget').on('click', '.spw-minus', function(){
        var $form = $(this).closest('.spw-form'),
            $parent = $(this).closest('.selected-post');
        $parent.fadeOut(function(){
            updatePosts($form);
        });

            
            
    });
    $('.widget').on('keypress', '.spw-search', function(e) {
        if(e.which === 13) {
            e.preventDefault();
            var $form = $(this).closest('.spw-form'),
                $spinner = $form.find('.loading'),
                query = $form.find('.spw-search').val(),
                $searchResults = $form.find('.search-results');
                
            if (!query){
                return;
            }
            var $postList = $form.find('.post-list');
            $spinner.show();
            $searchResults.html('');
            var data = {
                action: 'spw_search',
                query: query,
                alreadySelected: $postList.val()
            };
            $.post(ajaxurl, data, function(response) {
                $searchResults.html(response);
                $spinner.hide();
            });


        }
    });
    setSpwSortable();



});