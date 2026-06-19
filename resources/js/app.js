const normalizeText = (value) => (value || '').replace(/\s+/g, ' ').trim().toLowerCase();

const parseNumber = (value) => {
    const raw = (value || '').replace(/\s+/g, ' ').trim();

    if (!raw || /[a-z]/i.test(raw)) {
        return null;
    }

    const numeric = raw.replace(/[^\d,.-]/g, '');

    if (!numeric || !/\d/.test(numeric)) {
        return null;
    }

    const normalized = numeric.includes(',')
        ? numeric.replace(/\./g, '').replace(',', '.')
        : numeric.replace(/,/g, '');

    const parsed = Number(normalized);

    return Number.isFinite(parsed) ? parsed : null;
};

const compareValues = (left, right) => {
    const leftNumber = parseNumber(left);
    const rightNumber = parseNumber(right);

    if (leftNumber !== null && rightNumber !== null) {
        return leftNumber - rightNumber;
    }

    return normalizeText(left).localeCompare(normalizeText(right), 'id', {
        numeric: true,
        sensitivity: 'base',
    });
};

const getCellSortValue = (row, index) => {
    const cell = row.cells[index];

    if (!cell) {
        return '';
    }

    return cell.dataset.sort || cell.innerText || cell.textContent || '';
};

const buildDatatableControls = () => {
    const toolbar = document.createElement('div');
    toolbar.className = 'hs-datatable-toolbar';

    const searchLabel = document.createElement('label');
    searchLabel.className = 'hs-datatable-field hs-datatable-search';
    searchLabel.innerHTML = `
        <span>Cari Data</span>
        <div class="hs-datatable-input-wrap">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="search" placeholder="Cari di tabel ini...">
        </div>
    `;

    const perPageLabel = document.createElement('label');
    perPageLabel.className = 'hs-datatable-field hs-datatable-page-size';
    perPageLabel.innerHTML = `
        <span>Baris</span>
        <select>
            <option value="5">5</option>
            <option value="10" selected>10</option>
            <option value="25">25</option>
            <option value="50">50</option>
        </select>
    `;

    toolbar.append(searchLabel, perPageLabel);

    return {
        toolbar,
        searchInput: searchLabel.querySelector('input'),
        perPageSelect: perPageLabel.querySelector('select'),
    };
};

const buildDatatableFooter = () => {
    const footer = document.createElement('div');
    footer.className = 'hs-datatable-footer';

    const info = document.createElement('span');
    info.className = 'hs-datatable-info';

    const pagination = document.createElement('div');
    pagination.className = 'hs-datatable-pagination';

    const previousButton = document.createElement('button');
    previousButton.type = 'button';
    previousButton.className = 'hs-datatable-page-button';
    previousButton.innerHTML = '<i class="fa-solid fa-chevron-left"></i>';
    previousButton.setAttribute('aria-label', 'Halaman sebelumnya');

    const pageInfo = document.createElement('span');
    pageInfo.className = 'hs-datatable-page-info';

    const nextButton = document.createElement('button');
    nextButton.type = 'button';
    nextButton.className = 'hs-datatable-page-button';
    nextButton.innerHTML = '<i class="fa-solid fa-chevron-right"></i>';
    nextButton.setAttribute('aria-label', 'Halaman berikutnya');

    pagination.append(previousButton, pageInfo, nextButton);
    footer.append(info, pagination);

    return {
        footer,
        info,
        pageInfo,
        previousButton,
        nextButton,
    };
};

const initDatatable = (table) => {
    if (table.dataset.datatableInitialized === 'true') {
        return;
    }

    const thead = table.tHead;
    const tbody = table.tBodies[0];
    const headers = thead ? Array.from(thead.querySelectorAll('th')) : [];

    if (!thead || !tbody || headers.length === 0) {
        return;
    }

    table.dataset.datatableInitialized = 'true';
    table.classList.add('hs-datatable-table');

    const rows = Array.from(tbody.children).filter((child) => child.tagName === 'TR');
    const emptyRow = rows.length === 1 && rows[0].querySelector('td[colspan]');
    const dataRows = emptyRow ? [] : rows;

    dataRows.forEach((row, index) => {
        row.dataset.datatableIndex = String(index);
    });

    const tableViewport = table.parentElement && table.parentElement.classList.contains('overflow-x-auto')
        ? table.parentElement
        : table;
    const host = document.createElement('div');
    host.className = 'hs-datatable';

    tableViewport.parentNode.insertBefore(host, tableViewport);
    host.appendChild(tableViewport);

    const { toolbar, searchInput, perPageSelect } = buildDatatableControls();
    const { footer, info, pageInfo, previousButton, nextButton } = buildDatatableFooter();

    host.insertBefore(toolbar, tableViewport);
    host.appendChild(footer);

    const noResultRow = document.createElement('tr');
    const noResultCell = document.createElement('td');
    noResultCell.colSpan = headers.length;
    noResultCell.className = 'hs-datatable-empty';
    noResultCell.textContent = 'Tidak ada data yang cocok dengan pencarian.';
    noResultRow.appendChild(noResultCell);

    const state = {
        query: '',
        page: 1,
        perPage: Number(perPageSelect.value),
        sortIndex: null,
        sortDirection: 'asc',
    };

    const getFilteredRows = () => {
        if (!state.query) {
            return [...dataRows];
        }

        return dataRows.filter((row) => normalizeText(row.innerText || row.textContent).includes(state.query));
    };

    const getSortedRows = (filteredRows) => {
        if (state.sortIndex === null) {
            return filteredRows;
        }

        return [...filteredRows].sort((left, right) => {
            const compared = compareValues(
                getCellSortValue(left, state.sortIndex),
                getCellSortValue(right, state.sortIndex)
            );

            if (compared === 0) {
                return Number(left.dataset.datatableIndex) - Number(right.dataset.datatableIndex);
            }

            return state.sortDirection === 'asc' ? compared : -compared;
        });
    };

    const updateSortIndicators = () => {
        headers.forEach((header, index) => {
            const icon = header.querySelector('.hs-datatable-sort-icon i');

            header.classList.toggle('is-sorted', state.sortIndex === index);
            header.classList.toggle('is-sorted-asc', state.sortIndex === index && state.sortDirection === 'asc');
            header.classList.toggle('is-sorted-desc', state.sortIndex === index && state.sortDirection === 'desc');
            header.setAttribute(
                'aria-sort',
                state.sortIndex === index
                    ? (state.sortDirection === 'asc' ? 'ascending' : 'descending')
                    : 'none'
            );

            if (icon) {
                icon.className = state.sortIndex === index
                    ? `fa-solid ${state.sortDirection === 'asc' ? 'fa-sort-up' : 'fa-sort-down'}`
                    : 'fa-solid fa-sort';
            }
        });
    };

    const render = () => {
        const sortedRows = getSortedRows(getFilteredRows());
        const totalRows = sortedRows.length;
        const totalPages = Math.max(1, Math.ceil(totalRows / state.perPage));

        state.page = Math.min(Math.max(1, state.page), totalPages);

        const startIndex = (state.page - 1) * state.perPage;
        const visibleRows = sortedRows.slice(startIndex, startIndex + state.perPage);

        if (noResultRow.parentNode) {
            noResultRow.remove();
        }

        dataRows.forEach((row) => {
            row.hidden = true;
        });

        sortedRows.forEach((row) => {
            tbody.appendChild(row);
        });

        visibleRows.forEach((row) => {
            row.hidden = false;
        });

        if (dataRows.length > 0 && totalRows === 0) {
            tbody.appendChild(noResultRow);
        }

        if (emptyRow) {
            emptyRow.hidden = false;
        }

        const firstShown = totalRows === 0 ? 0 : startIndex + 1;
        const lastShown = Math.min(startIndex + visibleRows.length, totalRows);

        info.textContent = `Menampilkan ${firstShown}-${lastShown} dari ${totalRows} data`;
        pageInfo.textContent = `Halaman ${state.page} / ${totalPages}`;
        previousButton.disabled = state.page <= 1;
        nextButton.disabled = state.page >= totalPages;

        updateSortIndicators();
    };

    headers.forEach((header, index) => {
        const label = normalizeText(header.textContent);
        const sortable = header.dataset.sortable !== 'false' && label !== 'aksi';

        if (!sortable) {
            return;
        }

        const icon = document.createElement('span');
        icon.className = 'hs-datatable-sort-icon';
        icon.innerHTML = '<i class="fa-solid fa-sort"></i>';
        header.appendChild(icon);
        header.classList.add('hs-datatable-sortable');
        header.tabIndex = 0;
        header.setAttribute('aria-sort', 'none');

        const toggleSort = () => {
            if (state.sortIndex === index) {
                state.sortDirection = state.sortDirection === 'asc' ? 'desc' : 'asc';
            } else {
                state.sortIndex = index;
                state.sortDirection = 'asc';
            }

            state.page = 1;
            render();
        };

        header.addEventListener('click', toggleSort);
        header.addEventListener('keydown', (event) => {
            if (event.key === 'Enter' || event.key === ' ') {
                event.preventDefault();
                toggleSort();
            }
        });
    });

    searchInput.disabled = dataRows.length === 0;
    perPageSelect.disabled = dataRows.length === 0;

    searchInput.addEventListener('input', (event) => {
        state.query = normalizeText(event.target.value);
        state.page = 1;
        render();
    });

    perPageSelect.addEventListener('change', (event) => {
        state.perPage = Number(event.target.value);
        state.page = 1;
        render();
    });

    previousButton.addEventListener('click', () => {
        state.page -= 1;
        render();
    });

    nextButton.addEventListener('click', () => {
        state.page += 1;
        render();
    });

    render();
};

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('table:not([data-datatable="false"])').forEach(initDatatable);
});
