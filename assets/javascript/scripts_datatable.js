var linguagemDataTable = {
    "lengthMenu": "_MENU_ Registros por página",
    "zeroRecords": "Nenhum registro encontrado",
    "info": "Exibindo _START_ a _END_ de _TOTAL_ registros",
    "infoEmpty": "Nenhum registro disponível",
    "infoFiltered": "(filtrado de _MAX_ registros)",
    "emptyTable": "Nenhuma informação na tabela",
    "infoPostFix": "",
    "thousands": ".",
    "decimal": ",",
    "loadingRecords": "Carregando...",
    "processing": "Processando...",
    "search": "Busca:",
    "paginate": {
        "first":      "Primeira",
        "last":       "Última",
        "next":       "Próxima",
        "previous":   "Anterior"
    },
    "aria": {
        "sortAscending":  ": activate to sort column ascending",
        "sortDescending": ": activate to sort column descending"
    }
};

// $.fn.dataTable.ext.order.intl = function ( locales, options ) {
//     if ( window.Intl ) {
//         var collator = new window.Intl.Collator( locales, options );
//         var types = $.fn.dataTable.ext.type;
 
//         delete types.order['string-pre'];
//         types.order['string-asc'] = collator.compare;
//         types.order['string-desc'] = function ( a, b ) {
//             return collator.compare( a, b ) * -1;
//         };
//     }
// };
