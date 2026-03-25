/**
 * XSTO Pinned Scroll — Admin repeater + media uploader
 *
 * All selectors use xsto-* prefixed classes only (no WP postbox/hndle classes)
 * to avoid conflicts with WordPress core admin JS.
 */
(function ($) {
    'use strict';

    /* ======================================================================
       REPEATER — Add row
       ====================================================================== */
    $(document).on('click', '.xsto-add-row-btn', function (e) {
        e.preventDefault();

        var $list    = $('#xsto-repeater-list');
        var newIndex = $list.find('.xsto-row').length;

        // Get template from <script type="text/html"> (never inside the form)
        var html = $('#tmpl-xsto-row').html();
        html = html.replace(/\{\{IDX\}\}/g, newIndex);

        var $row = $(html).filter('.xsto-row');

        // Remove empty state if present
        $list.find('.xsto-repeater__empty').remove();

        $list.append($row);
        renumberRows();

        // Scroll to new row
        $('html, body').animate({ scrollTop: $row.offset().top - 80 }, 300);
    });

    /* ======================================================================
       REPEATER — Remove row
       ====================================================================== */
    $(document).on('click', '.xsto-row__remove', function (e) {
        e.preventDefault();

        var $row  = $(this).closest('.xsto-row');
        var title = $row.find('.xsto-row__title-input').val() || 'this industry';

        if (!confirm('Remove "' + title + '"? This cannot be undone after saving.')) {
            return;
        }

        $row.slideUp(250, function () {
            $(this).remove();
            renumberRows();

            if ($('#xsto-repeater-list .xsto-row').length === 0) {
                $('#xsto-repeater-list').html(
                    '<div class="xsto-repeater__empty"><p>No industries added yet. Click "Add Industry" to get started.</p></div>'
                );
            }
        });
    });

    /* ======================================================================
       REPEATER — Collapse / expand
       ====================================================================== */
    $(document).on('click', '.xsto-row__toggle', function (e) {
        e.preventDefault();
        e.stopPropagation();

        var $row  = $(this).closest('.xsto-row');
        var $body = $row.find('.xsto-row__body');
        var $icon = $(this).find('.dashicons');

        $body.slideToggle(200);
        $icon.toggleClass('dashicons-arrow-up-alt2 dashicons-arrow-down-alt2');
    });

    /* ======================================================================
       REPEATER — Live title preview
       ====================================================================== */
    $(document).on('input', '.xsto-row__title-input', function () {
        var val = $(this).val() || 'New Industry';
        $(this).closest('.xsto-row').find('.xsto-row__title-preview').text(val);
    });

    /* ======================================================================
       REPEATER — Sortable (drag to reorder)
       ====================================================================== */
    function initSortable() {
        var $list = $('#xsto-repeater-list');
        if ($list.length && $.fn.sortable) {
            $list.sortable({
                handle: '.xsto-row__drag',
                items: '.xsto-row',
                axis: 'y',
                opacity: 0.7,
                placeholder: 'xsto-row__placeholder',
                update: function () {
                    renumberRows();
                }
            });
        }
    }

    /* ======================================================================
       REPEATER — Re-index all rows
       ====================================================================== */
    function renumberRows() {
        $('#xsto-repeater-list .xsto-row').each(function (i) {
            var $row = $(this);
            var num  = ('0' + (i + 1)).slice(-2);

            $row.attr('data-index', i);
            $row.find('.xsto-row__num').text(num);

            // Replace xsto_cat[ANY_KEY][field] → xsto_cat[i][field]
            $row.find('[name]').each(function () {
                var name = $(this).attr('name');
                if (name) {
                    $(this).attr('name', name.replace(/xsto_cat\[[^\]]*\]/, 'xsto_cat[' + i + ']'));
                }
            });
        });
    }

    /* ======================================================================
       MEDIA UPLOADER — Select image
       ====================================================================== */
    $(document).on('click', '.xsto-upload-btn', function (e) {
        e.preventDefault();

        var $field = $(this).closest('.xsto-image-field');

        // Create a fresh frame each time so the correct target is captured
        var frame = wp.media({
            title: 'Select Featured Image',
            button: { text: 'Use this image' },
            multiple: false,
            library: { type: 'image' }
        });

        frame.on('select', function () {
            var attachment = frame.state().get('selection').first().toJSON();
            var url = (attachment.sizes && attachment.sizes.medium)
                ? attachment.sizes.medium.url
                : attachment.url;

            $field.find('.xsto-image-id').val(attachment.id);
            $field.find('.xsto-image-preview').html('<img src="' + url + '" alt="">');
            $field.find('.xsto-upload-btn').text('Change Image');
            $field.find('.xsto-remove-image-btn').show();
        });

        frame.open();
    });

    /* ======================================================================
       MEDIA UPLOADER — Remove image
       ====================================================================== */
    $(document).on('click', '.xsto-remove-image-btn', function (e) {
        e.preventDefault();

        var $field = $(this).closest('.xsto-image-field');

        $field.find('.xsto-image-id').val(0);
        $field.find('.xsto-image-preview').html('');
        $field.find('.xsto-upload-btn').text('Select Image');
        $(this).hide();
    });

    /* ======================================================================
       INIT
       ====================================================================== */
    $(function () {
        initSortable();
    });

})(jQuery);
