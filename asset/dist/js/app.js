(function ($) {
    'use strict';

    $(function () {
        initMobileLayout();
        applyAccessiblePlaceholders();

        if (!window.matchMedia('(max-width: 767.98px)').matches) {
            $('.ops-card, .stat-card, .action-card').each(function (index) {
                $(this).css({
                    opacity: 0,
                    transform: 'translateY(12px)',
                }).delay(index * 45).animate(
                    { opacity: 1 },
                    {
                        duration: 320,
                        step: function (now) {
                            var progress = now;
                            $(this).css('transform', 'translateY(' + (12 * (1 - progress)) + 'px)');
                        },
                        complete: function () {
                            $(this).css('transform', '');
                        },
                    }
                );
            });
        }

        $(document).on('change', '.custom-file-input', function () {
            var fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').addClass('selected').html(fileName || 'Choose file');
        });

        if ($.fn.DataTable) {
            $('.data_table1').each(function () {
                if ($.fn.dataTable.isDataTable(this)) {
                    return;
                }

                $(this).DataTable({
                    autoWidth: false,
                    scrollX: true,
                    scrollCollapse: true,
                    stateSave: true,
                    order: [],
                    pageLength: 25,
                    language: {
                        search: '',
                        searchPlaceholder: 'Search records...',
                        lengthMenu: 'Show _MENU_ entries',
                    },
                    dom: '<"ops-dt-toolbar"lf>rt<"ops-dt-footer"ip>',
                });
            });
        }

        initReportHub();
    });

    function initMobileLayout() {
        var $body = $('body');

        function syncSidebar() {
            if (window.innerWidth < 992) {
                $body.addClass('sidebar-collapse');
            } else {
                $body.removeClass('sidebar-collapse');
            }
        }

        syncSidebar();
        $(window).on('resize', syncSidebar);
    }

    function cleanLabelText(text) {
        return $.trim(String(text || '')
            .replace(/\*/g, '')
            .replace(/:/g, '')
            .replace(/\s+/g, ' '));
    }

    function applyAccessiblePlaceholders() {
        $('form label[for]').each(function () {
            var $label = $(this);
            var labelText = cleanLabelText($label.text());
            var targetId = $label.attr('for');
            var $field = $('#' + targetId);

            if (!labelText || !$field.length || $field.is('[type="file"], [type="checkbox"], [type="radio"]')) {
                return;
            }

            if (!$field.attr('aria-label')) {
                $field.attr('aria-label', labelText);
            }

            if ($field.is('input, textarea') && !$field.attr('placeholder')) {
                $field.attr('placeholder', labelText);
            }

            if ($field.is('select')) {
                var $firstOption = $field.find('option:first');
                if ($firstOption.length && cleanLabelText($firstOption.text()) === '') {
                    $firstOption.text(labelText);
                }
            }
        });
    }

    function formatReportDate(date) {
        var month = String(date.getMonth() + 1).padStart(2, '0');
        var day = String(date.getDate()).padStart(2, '0');
        return date.getFullYear() + '-' + month + '-' + day;
    }

    function initReportHub() {
        var $hub = $('.report-hub');
        if (!$hub.length) {
            return;
        }

        var filterMap = {};
        try {
            filterMap = JSON.parse($hub.attr('data-filter-map') || '{}');
        } catch (error) {
            filterMap = {};
        }

        var exportBase = $hub.data('export-base') || '';
        var reportsBase = $hub.data('reports-base') || '';
        var defaultType = $hub.data('active-type') || 'daily-visits';
        var rowCount = parseInt($hub.data('row-count'), 10) || 0;

        var $form = $('#reportForm');
        var $typeInput = $('#reportType');
        var $description = $('#reportTypeDescription');
        var $hint = $('#reportActiveHint');
        var $title = $('#reportResultsTitle');
        var $exportToggle = $('#reportExportToggle');

        function currentType() {
            return $typeInput.val() || defaultType;
        }

        function filtersForType(type) {
            return filterMap[type] || { date: true, search: false, period: false, limit: false };
        }

        function applyFilterVisibility(type) {
            var filters = filtersForType(type);

            $hub.find('[data-filter-group]').each(function () {
                var $group = $(this);
                var group = $group.data('filter-group');
                var show = Boolean(filters[group]);
                $group.toggleClass('is-visible', show).attr('aria-hidden', show ? 'false' : 'true');
                $group.find('input, select').prop('disabled', !show);
            });
        }

        function setActiveTypeCard(type) {
            $('.report-type-card').each(function () {
                var $card = $(this);
                var active = $card.data('type') === type;
                $card.toggleClass('is-active', active).attr('aria-selected', active ? 'true' : 'false');
            });
        }

        function updateExportLinks() {
            var type = currentType();
            var query = $form.serialize();

            $('.report-export-link').each(function () {
                var format = $(this).data('format');
                $(this).attr(
                    'href',
                    exportBase + '/' + encodeURIComponent(type) + '/' + encodeURIComponent(format) + '?' + query
                );
            });

            $exportToggle.prop('disabled', rowCount === 0);
        }

        function selectReportType(type, submitForm) {
            var $card = $('.report-type-card[data-type="' + type + '"]');
            if (!$card.length) {
                return;
            }

            $typeInput.val(type);
            setActiveTypeCard(type);
            applyFilterVisibility(type);

            $description.text($card.data('description') || '');
            $hint.text($card.data('label') || '');
            $title.text($card.data('label') || 'Results');
            updateExportLinks();

            if (submitForm) {
                $form.trigger('submit');
            }
        }

        $('.report-type-card').on('click', function () {
            selectReportType($(this).data('type'), true);
        });

        $('.report-range-btn').on('click', function () {
            if (!filtersForType(currentType()).date) {
                return;
            }

            var range = $(this).data('range');
            var end = new Date();
            var start = new Date();

            if (range === 'week') {
                var weekday = end.getDay();
                var mondayOffset = weekday === 0 ? -6 : 1 - weekday;
                start.setDate(end.getDate() + mondayOffset);
            } else if (range === 'month') {
                start = new Date(end.getFullYear(), end.getMonth(), 1);
                end = new Date(end.getFullYear(), end.getMonth() + 1, 0);
            } else if (range === 'last30') {
                start.setDate(end.getDate() - 29);
            }

            $('#start_date').val(formatReportDate(start));
            $('#end_date').val(formatReportDate(end));
            $('.report-range-btn').removeClass('active');
            $(this).addClass('active');
            updateExportLinks();
        });

        $('#reportResetBtn').on('click', function () {
            window.location.href = reportsBase + '?type=' + encodeURIComponent(defaultType);
        });

        $form.on('submit', function () {
            $('#reportRunBtn')
                .prop('disabled', true)
                .html('<i class="fas fa-spinner fa-spin mr-1"></i> Running...');
            $('#reportResultsCard').addClass('is-loading');
        });

        $form.on('change input', 'input, select', function () {
            updateExportLinks();
        });

        applyFilterVisibility(currentType());
        updateExportLinks();
    }
})(jQuery);
