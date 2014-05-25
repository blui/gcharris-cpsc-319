var reports = {
    init: function() {
        this.generateReport();
        this.dateRangePickers();
    },
    generateReport: function() {
        $('#reports-form').submit(function(e) {
            var $this = $(this);
            e.preventDefault();

            common.ajax('reports/ajax/generate', 'get', $this.serialize(), function(data) {
                common.content.renderEJS(common.content.ejsURL('/' + ($('#reports-form input[name="type"]:checked').val() == 1 ? 'quarterly' : 'annual')), data);
            });
        });
    },
    getOrdinal: function(n) {
        var s = ['th', 'st', 'nd', 'rd'],
            v = n % 100;
        return n + (s[(v - 20) % 10] || s[v] || s[0]);
    },
    updateDatePickers: function(skip) {
        var today = new Date();
        var dateParts = $('#reports-form-first-day').val().split('/');
        var firstDay = new Date(today.getFullYear() - 1, parseInt(dateParts[0], 10) - 1, parseInt(dateParts[1], 10));

        if (!skip) {
            if ($('#reports-form-report-type input[type="radio"]:checked').val() == '1') {
                var currentQuarter = Math.floor(((today.getMonth() - 1) / 3) + 1);
                var fDay = new Date(today.getFullYear(), 3 * (currentQuarter - 1) + (firstDay.getMonth() % 3), firstDay.getDate());
                var lDay = new Date(today.getFullYear(), 3 * (currentQuarter) + (firstDay.getMonth() % 3), firstDay.getDate() - 1);
                var quarter = (-1 * ((firstDay.getMonth() - fDay.getMonth() + (12 * (firstDay.getFullYear() - fDay.getFullYear()))) / 3)) % 4 + 1;
            } else {
                var fDay = new Date(today.getFullYear() - 1, firstDay.getMonth(), firstDay.getDate());
                var lDay = new Date(today.getFullYear(), firstDay.getMonth(), firstDay.getDate() - 1);
            }

            $('#reports-form-date-range').val(fDay.toString('MM/dd/yyyy') + ' - ' + lDay.toString('MM/dd/yyyy'));
            $('input[name="start-date"]').val(fDay.toString('MM/dd/yyyy'));
            $('input[name="end-date"]').val(lDay.toString('MM/dd/yyyy'));

            var data = $('#reports-form-date-range').data('daterangepicker');
            if (data) {
                data.startDate = fDay;
                data.endDate = lDay;
                data.updateView();
                data.updateCalendars();
            }

            var fDay2 = new Date(fDay.getFullYear(), fDay.getMonth() - 12, fDay.getDate());
            var lDay2 = new Date(lDay.getFullYear(), lDay.getMonth() - 12, lDay.getDate());

            $('#reports-form-compare-date-range').val(fDay2.toString('MM/dd/yyyy') + ' - ' + lDay2.toString('MM/dd/yyyy'));
            $('input[name="compare-start-date"]').val(fDay2.toString('MM/dd/yyyy'));
            $('input[name="compare-end-date"]').val(lDay2.toString('MM/dd/yyyy'));

            var data2 = $('#reports-form-compare-date-range').data('daterangepicker');
            if (data2) {
                data2.startDate = fDay2;
                data2.endDate = lDay2;
                data2.updateView();
                data2.updateCalendars();
            }
        } else {
            var fDay = Date.parse($('input[name="start-date"]').val(), 'MM/dd/yyyy');
            var lDay = Date.parse($('input[name="end-date"]').val(), 'MM/dd/yyyy');
            var fDay2 = Date.parse($('input[name="compare-start-date"]').val(), 'MM/dd/yyyy');
            var lDay2 = Date.parse($('input[name="compare-end-date"]').val(), 'MM/dd/yyyy');
        }

        var firstDayText = '<i>(first day of fiscal year is ' + firstDay.toString('MMM d') + ')</i>';
        $('#date-text').html((!skip ? '<span style="font-size:18px;font-family:\'Open Sans\';color:#333;">Report for ' + (currentQuarter ? this.getOrdinal(quarter) + ' Quarter of ' : '') + 'FY ' + firstDay.getFullYear() + '</span> ' + firstDayText + '<br>' : 'Report for ') + '<strong>' + fDay.toString('MMM d, yyyy') + '</strong> to <strong>' + lDay.toString('MMM d, yyyy')  + '</strong> compared with <strong>' + fDay2.toString('MMM d, yyyy') + '</strong> to <strong>' + lDay2.toString('MMM d, yyyy') + '</strong>' + (skip ? '<br>' + firstDayText : ''));
    },
    dateRangePickers: function() {
        if ($('#reports-form-date-range').length) {
            reports.updateDatePickers();
        }

        $('#reports-form-report-type input[type="radio"]').change(function(){
            reports.updateDatePickers();
        });

        $('#reports-form-date-range')
        .daterangepicker({}, function(start, end){
            $('input[name="start-date"]').val(start.toString('MM/dd/yyyy'));
            $('input[name="end-date"]').val(end.toString('MM/dd/yyyy'));
            reports.updateDatePickers(true);
        })
        .mask('99/99/9999 - 99/99/9999');

        $('#reports-form-compare-date-range')
        .daterangepicker({}, function(start, end){
            $('input[name="compare-start-date"]').val(start.toString('MM/dd/yyyy'));
            $('input[name="compare-end-date"]').val(end.toString('MM/dd/yyyy'));
            reports.updateDatePickers(true);
        })
        .mask('99/99/9999 - 99/99/9999');
        
        $('#reports-form-first-day')
        .datepicker({
            format: 'mm/dd',
            changeYear: false,
            autoclose: true
        })
        .mask('99/99')
        .change(function(){
            reports.updateDatePickers();
        });
    }
};