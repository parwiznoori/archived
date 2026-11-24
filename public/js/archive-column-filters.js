(($) => {
    function normalizeDigits(value) {
        return value
            .replace(/[۰-۹]/g, digit => String(digit.charCodeAt(0) - 0x06f0))
            .replace(/[٠-٩]/g, digit => String(digit.charCodeAt(0) - 0x0660));
    }

    $(document).on('init.dt', 'table', function (event, settings) {
        if (settings?.nTable !== this || !this.tFoot || !this.tBodies[0]) return;

        const footer = this.tFoot;
        const api = new $.fn.dataTable.Api(settings);
        const createFilters = this.hasAttribute('data-column-filters');
        let searchTimer;

        api.columns().every(function () {
            const column = this;
            const cell = column.footer();
            if (!cell) return;

            if (['action', 'actions'].includes(column.dataSrc())) {
                $(cell).find('input, select').remove();
                return;
            }

            const title = column.header().textContent.trim();
            if (createFilters && settings.aoColumns[column.index()].bSearchable) {
                const date = cell.classList.contains('hijri-date-filter');
                const numeric = cell.classList.contains('numeric-filter');
                const input = document.createElement('input');

                input.type = 'search';
                input.className = 'datatable-footer-input';
                input.size = 10;
                input.dir = date || numeric ? 'ltr' : 'auto';
                input.placeholder = date ? '۱۴۰۳/۰۱/۰۱' : title;
                input.value = column.search();
                if (numeric) input.inputMode = 'numeric';
                cell.replaceChildren(input);

                const queueSearch = () => {
                    const text = input.value.trim();
                    const value = date || numeric ? normalizeDigits(text) : text;
                    if (value === column.search()) return;

                    column.search(value, false, false, true);
                    clearTimeout(searchTimer);
                    searchTimer = setTimeout(() => api.draw(), 180);
                };

                input.addEventListener('input', event => {
                    if (!event.isComposing) queueSearch();
                });
                input.addEventListener('compositionend', queueSearch);
            }

            $(cell).find('input, select').attr('aria-label', title);
        });

        if (!footer.querySelector('input, select')) return;

        // Keep the footer node available to DataTables and to the existing reset handlers.
        footer.classList.add('archive-column-filters');
        this.insertBefore(footer, this.tBodies[0]);

        if (!this.parentElement.classList.contains('table-responsive')) {
            $(this).wrap('<div class="table-responsive"></div>');
        }

        $(api.table().container()).on('click', '.buttons-reset', () => {
            clearTimeout(searchTimer);
            $(footer).find('input, select').val('');
        });

        api.on('destroy.dt', () => clearTimeout(searchTimer));
    });
})(jQuery);
