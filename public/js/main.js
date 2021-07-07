let table = null;
let newStModal = null;
let editCntrModal = null;
let editStModal = null;

function format ( d ) {
    let response = "<h4>States</h4>";
    response += '<table cellpadding="5" class="table table-hover table-bordered">'+
    '<thead><th>Id</th><th>Name</th><th>Code</th><th>Action</th></thead><tbody>';
    $.each(d.state, function (key, value){
        response +='<tr><td>'+ value.id +'</td><td>'+ value.name +'</td><td>'+ value.code +'</td>' +
            '<td><button type="button" data-id="'+ value.id +'" data-name="'+ value.name +'" data-code="'+ value.code +'" ' +
            'class="btn btn-primary st_action_edit me-2">Edit</button>' +
            '<button type="button" data-id="'+ value.id +'" ' +
            'class="btn btn-danger st_action_delete">Delete</button></td></tr>';
    })
    response +='</tbody></table>';
    return response;
}

$(document).ready(function() {

    init_table();
    // Add event listener for opening and closing details
    $('#ajax-table tbody').on('click', 'td.details-control', function () {
        var tr = $(this).closest('tr');
        var row = table.row( tr );

        if ( row.child.isShown() ) {
            // This row is already open - close it
            row.child.hide();
            tr.removeClass('shown');
        }
        else {
            // Open this row
            row.child( format(row.data()) ).show();
            tr.addClass('shown');
        }
    } );

    $('#ajax-table tbody').on('click', '.action_new', function () {
        var tr = $(this).closest('tr');
        var row = table.row( tr );
        newState(row.data())
    });

    $('#ajax-table tbody').on('click', '.action_edit', function () {
        var tr = $(this).closest('tr');
        var row = table.row( tr );
        editCntr(row.data())
    });

    $('#ajax-table tbody').on('click', '.action_delete', function () {
        var tr = $(this).closest('tr');
        var row = table.row( tr );
        var data = row.data();
        deleteCntr(data.id);
    });

    $('#ajax-table tbody').on('click', '.st_action_delete', function () {
        deleteState($(this).data('id'))
    })

    $('#ajax-table tbody').on('click', '.st_action_edit', function () {
        editState($(this).data('id'), $(this).data('name'), $(this).data('code'));
    });

    $('#btn_search_cntr').on('click', function(){
        init_table(true, $('#search_country').val());
    });

    $('#btn_search_st').on('click', function(){
        init_table(false, $('#search_state').val());
    });

    $('#save_new_st').on('click', function(){
        newStateSave();
    });

    $('#save_edit_cntr').on('click', function(){
        updateCountry();
    });

    $('#save_edit_st').on('click', function(){
        updateState();
    });

    $('#btn_new_country').on('click', function(){
        newCountry();
    });


    newStModal = new bootstrap.Modal(document.getElementById('new_state_modal'), {
        keyboard: false
    });

    editCntrModal = new bootstrap.Modal(document.getElementById('edit_cntr_modal'), {
        keyboard: false
    });

    editStModal = new bootstrap.Modal(document.getElementById('editStModel'), {
        keyboard: false
    });

} );

function init_table(country = true, filter = '') {
    if(filter != ''){
        filter = '?filter='+filter;
    }

    let url = '/api/country';
    if(!country){
        url = '/api/state'
    }

    table = $('#ajax-table').DataTable( {
        "ajax": {
            url: url+filter,
            error: function (data) {
                alert(data.responseJSON.filter ? data.responseJSON.filter[0] : 'Fail to fetch data');
                return false;
            }
        },
        "paging":   false,
        "ordering": false,
        "searching": false,
        "destroy": true,
        "columns": [
            {
                "className":      'details-control font-weight-bold',
                "orderable":      false,
                "data":           null,
                "defaultContent": '<span style="cursor:pointer">+</span>'
            },
            { "data": "id" },
            { "data": "name" },
            { "data": "code" },
            {
                "data": null,
                "defaultContent": '<button type="button" class="btn btn-success action_new me-2">New State</button>' +
                    '<button type="button" class="btn btn-primary action_edit me-2">Edit</button>' +
                    '<button type="button" class="btn btn-danger action_delete">Delete</button>'
            }
        ],
    } );
}

function newState(data){
    $('#modal_new_state_cntr_id').val(data.id);
    $('#new_st_name').val('');
    $('#new_st_code').val('');
    newStModal.show();
}

function editCntr(data){
    $('#modal_edit_cntr_id').val(data.id);
    $('#edit_cntr_name').val(data.name);
    $('#edit_cntr_code').val(data.code);
    editCntrModal.show();
}

function editState(id, name, code){
    $('#modal_edit_st_id').val(id);
    $('#edit_st_name').val(name);
    $('#edit_st_code').val(code);
    editStModal.show();
}

function newStateSave(){
    $.ajax({
        url: '/api/country/'+ $('#modal_new_state_cntr_id').val() +'/state',
        dataType: "json",
        type: "POST",
        data: {
            name: $('#new_st_name').val(),
            code: $('#new_st_code').val()
        },
        success: function (data) {
            alert('Success')
            location.reload();
        },
        error: function (xhr, exception) {
            var msg = "";
            if (xhr.status === 0) {
                msg = "Not connect.\n Verify Network." + xhr.responseText;
            } else if (xhr.status == 404) {
                msg = "Requested page not found. [404]" + xhr.responseText;
            } else if (xhr.status == 500) {
                msg = "Internal Server Error [500]." +  xhr.responseText;
            } else if (exception === "parsererror") {
                msg = "Requested JSON parse failed.";
            } else if (exception === "timeout") {
                msg = "Time out error." + xhr.responseText;
            } else if (exception === "abort") {
                msg = "Ajax request aborted.";
            } else {
                msg = xhr.responseJSON[0];
            }

            alert(msg);

        }
    });
}

function updateCountry(){
    $.ajax({
        url: '/api/country/'+ $('#modal_edit_cntr_id').val(),
        dataType: "json",
        type: "PUT",
        data: {
            name: $('#edit_cntr_name').val(),
            code: $('#edit_cntr_code').val()
        },
        success: function (data) {
            alert('Success')
            location.reload();
        },
        error: function (xhr, exception) {
            var msg = "";
            if (xhr.status === 0) {
                msg = "Not connect.\n Verify Network." + xhr.responseText;
            } else if (xhr.status == 404) {
                msg = "Requested page not found. [404]" + xhr.responseText;
            } else if (xhr.status == 500) {
                msg = "Internal Server Error [500]." +  xhr.responseText;
            } else if (exception === "parsererror") {
                msg = "Requested JSON parse failed.";
            } else if (exception === "timeout") {
                msg = "Time out error." + xhr.responseText;
            } else if (exception === "abort") {
                msg = "Ajax request aborted.";
            } else {
                msg = xhr.responseJSON[0];
            }

            alert(msg);

        }
    });
}

function deleteCntr(id){
    $.ajax({
        url: '/api/country/'+id,
        dataType: "json",
        type: "DELETE",
        success: function (data) {
            alert('Success')
            location.reload();
        },
        error: function (xhr, exception) {
            var msg = "";
            if (xhr.status === 0) {
                msg = "Not connect.\n Verify Network." + xhr.responseText;
            } else if (xhr.status == 404) {
                msg = "Requested page not found. [404]" + xhr.responseText;
            } else if (xhr.status == 500) {
                msg = "Internal Server Error [500]." +  xhr.responseText;
            } else if (exception === "parsererror") {
                msg = "Requested JSON parse failed.";
            } else if (exception === "timeout") {
                msg = "Time out error." + xhr.responseText;
            } else if (exception === "abort") {
                msg = "Ajax request aborted.";
            } else {
                msg = xhr.responseJSON[0];
            }

            alert(msg);

        }
    });
}

function updateState(){
    $.ajax({
        url: '/api/state/'+ $('#modal_edit_st_id').val(),
        dataType: "json",
        type: "PUT",
        data: {
            name: $('#edit_st_name').val(),
            code: $('#edit_st_code').val()
        },
        success: function (data) {
            alert('Success')
            location.reload();
        },
        error: function (xhr, exception) {
            var msg = "";
            if (xhr.status === 0) {
                msg = "Not connect.\n Verify Network." + xhr.responseText;
            } else if (xhr.status == 404) {
                msg = "Requested page not found. [404]" + xhr.responseText;
            } else if (xhr.status == 500) {
                msg = "Internal Server Error [500]." +  xhr.responseText;
            } else if (exception === "parsererror") {
                msg = "Requested JSON parse failed.";
            } else if (exception === "timeout") {
                msg = "Time out error." + xhr.responseText;
            } else if (exception === "abort") {
                msg = "Ajax request aborted.";
            } else {
                msg = xhr.responseJSON[0];
            }

            alert(msg);

        }
    });
}

function deleteState(id){
    $.ajax({
        url: '/api/state/'+id,
        dataType: "json",
        type: "DELETE",
        success: function (data) {
            alert('Success')
            location.reload();
        },
        error: function (xhr, exception) {
            var msg = "";
            if (xhr.status === 0) {
                msg = "Not connect.\n Verify Network." + xhr.responseText;
            } else if (xhr.status == 404) {
                msg = "Requested page not found. [404]" + xhr.responseText;
            } else if (xhr.status == 500) {
                msg = "Internal Server Error [500]." +  xhr.responseText;
            } else if (exception === "parsererror") {
                msg = "Requested JSON parse failed.";
            } else if (exception === "timeout") {
                msg = "Time out error." + xhr.responseText;
            } else if (exception === "abort") {
                msg = "Ajax request aborted.";
            } else {
                msg = xhr.responseJSON[0];
            }

            alert(msg);

        }
    });
}

function newCountry(){
    $.ajax({
        url: '/api/country',
        dataType: "json",
        type: "POST",
        data: {
            name: $('#new_country_name').val(),
            code: $('#new_country_code').val()
        },
        success: function (data) {
            alert('Success')
            location.reload();
        },
        error: function (xhr, exception) {
            var msg = "";
            if (xhr.status === 0) {
                msg = "Not connect.\n Verify Network." + xhr.responseText;
            } else if (xhr.status == 404) {
                msg = "Requested page not found. [404]" + xhr.responseText;
            } else if (xhr.status == 500) {
                msg = "Internal Server Error [500]." +  xhr.responseText;
            } else if (exception === "parsererror") {
                msg = "Requested JSON parse failed.";
            } else if (exception === "timeout") {
                msg = "Time out error." + xhr.responseText;
            } else if (exception === "abort") {
                msg = "Ajax request aborted.";
            } else {
                msg = xhr.responseJSON[0];
            }

            alert(msg);

        }
    });
}
