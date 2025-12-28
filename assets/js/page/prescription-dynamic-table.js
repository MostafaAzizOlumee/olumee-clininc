(() => {
  'use strict';

  /* ==========================
   * DOM REFERENCES
   * ========================== */
  const table = document.querySelector('#prescription-add-table');
  const appendArea = table.querySelector('.append-area');

  /* ==========================
   * STATE & CONSTANTS
   * ========================== */
  const USAGE_FORMS = DRUG_USAGE_FORMS;
  let rowIndex = 1;
  
  let USAGE_FORM_OPTIONS = '';

  for (var key in USAGE_FORMS) {
      if (USAGE_FORMS.hasOwnProperty(key)) {
        var label = USAGE_FORMS[key];
        USAGE_FORM_OPTIONS +=
          '<option value="' + label + '">' + label + '</option>';
      }
    }
  const addRowBtn = document.getElementById('add-row');

  /* ==========================
   * EVENTS
   * ========================== */
  addRowBtn.addEventListener('click', () => addRow(true));
  document.addEventListener('click', handleRowRemove);

  /* ==========================
   * INITIAL LOAD
   * ========================== */
  addRow(false);

  /* ==========================
   * FUNCTIONS
   * ========================== */

  function addRow(autoOpenMedicine = false) {
    const rowId = rowIndex++;

    appendArea.insertAdjacentHTML('beforeend', createRowHTML(rowId));

    initMedicineSelect(`#medicines_id-${rowId}`, autoOpenMedicine);
    initUsageFormSelect(`#usage-form-${rowId}`);
  }

  function createRowHTML(rowId) {
    return `
      <tr data-row-id="${rowId}" class="align-middle">
        <td class="ps-4">
            <span class="row-id-badge">${rowId}</span>
        </td>
        <td>
          <select
            class="form-control dir-ltr select2 dynamic table-input"
            id="medicines_id-${rowId}"
            name="medicines_id[]">
          </select>
        </td>

        <td>
          <input type="text"
                 id="medicine_total_usage-${rowId}" list="medicine-total-usage-options"
                 class="form-control dir-ltr table-input text-center"
                 name="medicine_total_usage[]"
                 placeholder="مثلا 30">
        </td>

        <td>
          <input type="text" list="medicine-usage-time-options"
                 id="medicine_usage_time-${rowId}"
                 class="form-control table-input dir-ltr text-center"
                 name="medicine_usage_time[]"
                 placeholder="1x3 مثلا">
        </td>

        <td>
          <select
            class="form-control dir-ltr table-input"
            id="usage-form-${rowId}"
            name="medicine_usage_form[]">
            <option value="">انتخاب</option>
            ${USAGE_FORM_OPTIONS}
          </select>
        </td>

        <td>
          <textarea rows="1"
                    id="medicine_usage_note-${rowId}"
                    class="form-control dir-ltr table-input"
                    name="medicine_usage_note[]"
                    placeholder="..."></textarea>
        </td>
        <td class="pe-4 text-center">
            <button type="button" class="btn btn-link text-danger remove-row-btn p-0" title="Delete">
                <i class="fa-solid fa-trash-can"></i>
            </button>
        </td>
      </tr>
    `;
}

// Fixed Handle Row Remove (Search for the correct class)
function handleRowRemove(e) {
    const btn = e.target.closest('.remove-row-btn');
    if (!btn) return;

    const row = btn.closest('tr');
    if (row) {
        row.style.opacity = '0';
        row.style.transform = 'translateX(20px)';
        setTimeout(() => row.remove(), 200); // Smooth exit animation
    }
}

  /* ==========================
   * SELECT2 INITIALIZERS
   * ========================== */

  function initMedicineSelect(selector, autoOpen) {
    const $select = $(selector);
    if ($select.hasClass('select2-hidden-accessible')) return;

    $select.select2({
      language: 'fa',
      dir: 'ltr',
      placeholder: 'انتخاب دوا',
      minimumInputLength: 3,
      ajax: {
        url: 'ajax/medicine-search.php',
        type: 'POST',
        dataType: 'json',
        delay: 250,
        data: params => ({
          key: 'search_in_select2',
          search_key: params.term,
          page: params.page || 1
        }),
        processResults: data => ({
          results: data.map(item => ({
            ...item,
            text:
              item.text ||
              `${item.type} | ${item.generic_name} (${item.company_name}) ${item.dose}`
          }))
        })
      }
    });

    if (autoOpen) {
      setTimeout(() => $select.select2('open'), 0);
    }
    /* ON focus open the dropdown */
    const $container = $select.next('.select2-container');

    $container.find('.select2-selection').on('focus', function () {
      if (!$select.select2('isOpen')) {
        $select.select2('open');
      }
    });

    /* After medicine select focus medicine_total_usage input */
    $select.on('select2:select', function () {
      const row = this.closest('tr');
      const totalUsageInput = row.querySelector(
        'input[name="medicine_total_usage[]"]'
      );

      if (totalUsageInput) totalUsageInput.focus();
    });
  }

  function initUsageFormSelect(selector) {
    const $select = $(selector);
    if ($select.hasClass('select2-hidden-accessible')) return;

    $select.select2({
      language: 'fa',
      dir: 'ltr',
      placeholder: 'انتخاب',
      width: '100%'
    });

    const $container = $select.next('.select2');

    $container.find('.select2-selection').on('focus', function () {
      $select.select2('open');
    });

    // After usage form select → add new row
    $select.on('select2:select', function () {
      const row = this.closest('tr');
      const isLastRow = row === appendArea.lastElementChild;

      if (isLastRow) {
        addRow(true);
      }
    });
  }

// Change Focus to Next Input on Enter Key Press
document.addEventListener('keydown', function(e) {
    if (e.key !== 'Enter') return;

    const form = document.getElementById('prescriptionForm');
    const elements = Array.from(form.querySelectorAll('input, select, textarea, button'))
                          .filter(el => !el.disabled && el.type !== 'hidden');

    const index = elements.indexOf(e.target);
    if (index === -1) return;

    e.preventDefault(); // prevent form submission

    let next = elements[index + 1];
    if (!next) return;
    /* Skip these elements */
    if(next.id === 'add-row'){
      next = elements[index + 2];
    } 
      

    if (next.tagName === 'SELECT' && $(next).hasClass('select2-hidden-accessible')) {
        $(next).select2('open');
    } else {
        next.focus();
    }
});

// Submit Form when submit button is clicked
window.addEventListener("DOMContentLoaded", ()=>{
    document.querySelector("[name='submit_btn']").addEventListener("click", ()=>{
      // Remove empty rows before submitting
      const rows = appendArea.querySelectorAll('tr[data-row-id]');
      rows.forEach(row => {
        const inputs = row.querySelectorAll('input, select, textarea');
        const isEmpty = Array.from(inputs).every(input => {
          if (input.tagName === 'SELECT') {
            return !input.value; // empty select
          } else {
            return !input.value.trim(); // empty input/textarea
          }
        });
        if (isEmpty) row.remove();
      });
      $('#prescriptionForm').submit();
    });
});
})();

/* ====================
* Handle Double Enter on Empty Select2 to Focus Submit Button
* Focus Submit Button after pressing Enter twice on empty Select2
* For better UX when filling the form using keyboard only
===================*/
let activeSelect2 = null;
let lastEnterTime = 0;

/**
 * Track currently opened Select2
 * This gives us the EXACT <select> instance
 */
$(document).on('select2:open', function (e) {
    activeSelect2 = e.target; // original <select>
});


/**
 * Detect double Enter inside Select2 search input
 */
$(document).on('keydown', '.select2-search__field', function (e) {
    if (e.key !== 'Enter') return;

    const now = Date.now();
    const delta = now - lastEnterTime;
    lastEnterTime = now;

    if (!activeSelect2) return;

    // Only medicine select2
    if (!activeSelect2.id || !activeSelect2.id.startsWith('medicines_id-')) return;

    // Double Enter within 400ms AND nothing selected
    if (delta < 400 && !activeSelect2.value) {
        e.preventDefault();
        e.stopPropagation();

        // Close Select2
        $(activeSelect2).select2('close');

        // Focus submit button
        const submitBtn = document.querySelector("[name='submit_btn']");
        if (submitBtn) submitBtn.click();
    }
});



