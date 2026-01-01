
// Submit Form when submit button is clicked
window.addEventListener("DOMContentLoaded", ()=>{
    document.querySelector("[name='submit_btn']").addEventListener("click", ()=>{
      $('#medicineAdd').submit();
    });
});

const $select = $("[name=medicine_type_id]");
if (!$select.hasClass('select2-hidden-accessible') ){

    
    $select.select2({
            language: 'fa',
            dir: 'ltr',
            placeholder: 'انتخاب',
            width: '100%',
        });
}
/* ON Enter Key Press */
$('#medicineAdd').on('keydown', function (e) {
    if (e.key === 'Enter') {
        e.preventDefault();
    }
});


function focusNextField(current) {
    const focusable = $('#medicineAdd')
        .find('input, select, textarea, button')
        .filter(':visible:not([disabled])');

    if(current.tagName === 'BUTTON'){
        current.click();
        return;
    }

    const index = focusable.index(current);
    if (index > -1 && index + 1 < focusable.length) {
        focusable.eq(index + 1).focus();
    }

}

$('#medicineAdd').on('keydown', 'input, textarea, button', function (e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        focusNextField(this);
    }
});

$select.on('select2:select', function () {
    focusNextField(this);
});

$select.on('select2:close', function () {
    focusNextField(this);
});