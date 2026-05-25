const RESPONSIVE_QUERY = '(max-width: 1024px)';
const PANEL_CLASS = 'mobile-search-options-panel';
const TOGGLE_CLASS = 'mobile-search-options-toggle';
const PROCESSED_ATTR = 'data-mobile-search-options';

const containerSelectors = [
    '.header-actions',
    '.header-filters',
    '.search-filters',
    '.packaging-search-area',
    '.filters-group',
    '.filters-section',
];

const fieldSelectors = [
    '.search-input',
    '.filter-search',
    '.filter-select',
    '.date-picker',
    '.packaging-search-field',
    '.packaging-filters-row',
    '.packaging-date-row',
    'input[type="search"]',
    'input[type="date"]',
    'select',
];

function isActionElement(element) {
    return element.matches(
        'button, a.btn, .btn, .btn-primary, .btn-secondary, .btn-success, .btn-print, .btn-print-header, .btn-add-stock, .btn-print-all',
    );
}

function isSearchOrFilterElement(element) {
    if (isActionElement(element)) return false;
    if (element.matches(fieldSelectors.join(','))) return true;
    return Boolean(element.querySelector(fieldSelectors.join(',')));
}

function createToggle(panel) {
    const toggle = document.createElement('button');
    toggle.type = 'button';
    toggle.className = TOGGLE_CLASS;
    toggle.setAttribute('aria-expanded', 'false');
    toggle.innerHTML = `
        <span><i class="fas fa-sliders-h"></i> Deplier les options de recherche & tri</span>
        <i class="fas fa-chevron-down"></i>
    `;

    toggle.addEventListener('click', () => {
        const isOpen = panel.classList.toggle('is-open');
        toggle.classList.toggle('is-open', isOpen);
        toggle.setAttribute('aria-expanded', String(isOpen));
    });

    return toggle;
}

function removeNestedToggles(container, keepToggle) {
    container.querySelectorAll(`.${TOGGLE_CLASS}`).forEach((toggle) => {
        if (toggle !== keepToggle) {
            toggle.remove();
        }
    });
}

function enhanceContainer(container) {
    if (container.closest(`.${PANEL_CLASS}`)) return;

    if (container.hasAttribute(PROCESSED_ATTR)) {
        const panel = container.querySelector(`.${PANEL_CLASS}`);
        if (!panel) return;

        Array.from(container.children)
            .filter((child) => isSearchOrFilterElement(child))
            .forEach((field) => panel.appendChild(field));
        removeNestedToggles(panel, container.querySelector(`.${TOGGLE_CLASS}`));
        return;
    }

    const fields = Array.from(container.children).filter((child) =>
        isSearchOrFilterElement(child),
    );

    if (!fields.length) return;

    const panel = document.createElement('div');
    panel.className = PANEL_CLASS;
    const toggle = createToggle(panel);

    container.insertBefore(toggle, fields[0]);
    toggle.after(panel);
    fields.forEach((field) => panel.appendChild(field));
    removeNestedToggles(panel, toggle);

    container.setAttribute(PROCESSED_ATTR, 'true');
}

function moveHeaderFieldsToFilterSections() {
    document.querySelectorAll('.filters-section').forEach((filtersSection) => {
        if (filtersSection.hasAttribute('data-mobile-search-merged')) return;

        const previousBlock = filtersSection.previousElementSibling;
        const headerActions = previousBlock?.querySelector?.('.header-actions');
        if (!headerActions) return;

        const fields = Array.from(headerActions.children).filter((child) =>
            isSearchOrFilterElement(child),
        );
        fields.reverse().forEach((field) => filtersSection.prepend(field));
        filtersSection.setAttribute('data-mobile-search-merged', 'true');
    });
}

function installMobileSearchOptions() {
    if (!window.matchMedia(RESPONSIVE_QUERY).matches) return;

    moveHeaderFieldsToFilterSections();

    document
        .querySelectorAll(containerSelectors.join(','))
        .forEach(enhanceContainer);

    const salesStats = document.querySelector(
        '.statistics-section + .sales-content .sales-history .page-header',
    );
    const salesContent = document.querySelector(
        '.statistics-section + .sales-content',
    );

    if (
        salesStats &&
        salesContent &&
        !salesStats.hasAttribute('data-mobile-sales-filters-moved')
    ) {
        salesContent.before(salesStats);
        salesStats.setAttribute('data-mobile-sales-filters-moved', 'true');
    }
}

function injectMobileSearchOptionsStyles() {
    if (document.getElementById('mobile-search-options-styles')) return;

    const style = document.createElement('style');
    style.id = 'mobile-search-options-styles';
    style.textContent = `
        .mobile-search-options-toggle {
            display: none;
        }

        .mobile-search-options-panel {
            display: contents;
        }

        @media (max-width: 1024px) {
            .mobile-search-options-toggle {
                width: min(86%, 340px) !important;
                margin: 6px auto 12px !important;
                flex: 0 0 auto !important;
                max-width: 340px !important;
                min-height: 46px;
                display: flex !important;
                align-items: center;
                justify-content: space-between;
                gap: 10px;
                padding: 0 14px;
                border: 0;
                border-radius: 12px;
                background: linear-gradient(135deg, #5f6fe6 0%, #7b4bb5 100%);
                color: #fff;
                font-weight: 700;
                box-shadow: 0 8px 20px rgba(102, 126, 234, 0.24);
                cursor: pointer;
            }

            .mobile-search-options-toggle span {
                min-width: 0;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }

            .mobile-search-options-toggle .fa-chevron-down {
                transition: transform 0.25s ease;
            }

            .mobile-search-options-toggle.is-open .fa-chevron-down {
                transform: rotate(180deg);
            }

            .mobile-search-options-panel {
                width: calc(100% - 24px) !important;
                max-height: 0;
                margin: 0 12px !important;
                padding: 0;
                display: grid;
                gap: 10px;
                overflow: hidden;
                opacity: 0;
                transform: translateY(-8px);
                visibility: hidden;
                pointer-events: none;
                transition: max-height 0.3s ease, opacity 0.25s ease, transform 0.25s ease, padding 0.25s ease;
            }

            .mobile-search-options-panel.is-open {
                max-height: 700px;
                padding: 4px 0 8px;
                opacity: 1;
                transform: translateY(0);
                visibility: visible;
                pointer-events: auto;
            }

            .mobile-search-options-panel > *,
            .mobile-search-options-panel input,
            .mobile-search-options-panel select {
                width: 100% !important;
                max-width: 100% !important;
                min-width: 0 !important;
                flex: 1 0 100% !important;
                box-sizing: border-box;
            }

            .mobile-search-options-panel input,
            .mobile-search-options-panel select {
                margin-left: 0 !important;
                margin-right: 0 !important;
            }
        }
    `;
    document.head.appendChild(style);
}

export function installResponsiveSearchOptions() {
    injectMobileSearchOptionsStyles();

    const run = () => installMobileSearchOptions();
    window.addEventListener('load', run, { once: true });
    window.addEventListener('resize', run);

    const observer = new MutationObserver(() => run());
    observer.observe(document.body, { childList: true, subtree: true });

    run();
}
