jQuery(document).ready(function () {
    /*
     * Give an attribute to anchor tag 'js-add' and the value of that attribute
     * to be his    parent selector which is to be copied and to be added after it.
     * For example if you provide <a  js-add=".js-field_row">text</a>
     * On click of this element will copy its parent element having class js-field_row
     * and will after it.
     * */
    jQuery(document).on('click', 'a[js-add]', function () {
        var element_to_clone_selector = jQuery(this).attr('js-add');
        var element_to_clone = jQuery(this).parents(element_to_clone_selector);
        var duplicate_element = element_to_clone.clone();
        element_to_clone.after(duplicate_element);
    });

    /*
     * Give an attribute to anchor tag 'js-remove' and the value of that attribute
     * to be his    parent selector which is to be deleted.
     * For example if you provide <a  js-remove=".js-field_row">text</a>
     * On click of this element will remove its parent element having class js-field_row
     * */
    jQuery(document).on('click', 'a[js-remove]', function () {
        var removable_element_selector = jQuery(this).attr('js-remove');
        var removable_element = jQuery(this).parents(removable_element_selector);
        //count all siblings, if its more than 1, delete the selected element.
        var total_row = removable_element.siblings(removable_element_selector).length;
        if (total_row > 0) {
            removable_element.remove();
        } else {
            alert('There should be at least one row');
        }
    });


//    jQuery("#twigztech_sticky_sortable").sortable();
//    jQuery('#twigztech_sticky_sortable').disableSelection();
//    jQuery(document).on('click', '#twigztech_sticky_sortable .close', function () {
//        alert('hi');
////        jQuery(this).parents('#twigztech_sticky_sortable').remove();
//    });
    jQuery('.color-field').wpColorPicker();
    jQuery(document).ajaxComplete(function () {
        jQuery('.color-field').wpColorPicker();
    });

});