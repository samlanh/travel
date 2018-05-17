/**
 * Theme: Simple Admin Template
 * Author: Coderthemes
 * Component: Datatable
 */

$('#datatable').dataTable({"pageLength": 20});
$('#datatable-keytable').DataTable({keys: true});
$('#datatable-responsive').DataTable(
		{
			"dom": '<"top"i>rt<"bottom"flp><"clear">',
			 searching: false,
			 "pageLength": 25,
		}
);
$('#datatable-colvid').DataTable({
    "dom": 'C<"clear">lfrtip',
    "colVis": {
        "buttonText": "Change columns"
    }
});
$('#datatable-scroller').DataTable({
    ajax: "assets/plugins/datatables/json/scroller-demo.json",
    deferRender: true,
    scrollY: 380,
    scrollCollapse: true,
    scroller: true
});
//var table = $('#datatable-fixed-header').DataTable({fixedHeader: true});
var table = $('#datatable-fixed-col').DataTable({
    scrollY: "300px",
    scrollX: true,
    scrollCollapse: true,
    paging: false,
    fixedColumns: {
        leftColumns: 1,
        rightColumns: 1
    }
});

var handleDataTableButtons = function () {
        "use strict";
        0 !== $("#datatable-buttons").length && $("#datatable-buttons").DataTable({
            dom: "Bfrtip",
			"pageLength": 20,
            buttons: [{
                extend: "excel",
                className: "btn-sm"
            }],
            responsive: !0
        })
    },
    TableManageButtons = function () {
        "use strict";
        return {
            init: function () {
                handleDataTableButtons()
            }
        }
    }();
TableManageButtons.init();
