/**
 * All of the JavaScript for your public-facing functionality should be
 * included in this file.
 *
 * @package    Emojis_For_Posts_And_Pages
 * @subpackage Emojis_For_Posts_And_Pages/public/js
 * @since      1.0.0
 */
(function($) {
    'use strict';

    /**
     * Handle emoji reactions
     */
    $(document).ready(function() {
        // Handle reaction button click
        $(document).on('click', '.emojfopo-reaction-button', function() {
            const button = $(this);
            const container = button.closest('.emojfopo-container');
            const postId = container.data('post-id');
            const reaction = button.data('reaction');
            
            // Don't do anything if already processing
            if (container.hasClass('processing')) {
                return;
            }
            
            // Add processing class to prevent multiple clicks
            container.addClass('processing');
            
            // Add animation class
            button.addClass('reacting');
            
            // Send AJAX request
            $.ajax({
                url: emojfopo_reactions.ajax_url,
                type: 'POST',
                data: {
                    action: 'emojfopo_reaction',
                    post_id: postId,
                    reaction: reaction,
                    nonce: emojfopo_reactions.nonce
                },
                success: function(response) {
                    if (response.success) {
                        updateReactionCounts(container, response.data.counts, response.data.user_reaction);
                        
                        // Show success message
                        showMessage(container, emojfopo_reactions.reaction_added, 'success');
                    } else {
                        // Show error message
                        showMessage(container, response.data.message || emojfopo_reactions.error, 'error');
                    }
                },
                error: function() {
                    // Show error message
                    showMessage(container, emojfopo_reactions.error, 'error');
                },
                complete: function() {
                    // Remove processing class
                    container.removeClass('processing');
                    
                    // Remove animation class after a delay
                    setTimeout(function() {
                        button.removeClass('reacting');
                    }, 300);
                }
            });
        });
        
        /**
         * Update reaction counts and active state
         */
        function updateReactionCounts(container, counts, userReaction) {
            // Update counts for all reactions
            container.find('.emojfopo-reaction-button').each(function() {
                const button = $(this);
                const reaction = button.data('reaction');
                const countEl = button.find('.count');
                
                // Update count
                countEl.text(counts[reaction] || 0);
                
                // Update active state
                if (reaction === userReaction) {
                    button.addClass('active');
                } else {
                    button.removeClass('active');
                }
            });
            
            // Update user reaction message
            const messageEl = container.find('.emojfopo-message');
            if (messageEl.length === 0) {
                // Create message element if it doesn't exist
                const message = $('<div class="emojfopo-message"></div>');
                container.append(message);
            }
            
            // Get emoji for user reaction
            let emojiHtml = '';
            if (userReaction) {
                const emojiEl = container.find('.emojfopo-reaction-button[data-reaction="' + userReaction + '"] .emoji');
                if (emojiEl.length > 0) {
                    emojiHtml = emojiEl.html();
                }
            }
            
            // Update message
            if (userReaction) {
                container.find('.emojfopo-message').html(emojfopo_reactions.you_reacted_with + ' ' + emojiHtml);
            } else {
                container.find('.emojfopo-message').html('');
            }
            
            // If there are multiple containers for the same post (e.g., floating and inline),
            // update them all
            const postId = container.data('post-id');
            $('.emojfopo-container[data-post-id="' + postId + '"]').not(container).each(function() {
                updateReactionCounts($(this), counts, userReaction);
            });
        }
        
        /**
         * Show message
         */
        function showMessage(container, message, type) {
            // Create message element if it doesn't exist
            let messageEl = container.find('.emojfopo-message');
            if (messageEl.length === 0) {
                messageEl = $('<div class="emojfopo-message"></div>');
                container.append(messageEl);
            }
            
            // Add class based on message type
            messageEl.removeClass('success error').addClass(type);
            
            // Set message text
            messageEl.text(message);
            
            // Hide message after a delay
            setTimeout(function() {
                messageEl.fadeOut(function() {
                    // If user has reacted, show the permanent message
                    const userReaction = container.find('.emojfopo-reaction-button.active').data('reaction');
                    if (userReaction) {
                        const emojiEl = container.find('.emojfopo-reaction-button[data-reaction="' + userReaction + '"] .emoji');
                        if (emojiEl.length > 0) {
                            const emojiHtml = emojiEl.html();
                            messageEl.html(emojfopo_reactions.you_reacted_with + ' ' + emojiHtml);
                            messageEl.removeClass('success error');
                            messageEl.fadeIn();
                        }
                    }
                });
            }, 3000);
        }
        
        /**
         * Load reaction counts on page load
         */
        function loadReactionCounts() {
            $('.emojfopo-container').each(function() {
                const container = $(this);
                const postId = container.data('post-id');
                
                if (!postId) {
                    return;
                }
                
                $.ajax({
                    url: emojfopo_reactions.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'get_emojfopo_reactions',
                        post_id: postId,
                        nonce: emojfopo_reactions.nonce
                    },
                    success: function(response) {
                        if (response.success) {
                            updateReactionCounts(container, response.data.counts, response.data.user_reaction);
                        }
                    }
                });
            });
        }
        
        // Load reaction counts on page load
        loadReactionCounts();
    });

})(jQuery);
