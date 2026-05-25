export function installAdminResponsiveSpacing() {
    if (document.getElementById('admin-responsive-spacing')) return;

    const style = document.createElement('style');
    style.id = 'admin-responsive-spacing';
    style.textContent = `
        @media (max-width: 1024px) {
            body .main-content,
            body .content {
                padding-top: 108px !important;
            }

            body .main-content > .header,
            body .content > .header {
                height: 68px !important;
                min-height: 68px !important;
            }

            body .admin-responsive-header-spacer {
                display: block !important;
                height: 34px !important;
                min-height: 34px !important;
                width: 100% !important;
                flex: 0 0 auto !important;
            }

            body .main-content > .header + *,
            body .content > .header + * {
                margin-top: 0 !important;
            }

            body .main-content .statistics-section:first-child,
            body .main-content .stats-grid:first-child,
            body .content .statistics-section:first-child,
            body .content .stats-grid:first-child {
                margin-top: 0 !important;
            }
        }

        @media (max-width: 768px) {
            body .main-content,
            body .content {
                padding-top: 112px !important;
            }

            body .admin-responsive-header-spacer {
                height: 42px !important;
                min-height: 42px !important;
            }
        }
    `;
    document.head.appendChild(style);

    const applySpacer = () => {
        if (!window.matchMedia('(max-width: 1024px)').matches) return;
        if (!document.querySelector('.mobile-bottom-menu')) return;

        document
            .querySelectorAll('.main-content > .header, .content > .header')
            .forEach((header) => {
                const next = header.nextElementSibling;
                if (next?.classList?.contains('admin-responsive-header-spacer')) {
                    return;
                }

                const spacer = document.createElement('div');
                spacer.className = 'admin-responsive-header-spacer';
                spacer.setAttribute('aria-hidden', 'true');
                header.after(spacer);
            });
    };

    applySpacer();
    window.addEventListener('resize', applySpacer);
    window.addEventListener('load', applySpacer, { once: true });
}
