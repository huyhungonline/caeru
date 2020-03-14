$(document).ready(function() {
    // Initialize
    var initial_company_authority = $("[name=company_wide_authority][checked]");
    var company_authority = $("[name=company_wide_authority]");
    var select_box = $("select.ms");

    // Multiple select box
    select_box.multipleSelect({
        width: 250,
        selectAll: false,
        minimumCountSelected: 2,
    });

    // Disable if company_wide_authority = true
    if(initial_company_authority.val()==1) {
        select_box.multipleSelect("disable");
    } else {
        select_box.multipleSelect("enable");
    }

    // toggle the select box base on the choice of company_wide_authority
    company_authority.change(function() {
        if($(this).val()==1) {
            select_box.multipleSelect("disable");
        } else {
            select_box.multipleSelect("enable");
        }
    });
});