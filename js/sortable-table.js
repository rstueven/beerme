/**
 * Table sorting: http://stackoverflow.com/questions/3160277/jquery-table-sort/19947532#19947532
 * To use:
 *   Mark your sortable column headers (th) with the 'table-sortable' class.
 *   Mark your sortable rows (tr) with the 'table-sortable' class.
 *   Mark any trailing rows (tr) with the 'table-sortable-footer' class. They won't get sorted.
 */

TableSortable = {};

TableSortable.init = function () {
  $('th.table-sortable').click(function () {
    const $table = $(this).parents('table').eq(0);
    let $rows = $table.find('tr.table-sortable').toArray().sort(TableSortable.comparer($(this).index()));
    this.asc = !this.asc;
    if (!this.asc) {
      $rows = $rows.reverse();
    }
    for (let i = 0; i < $rows.length; i++) {
      $table.append($rows[i]);
    }
    $table.append($table.find('tr.table-sortable-footer'));
  });
}

// TODO: Allow for strings wrapped in html, dates.
TableSortable.comparer = function (index) {
  return function (a, b) {
    const valA = TableSortable.getCellValue(a, index);
    const valB = TableSortable.getCellValue(b, index);
    return $.isNumeric(valA) && $.isNumeric(valB) ? valA - valB : valA.localeCompare(valB);
  }
};

TableSortable.getCellValue = function (row, index) {
  return $(row).children('td').eq(index).text().trim();
};