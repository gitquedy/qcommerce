/*=========================================================================================
    File Name: data-list-view.js
    Description: List View
    ----------------------------------------------------------------------------------------
    Item name: Vuexy  - Vuejs, HTML & Laravel Admin Dashboard Template
    Version: 1.0
    Author: PIXINVENT
    Author URL: http://www.themeforest.net/user/pixinvent
==========================================================================================*/

$(document).ready(function () {
  "use strict";
  var table = $(".data-list-view").DataTable({
        processing: true,
        serverSide: true,
        ajax: table_route,
        columns: columnns,
        createdRow: created_row_function,
        responsive: !1,
        columnDefs: [{
            orderable: false,
            targets: 0,
            checkboxes: {
                selectRow: !0
            },
        }],
        dom: '<"top"<"actions action-btns"B><"action-filters"lf>><"clear">rt<"bottom"<"actions">p>',
        oLanguage: {
            sLengthMenu: "_MENU_",
            sSearch: ""
        },
        aLengthMenu: aLengthMenu,
        select: {
            selector: "first-child",
            style: "multi",
        },
        bInfo: typeof BInfo !== 'undefined' ? BInfo : true,
        bFilter: typeof bFilter !== 'undefined' ? bFilter : true,
        pageLength: pageLength,
        order: [[1, 'asc']],
        buttons: buttons,
        initComplete: function(t, e) {
            $(".dt-buttons .btn").removeClass("btn-secondary")
        }
    });
  
    $(document).on('change', '.selectFilter', function() {
        table.ajax.reload();
      });

    $(document).on('keyup', '.inputSearch', function() {
        table.ajax.reload();
      });

    $(document).on("click",".confirm",function() {
      Swal.fire({
      title: $(this).data('title'),
      text: $(this).data('text'),
      type: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Confirm',
      confirmButtonClass: 'btn btn-primary',
      cancelButtonClass: 'btn btn-danger ml-1',
      input: $(this).data('input'),
      inputPlaceholder: $(this).data('placeholder'),
      inputAttributes: {
        'aria-label': $(this).data('placeholder')
      },
      buttonsStyling: false,
        }).then((result) => {
        if (result.value) {
          $.ajax({
            method: "GET",
            url: $(this).data('href'),
            data: {'input': result.value},
            dataType: "json",
            success: function(result){
              if(result.success == true){
                toastr.success(result.msg);
                table.ajax.reload();
              }else{
                if(result.msg){
                  toastr.error(result.msg);
                }
              }
            },
            error: function(jqXhr, json, errorThrown){
            console.log(jqXhr);
            console.log(json);
            console.log(errorThrown);
            toastr.error('Sorry, Something went wrong. Please try again later.');
          }
          });
        }
      });
    });

    $('.view_modal').on('hidden.bs.modal', function () {
        table.ajax.reload();
      });

    $(document).on('click', '.massAction', function(){
        var ids = [];
        if(confirm("Are you sure you want to " + $(this).html()  + " this data?"))
        {
            $('tr.selected').each(function(){
                ids.push($(this).data('id'));
            });
            if(ids.length > 0)
            {
                $.ajax({
                    url: $(this).data('action'),
                    method: "POST",
                    data: {ids:ids},
                    success:function(result)
                    {
                        if(result.success == true){
                            toastr.success(result.msg);
                        }
                        else{
                          if(result.msg){
                            toastr.error(result.msg);
                          }
                        }
                        table.ajax.reload();
                    },
                    error: function(jqXhr, json, errorThrown){
                      console.log(jqXhr);
                      console.log(json);
                      console.log(errorThrown);
                    }
                });
            }
            else
            {
                alert("Please select atleast one checkbox");
            }
        }
  });

  // To append actions dropdown before add new button
  var actionDropdown = $(".actions-dropodown")
  actionDropdown.insertBefore($(".top .actions .dt-buttons"))

  // to check and uncheck checkboxes on click of <td> tag
  // $(".data-list-view, .data-thumb-view").on("click", "tbody td", function () {
  //   var dtCheckbox = $(this).parent("tr").find(".dt-checkboxes-cell .dt-checkboxes")
  //   $(this).closest("tr").toggleClass("selected");
  //   dtCheckbox.prop("checked", !dtCheckbox.prop("checked"))
  // });

  $(".dt-checkboxes").on("click", function () {
    $(this).closest("tr").toggleClass("selected");
  });

  $(".data-list-view, .data-thumb-view").on("click", "tbody td", function () {
    var dtCheckbox = $(this).parent("tr").find(".dt-checkboxes-cell .dt-checkboxes")
    $(this).closest("tr").toggleClass("selected");
    dtCheckbox.prop("checked", !dtCheckbox.prop("checked"))
  });
  
  $(".dt-checkboxes-select-all input").on("click", function () {
    $(".data-list-view").find("tbody tr").toggleClass("selected")
    $(".data-thumb-view").find("tbody tr").toggleClass("selected")
  });
  // Scrollbar
  if ($(".data-items").length > 0) {
    new PerfectScrollbar(".data-items", { wheelPropagation: false });
  }

  // Close sidebar
  $(".hide-data-sidebar, .cancel-data-btn").on("click", function () {
    $(".add-new-data").removeClass("show");
    $(".overlay-bg").removeClass("show");
    $("#data-name, #data-price").val("");
    $("#data-category, #data-status").prop('selectedIndex', 0);
  });

  // mac chrome checkbox fix
  if (navigator.userAgent.indexOf('Mac OS X') != -1) {
    $(".dt-checkboxes-cell input, .dt-checkboxes").addClass("mac-checkbox");
  }
});
