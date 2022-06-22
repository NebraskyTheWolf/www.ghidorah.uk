/*
=== GLOBAL ===
*/
if (($('body.front').length))
{
    $('.ui.menu.navbar').clone().insertBefore('.pusher').addClass('inverted vertical masthead sidebar')
    $(document).ready(function () {
        $('.dropdown').dropdown({on: 'hover'})
        $('.ui.checkbox').checkbox()
    })
}

function getURLParameter(name) {
  return decodeURIComponent((new RegExp('[?|&]' + name + '=' + '([^&;]+?)(&|#|;|$)').exec(location.search) || [null, ''])[1].replace(/\+/g, '%20')) || null;
}

function nFormatter(num, digits) {
  var si = [
    { value: 1E18, symbol: "E" },
    { value: 1E15, symbol: "P" },
    { value: 1E12, symbol: "T" },
    { value: 1E9,  symbol: "G" },
    { value: 1E6,  symbol: "M" },
    { value: 1E3,  symbol: "k" }
  ], rx = /\.0+$|(\.[0-9]*[1-9])0+$/, i;
  for (i = 0; i < si.length; i++) {
    if (num >= si[i].value) {
      return (num / si[i].value).toFixed(digits).replace(rx, "$1") + si[i].symbol;
    }
  }
  return num.toFixed(digits).replace(rx, "$1");
}

/*
  === CSRF ===
*/
$.ajaxSetup({
  headers: {
    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  }
})

var datatableLang = {
    "sProcessing":     "Processing in progress...",
    "sSearch":         "Search&nbsp;:",
    "sLengthMenu":     "Display _MENU_ &eacute;l&eacute;ments",
    "sInfo":           "Display of the &eacute;l&eacute;ment _START_ &agrave; _END_ sur _TOTAL_ &eacute;l&eacute;ments",
    "sInfoEmpty":      "Display of the &eacute;l&eacute;ment 0 &agrave; 0 sur 0 &eacute;l&eacute;ment",
    "sInfoFiltered":   "(filtr&eacute; de _MAX_ &eacute;l&eacute;ments in total)",
    "sInfoPostFix":    "",
    "sLoadingRecords": "Loading in progress...",
    "sZeroRecords":    "None display",
    "sEmptyTable":     "None available in the table",
    "oPaginate": {
        "sFirst":      "First",
        "sPrevious":   "Previous",
        "sNext":       "Next",
        "sLast":       "Last"
    },
    "oAria": {
        "sSortAscending":  ": activate to sort the column in ascending order",
        "sSortDescending": ": activate to sort the column by decreasing order"
    }
}