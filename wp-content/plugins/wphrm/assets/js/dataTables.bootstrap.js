/* Set the defaults for DataTables initialisation */
jQuery.extend(true, jQuery.fn.dataTable.defaults, {
    "dom": "<'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'f>r><'table-scrollable't><'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>", // default layout with horizobtal scrollable datatable
    //"dom": "<'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'f>r>t<'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>", // datatable layout without  horizobtal scroll(used when bootstrap dropdowns used in the datatable cells)
    "language": {
        "lengthMenu": " _MENU_ records ",
        "paginate": {
            "previous": '<i class="fa fa-angle-left"></i>',
            "next": '<i class="fa fa-angle-right"></i>'
        }
    }
});
/* Default class modification */
jQuery.extend(jQuery.fn.dataTableExt.oStdClasses, {
    "sWrapper": "dataTables_wrapper",
    "sFilterInput": "form-control input-small input-inline",
    "sLengthSelect": "form-control input-xsmall input-inline"
});
// In 1.10 we use the pagination renderers to draw the Bootstrap paging,
// rather than  custom plug-in
jQuery.fn.dataTable.defaults.renderer = 'bootstrap';
jQuery.fn.dataTable.ext.renderer.pageButton.bootstrap = function (settings, host, idx, buttons, page, pages) {
    var api = new jQuery.fn.dataTable.Api(settings);
    var classes = settings.oClasses;
    var lang = settings.oLanguage.oPaginate;
    var btnDisplay, btnClass;
    var attach = function (container, buttons) {
        var i, ien, node, button;
        var clickHandler = function (e) {
            e.preventDefault();
            if (e.data.action !== 'ellipsis') {
                api.page(e.data.action).draw(false);
            }
        };
        for (i = 0, ien = buttons.length; i < ien; i++) {
            button = buttons[i];
            if (jQuery.isArray(button)) {
                attach(container, button);
            } else {
                btnDisplay = '';
                btnClass = '';
                switch (button) {
                case 'ellipsis':
                    btnDisplay = '&hellip;';
                    btnClass = 'disabled';
                    break;
                case 'first':
                    btnDisplay = lang.sFirst;
                    btnClass = button + (page > 0 ?
                        '' : ' disabled');
                    break;
                case 'previous':
                    btnDisplay = lang.sPrevious;
                    btnClass = button + (page > 0 ?
                        '' : ' disabled');
                    break;
                case 'next':
                    btnDisplay = lang.sNext;
                    btnClass = button + (page < pages - 1 ?
                        '' : ' disabled');
                    break;
                case 'last':
                    btnDisplay = lang.sLast;
                    btnClass = button + (page < pages - 1 ?
                        '' : ' disabled');
                    break;
                default:
                    btnDisplay = button + 1;
                    btnClass = page === button ?
                        'active' : '';
                    break;
                }
                if (btnDisplay) {
                    node = jQuery('<li>', {
                        'class': classes.sPageButton + ' ' + btnClass,
                        'aria-controls': settings.sTableId,
                        'tabindex': settings.iTabIndex,
                        'id': idx === 0 && typeof button === 'string' ?
                            settings.sTableId + '_' + button : null
                    })
                        .append(jQuery('<a>', {
                                'href': '#'
                            })
                            .html(btnDisplay)
                    )
                        .appendTo(container);
                    settings.oApi._fnBindAction(
                        node, {
                            action: button
                        }, clickHandler
                    );
                }
            }
        }
    };
    attach(
        jQuery(host).empty().html('<ul class="pagination"/>').children('ul'),
        buttons
    );
}
/*
 * TableTools Bootstrap compatibility
 * Required TableTools 2.1+
 */
if (jQuery.fn.DataTable.TableTools) {
    // Set the classes that TableTools uses to something suitable for Bootstrap
    jQuery.extend(true, jQuery.fn.DataTable.TableTools.classes, {
        "container": "DTTT btn-group",
        "buttons": {
            "normal": "btn btn-default",
            "disabled": "disabled"
        },
        "collection": {
            "container": "DTTT_dropdown dropdown-menu",
            "buttons": {
                "normal": "",
                "disabled": "disabled"
            }
        },
        "print": {
            "info": "DTTT_Print_Info"  
        },
        "select": {
            "row": "active"
        }
    });
    // Have the collection use a bootstrap compatible dropdown
   jQuery.extend(true, jQuery.fn.DataTable.TableTools.DEFAULTS.oTags, {
        "collection": {
            "container": "ul",
            "button": "li",
            "liner": "a"
        }
    });
}
/***
Custom Pagination
***/
/* API method to get paging information */
jQuery.fn.dataTableExt.oApi.fnPagingInfo = function (oSettings) {
    return {
        "iStart": oSettings._iDisplayStart,
        "iEnd": oSettings.fnDisplayEnd(),
        "iLength": oSettings._iDisplayLength,
        "iTotal": oSettings.fnRecordsTotal(),
        "iFilteredTotal": oSettings.fnRecordsDisplay(),
        "iPage": oSettings._iDisplayLength === -1 ?
            0 : Math.ceil(oSettings._iDisplayStart / oSettings._iDisplayLength),
        "iTotalPages": oSettings._iDisplayLength === -1 ?
            0 : Math.ceil(oSettings.fnRecordsDisplay() / oSettings._iDisplayLength)
    };
};
/* Bootstrap style full number pagination control */
jQuery.extend(jQuery.fn.dataTableExt.oPagination, {
    "bootstrap_full_number": {
        "fnInit": function (oSettings, nPaging, fnDraw) {
            var oLang = oSettings.oLanguage.oPaginate;
            var fnClickHandler = function (e) {
                e.preventDefault();
                if (oSettings.oApi._fnPageChange(oSettings, e.data.action)) {
                    fnDraw(oSettings);
                }
            };
            jQuery(nPaging).append(
                '<ul class="pagination">' +
                '<li class="prev disabled"><a href="#" title="' + oLang.sFirst + '"><i class="fa fa-angle-double-left"></i></a></li>' +
                '<li class="prev disabled"><a href="#" title="' + oLang.sPrevious + '"><i class="fa fa-angle-left"></i></a></li>' +
                '<li class="next disabled"><a href="#" title="' + oLang.sNext + '"><i class="fa fa-angle-right"></i></a></li>' +
                '<li class="next disabled"><a href="#" title="' + oLang.sLast + '"><i class="fa fa-angle-double-right"></i></a></li>' +
                '</ul>'
            );
            var els = jQuery('a', nPaging);
            jQuery(els[0]).bind('click.DT', {
                action: "first"
            }, fnClickHandler);
            jQuery(els[1]).bind('click.DT', {
                action: "previous"
            }, fnClickHandler);
            jQuery(els[2]).bind('click.DT', {
                action: "next"
            }, fnClickHandler);
            jQuery(els[3]).bind('click.DT', {
                action: "last"
            }, fnClickHandler);
        },
        "fnUpdate": function (oSettings, fnDraw) {
            var iListLength = 5;
            var oPaging = oSettings.oInstance.fnPagingInfo();
            var an = oSettings.aanFeatures.p;
            var i, j, sClass, iStart, iEnd, iHalf = Math.floor(iListLength / 2);
            if (oPaging.iTotalPages < iListLength) {
                iStart = 1;
                iEnd = oPaging.iTotalPages;
            } else if (oPaging.iPage <= iHalf) {
                iStart = 1;
                iEnd = iListLength;
            } else if (oPaging.iPage >= (oPaging.iTotalPages - iHalf)) {
                iStart = oPaging.iTotalPages - iListLength + 1;
                iEnd = oPaging.iTotalPages;
            } else {
                iStart = oPaging.iPage - iHalf + 1;
                iEnd = iStart + iListLength - 1;
            }
            for (i = 0, iLen = an.length; i < iLen; i++) {
                if (oPaging.iTotalPages <= 0) {
                    jQuery('.pagination', an[i]).css('visibility', 'hidden');
                } else {
                    jQuery('.pagination', an[i]).css('visibility', 'visible');
                }
                // Remove the middle elements
                jQuery('li:gt(1)', an[i]).filter(':not(.next)').remove();
                // Add the new list items and their event handlers
                for (j = iStart; j <= iEnd; j++) {
                    sClass = (j == oPaging.iPage + 1) ? 'class="active"' : '';
                    jQuery('<li ' + sClass + '><a href="#">' + j + '</a></li>')
                        .insertBefore(jQuery('li.next:first', an[i])[0])
                        .bind('click', function (e) {
                            e.preventDefault();
                            oSettings._iDisplayStart = (parseInt(jQuery('a', this).text(), 10) - 1) * oPaging.iLength;
                            fnDraw(oSettings);
                        });
                }
                // Add / remove disabled classes from the static elements
                if (oPaging.iPage === 0) {
                    jQuery('li.prev', an[i]).addClass('disabled');
                } else {
                    jQuery('li.prev', an[i]).removeClass('disabled');
                }
                if (oPaging.iPage === oPaging.iTotalPages - 1 || oPaging.iTotalPages === 0) {
                    jQuery('li.next', an[i]).addClass('disabled');
                } else {
                    jQuery('li.next', an[i]).removeClass('disabled');
                }
            }
        }
    }
});
/* Bootstrap style full number pagination control */
jQuery.extend(jQuery.fn.dataTableExt.oPagination, {
    "bootstrap_extended": {
        "fnInit": function (oSettings, nPaging, fnDraw) {
            var oLang = oSettings.oLanguage.oPaginate;
            var oPaging = oSettings.oInstance.fnPagingInfo();
            var fnClickHandler = function (e) {
                e.preventDefault();
                if (oSettings.oApi._fnPageChange(oSettings, e.data.action)) {
                    fnDraw(oSettings);
                }
            };
            jQuery(nPaging).append(
                '<div class="pagination-panel"> ' + oLang.page + ' ' +
                '<a href="#" class="btn btn-sm default prev disabled" title="' + oLang.previous + '"><i class="fa fa-angle-left"></i></a>' +
                '<input type="text" class="pagination-panel-input form-control input-mini input-inline input-sm" maxlenght="5" style="text-align:center; margin: 0 5px;">' +
                '<a href="#" class="btn btn-sm default next disabled" title="' + oLang.next + '"><i class="fa fa-angle-right"></i></a> ' +
                oLang.pageOf + ' <span class="pagination-panel-total"></span>' +
                '</div>'
            );
            var els = jQuery('a', nPaging);
            jQuery(els[0]).bind('click.DT', {
                action: "previous"
            }, fnClickHandler);
           jQuery(els[1]).bind('click.DT', {
                action: "next"
            }, fnClickHandler);
            jQuery('.pagination-panel-input', nPaging).bind('change.DT', function (e) {
                var oPaging = oSettings.oInstance.fnPagingInfo();
                e.preventDefault();
                var page = parseInt(jQuery(this).val());
                if (page > 0 && page <= oPaging.iTotalPages) {
                    if (oSettings.oApi._fnPageChange(oSettings, page - 1)) {
                        fnDraw(oSettings);
                    }
                } else {
                    jQuery(this).val(oPaging.iPage + 1);
                }
            });
            jQuery('.pagination-panel-input', nPaging).bind('keypress.DT', function (e) {
                var oPaging = oSettings.oInstance.fnPagingInfo();
                if (e.which == 13) {
                    var page = parseInt(jQuery(this).val());
                    if (page > 0 && page <= oSettings.oInstance.fnPagingInfo().iTotalPages) {
                        if (oSettings.oApi._fnPageChange(oSettings, page - 1)) {
                            fnDraw(oSettings);
                        }
                    } else {
                        jQuery(this).val(oPaging.iPage + 1);
                    }
                    e.preventDefault();
                }
            });
        },
        "fnUpdate": function (oSettings, fnDraw) {
            var iListLength = 5;
            var oPaging = oSettings.oInstance.fnPagingInfo();
            var an = oSettings.aanFeatures.p;
            var i, j, sClass, iStart, iEnd, iHalf = Math.floor(iListLength / 2);
            if (oPaging.iTotalPages < iListLength) {
                iStart = 1;
                iEnd = oPaging.iTotalPages;
            } else if (oPaging.iPage <= iHalf) {
                iStart = 1;
                iEnd = iListLength;
            } else if (oPaging.iPage >= (oPaging.iTotalPages - iHalf)) {
                iStart = oPaging.iTotalPages - iListLength + 1;
                iEnd = oPaging.iTotalPages;
            } else {
                iStart = oPaging.iPage - iHalf + 1;
                iEnd = iStart + iListLength - 1;
            }
            for (i = 0, iLen = an.length; i < iLen; i++) {
                var wrapper = jQuery(an[i]).parents(".dataTables_wrapper");
                if (oPaging.iTotal <= 0) {
                    jQuery('.dataTables_paginate, .dataTables_length', wrapper).hide();
                } else {
                    jQuery('.dataTables_paginate, .dataTables_length', wrapper).show();
                }
                if (oPaging.iTotalPages <= 0) {
                    jQuery('.dataTables_paginate, .dataTables_length .seperator', wrapper).hide();
                } else {
                    jQuery('.dataTables_paginate, .dataTables_length .seperator', wrapper).show();
                }
                jQuery('.pagination-panel-total', an[i]).html(oPaging.iTotalPages);
                jQuery('.pagination-panel-input', an[i]).val(oPaging.iPage + 1);
                // Remove the middle elements
                jQuery('li:gt(1)', an[i]).filter(':not(.next)').remove();
                // Add the new list items and their event handlers
                for (j = iStart; j <= iEnd; j++) {
                    sClass = (j == oPaging.iPage + 1) ? 'class="active"' : '';
                    jQuery('<li ' + sClass + '><a href="#">' + j + '</a></li>')
                        .insertBefore(jQuery('li.next:first', an[i])[0])
                        .bind('click', function (e) {
                            e.preventDefault();
                            oSettings._iDisplayStart = (parseInt(jQuery('a', this).text(), 10) - 1) * oPaging.iLength;
                            fnDraw(oSettings);
                        });
                }
                // Add / remove disabled classes from the static elements
                if (oPaging.iPage === 0) {
                    jQuery('a.prev', an[i]).addClass('disabled');
                } else {
                    jQuery('a.prev', an[i]).removeClass('disabled');
                }
                if (oPaging.iPage === oPaging.iTotalPages - 1 || oPaging.iTotalPages === 0) {
                    jQuery('a.next', an[i]).addClass('disabled');
                } else {
                    jQuery('a.next', an[i]).removeClass('disabled');
                }
            }
        }
    }
});