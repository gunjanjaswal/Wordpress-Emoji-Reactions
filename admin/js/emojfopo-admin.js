/**
 * All of the JavaScript for your admin-specific functionality should be
 * included in this file.
 */
(function($) {
    'use strict';

    /**
     * Update the preview when emoji settings change
     */
    $(document).ready(function() {
        // Update preview when emoji checkboxes change
        $('input[name^="emojfopo_enabled_emojis"]').on('change', function() {
            updatePreview();
        });

        function updatePreview() {
            const previewContainer = $('.emojfopo-buttons');
            previewContainer.empty();

            // Get all checked emojis
            $('input[name^="emojfopo_enabled_emojis"]:checked').each(function() {
                const key = $(this).attr('name').match(/\[(.*?)\]/)[1];
                const emoji = $(this).val();
                
                const button = $('<div class="wp-emoji-reaction-button" data-reaction="' + key + '"></div>');
                button.append('<span class="emoji">' + emoji + '</span>');
                button.append('<span class="count">0</span>');
                
                previewContainer.append(button);
            });

            // If no emojis are selected, show a message
            if (previewContainer.children().length === 0) {
                previewContainer.append('<p>' + emojfopo_admin.no_emojis_selected + '</p>');
            }
        }

        // Make the preview buttons interactive for demonstration
        $(document).on('click', '.emojfopo-reaction-button', function() {
            const countEl = $(this).find('.count');
            let count = parseInt(countEl.text(), 10);
            countEl.text(count + 1);
            
            // Add a brief animation
            $(this).addClass('active');
            setTimeout(() => {
                $(this).removeClass('active');
            }, 300);
        });
    });

})(jQuery);
