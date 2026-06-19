// Using $(function() { ... }) is the shorthand for $(document).ready()
$(function() {

    // BULK SELECT & DELETE (Event Delegation)
    // This works even if the table is loaded dynamically
    $(document).on('change', '#selectAll', function() {
        const isChecked = $(this).prop('checked');
        $('.rowCheckbox').prop('checked', isChecked);
        updateDeleteButton();
    });

    $(document).on('change', '.rowCheckbox', function() {
        // Sync "Select All" state
        const total = $('.rowCheckbox').length;
        const checked = $('.rowCheckbox:checked').length;
        $('#selectAll').prop('checked', total === checked);
        
        updateDeleteButton();
    });

    function updateDeleteButton() {
        const anyChecked = $('.rowCheckbox:checked').length > 0;
        const deleteBtn = $('#deleteBtn');

        if (deleteBtn.length) {
            deleteBtn.prop('disabled', !anyChecked);
            if (anyChecked) {
                deleteBtn.removeClass('btn-secondary').addClass('btn-danger');
            } else {
                deleteBtn.removeClass('btn-danger').addClass('btn-secondary');
            }
        }
    }

    // TOGGLE DETAILS (Business History)
    $(document).on('click', '.toggle-more-details', function() {
        const btn = $(this);
        const currentRow = btn.closest('tr');
        const detailsRow = currentRow.next('.hidden-details');
        const icon = btn.find('i');

        // Close other rows
        $('.hidden-details.show-details').not(detailsRow).removeClass('show-details');
        $('.toggle-more-details i').not(icon).removeClass('fa-sort-up').addClass('fa-sort-down');

        // Toggle current
        detailsRow.toggleClass('show-details');
        icon.toggleClass('fa-sort-down fa-sort-up');
    });

    // 3. LIVE EDIT TIMERS (Page-Specific Wording)
    function updateTimers() {
        const now = Math.floor(Date.now() / 1000);
        
        // Check if we are on the View Details page
        // (Make sure your view-details.php container has id="view-details-page")
        const isDetailsPage = document.getElementById('view-details-page') !== null;

        $('.edit-timer').each(function() {
            const ts = parseInt($(this).attr('data-timestamp'));
            if (isNaN(ts)) return;

            const diff = now - ts;
            let label = "";

            if (diff < 5) label = "just now";
            else if (diff < 60) label = diff + "s ago";
            else if (diff < 3600) label = Math.floor(diff / 60) + "m ago";
            else if (diff < 86400) label = Math.floor(diff / 3600) + "h ago";
            else if (diff < 604800) label = Math.floor(diff / 86400) + "d ago";
            else if (diff < 2592000) label = Math.floor(diff / 604800) + "w ago";
            else if (diff < 31536000) label = Math.floor(diff / 2592000) + "mo ago";
            else label = Math.floor(diff / 31536000) + "y ago";

            // If it's the details page, use "Updated", otherwise use "Edited"
            const prefix = isDetailsPage ? "Updated " : "Edited ";
            $(this).text(prefix + label);
        });
    }
    setInterval(updateTimers, 1000);
    updateTimers();

    // AJAX FORM SUBMISSIONS (Unified)
    $(document).on('submit', '.update-form, #updateDetailsForm', function(e) {
        e.preventDefault();
        const form = $(this);
        const historyId = form.data('id');

        $.post(form.attr('action') || 'actions/update_loan.php', form.serialize(), function(res) {
            // Close any open bootstrap modals
            $('.modal').modal('hide');

            // If it's a simple update (history.php), we refresh the UI
            // If it's business or view-details, we reload for data integrity
            if (historyId && $('#row-' + historyId).length) {
                location.reload(); // Safest for syncing checkboxes & timers
            } else {
                location.reload();
            }
        });
    });
});