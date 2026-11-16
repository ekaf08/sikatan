<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
{{-- <script src="assets/js/app.js"></script> --}}
<script src="{{ asset('sikatan/assets/js/app.js') }}"></script>

<!-- jQuery (Wajib untuk DataTables) -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<!-- JS DataTables -->
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

<!-- JS Flatpickr -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/l10n/id.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof window.flatpickr === 'undefined') {
            return;
        }

        const locale = window.flatpickr.l10ns && window.flatpickr.l10ns.id ?
            window.flatpickr.l10ns.id :
            'default';

        document.querySelectorAll('input[type="date"], input[data-flatpickr]').forEach(function(input) {
            if (input._flatpickr) {
                return;
            }

            input.type = 'text';
            input.autocomplete = 'off';

            window.flatpickr(input, {
                locale: locale,
                dateFormat: 'Y-m-d',
                allowInput: true,
                disableMobile: true
            });
        });
    });
</script>
