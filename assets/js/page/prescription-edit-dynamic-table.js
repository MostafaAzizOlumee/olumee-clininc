document.addEventListener('DOMContentLoaded', () => {
  'use strict';

  /* ==========================
   * DOM REFERENCES
   * ========================== */
  // Ensure this ID matches your PHP HTML: <table id="prescription-edit-table">
  const table = document.querySelector('#prescription-edit-table');
  
  // Safety check: if table is not found, stop the script to prevent errors
  if (!table) {
      console.error('Error: Table with ID #prescription-edit-table not found.');
      return;
  }

  const appendArea = table.querySelector('.append-area');
  console.log(appendArea)
  const addRowBtn = document.getElementById('add-row');

  /* ==========================
   * STATE & CONSTANTS
   * ========================== */
  const USAGE_FORMS = (typeof DRUG_USAGE_FORMS !== 'undefined') ? DRUG_USAGE_FORMS : {};
  let rowIndex = 1;
  
  let USAGE_FORM_OPTIONS = '';
  for (var key in USAGE_FORMS) {
      if (USAGE_FORMS.hasOwnProperty(key)) {
        var label = USAGE_FORMS[key];
        USAGE_FORM_OPTIONS += `<option value="${key}">${key} (${label})</option>`;
      }
  }

  /* ==========================
   * EVENTS
   * ========================== */
  if(addRowBtn){
      addRowBtn.addEventListener('click', () => addRow(true));
  }
  
  // Use event delegation on the Tbody/AppendArea, not document (better performance)
  if(appendArea) {
      appendArea.addEventListener('click', handleRowRemove);
  }

  /* ==========================
   * INITIAL LOAD LOGIC
   * ========================== */
  // Check if PRE_FILLED_MEDICINES exists (passed from PHP)
  if (typeof PRE_FILLED_MEDICINES !== 'undefined' && PRE_FILLED_MEDICINES !== null && PRE_FILLED_MEDICINES.length > 0) {
      // Loop through existing data
      PRE_FILLED_MEDICINES.forEach((med) => {
          addExistingRow(med);
      });
  } else {
      // Fallback: No data, add one empty row
      addRow(false);
  }

  /* ==========================
   * FUNCTIONS
   * ========================== */

  function addRow(autoOpenMedicine = false) {
    const rowId = rowIndex++;
    appendArea.insertAdjacentHTML('beforeend', createRowHTML(rowId));
    initMedicineSelect(`#medicines_id-${rowId}`, autoOpenMedicine);
    initUsageFormSelect(`#usage-form-${rowId}`);
  }

  function addExistingRow(data) {
    const rowId = rowIndex++;
    
    // 1. Append the HTML row
    appendArea.insertAdjacentHTML('beforeend', createRowHTML(rowId));

    // 2. Initialize Select2 for Medicine
    const medSelectSelector = `#medicines_id-${rowId}`;
    initMedicineSelect(medSelectSelector, false);

    // 3. Pre-select the medicine
    const $medSelect = $(medSelectSelector);
    
    // Construct display text safely
    const displayText = data.text || `${data.medicine_type || ''} | ${data.company_name || ''} (${data.generic_name || ''}) ${data.dose || ''}`;
    
    const option = new Option(displayText, data.medicine_id, true, true);
    $medSelect.append(option).trigger('change');

    // 4. Fill other inputs
    const row = appendArea.querySelector(`tr[data-row-id="${rowId}"]`);
    if(row) {
        // Map database columns to inputs
        const inputTotal = row.querySelector(`input[name="medicine_total_usage[]"]`);
        if(inputTotal) inputTotal.value = data.medicine_total_usage || data.total_usage || '';

        const inputFreq = row.querySelector(`input[name="medicine_usage_time[]"]`);
        if(inputFreq) inputFreq.value = data.medicine_usage_frequency || data.usage_frequency || '';

        const selectForm = row.querySelector(`select[name="medicine_usage_form[]"]`);
        if(selectForm) selectForm.value = data.medicine_usage_form || data.usage_form || '';

        const textNote = row.querySelector(`textarea[name="medicine_usage_note[]"]`);
        if(textNote) textNote.value = data.medicine_doctor_note || data.doctor_note || '';
    }

    // 5. Initialize Usage Form Select2
    initUsageFormSelect(`#usage-form-${rowId}`);
  }

  function createRowHTML(rowId) {
    return `
      <tr data-row-id="${rowId}" class="align-middle">
        <td class="ps-4">
            <span class="row-id-badge">${rowId}</span>
        </td>
        <td>
          <div class="medicine-select-wrapper">
              <select class="form-control dir-ltr select2 dynamic table-input"
                id="medicines_id-${rowId}"
                name="medicines_id[]"
                style="width: 100%"> 
              </select>
          </div>
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
          <select class="form-control dir-ltr table-input"
            id="usage-form-${rowId}"
            name="medicine_usage_form[]">
            <option value="">انتخاب</option>
            ${USAGE_FORM_OPTIONS}
          </select>
        </td>
        <td>
          <textarea rows="1"
                    id="medicine_usage_note-${rowId}"
                    class="form-control dir-rtl table-input"
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

  function handleRowRemove(e) {
    const btn = e.target.closest('.remove-row-btn');
    if (!btn) return;

    const row = btn.closest('tr');
    if (row) {
        row.style.opacity = '0';
        row.style.transform = 'translateX(20px)';
        setTimeout(() => row.remove(), 200); 
    }
  }

  /* ==========================
   * SELECT2 INITIALIZERS
   * ========================== */

  function initMedicineSelect(selector, autoOpen) {
    const $select = $(selector);
    if ($select.length === 0 || $select.hasClass('select2-hidden-accessible')) return;

    $select.select2({
      language: 'fa',
      dir: 'ltr',
      placeholder: 'انتخاب دوا',
      minimumInputLength: 3,
      dropdownParent: $('body'), 
      width: '100%',
      ajax:{
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
            text: item.text || `${item.type} | ${item.company_name} (${item.generic_name})  ${item.dose}`
          }))
        })
      }
    });

    if (autoOpen) {
      setTimeout(() => $select.select2('open'), 0);
    }

    const $container = $select.next('.select2-container');
    if($container.length) {
        $container.find('.select2-selection').on('focus', function () {
          if (!$select.select2('isOpen')) {
            $select.select2('open');
          }
        });
    }

    $select.on('select2:select', function (e) {
      const data = e.params.data; 
      const row = this.closest('tr');
      if (!row || !data) return;

      const usageNoteInput = row.querySelector('textarea[name="medicine_usage_note[]"]');
      if (usageNoteInput) {
        usageNoteInput.value = data.usage_description || '';
      }

      const totalUsageInput = row.querySelector('input[name="medicine_total_usage[]"]');
      if (totalUsageInput) totalUsageInput.focus();
    });
  }

  function initUsageFormSelect(selector) {
    const $select = $(selector);
    if ($select.length === 0 || $select.hasClass('select2-hidden-accessible')) return;

    $select.select2({
      language: 'fa',
      dir: 'ltr',
      placeholder: 'انتخاب',
      width: '100%'
    });

    const $container = $select.next('.select2');
    if($container.length){
        $container.find('.select2-selection').on('focus', function () {
          $select.select2('open');
        });
    }

    $select.on('select2:select', function () {
      const row = this.closest('tr');
      const isLastRow = row === appendArea.lastElementChild;
      if (isLastRow) {
        addRow(true);
      }
    });
  }

  // Handle Form Submission
  const submitBtn = document.querySelector("[name='submit_btn']");
  if(submitBtn) {
      submitBtn.addEventListener("click", ()=>{
        const rows = appendArea.querySelectorAll('tr[data-row-id]');
        rows.forEach(row => {
          const inputs = row.querySelectorAll('input, select, textarea');
          const isEmpty = Array.from(inputs).every(input => {
            if (input.tagName === 'SELECT') {
              return !input.value; 
            } else {
              return !input.value.trim(); 
            }
          });
          if (isEmpty) row.remove();
        });
        $('#prescriptionForm').submit();
      });
  }

  // Keyboard navigation
  document.addEventListener('keydown', function(e) {
    if (e.key !== 'Enter') return;
    const form = document.getElementById('prescriptionForm');
    if(!form) return;
    
    const elements = Array.from(form.querySelectorAll('input, select, textarea, button'))
                          .filter(el => !el.disabled && el.type !== 'hidden');

    const index = elements.indexOf(e.target);
    if (index === -1) return;
    e.preventDefault(); 

    let next = elements[index + 1];
    if (!next) return;
    if(next.id === 'add-row'){
      next = elements[index + 2];
    } 

    if (next.tagName === 'SELECT' && $(next).hasClass('select2-hidden-accessible')) {
        $(next).select2('open');
    } else {
        next.focus();
    }
  });

});

/* ==========================
 * GLOBAL HELPERS (Outside DOMContentLoaded)
 * ========================== */

// Select2 Double Enter logic
let activeSelect2 = null;
let lastEnterTime = 0;

$(document).on('select2:open', function (e) {
    activeSelect2 = e.target; 
});

$(document).on('keydown', '.select2-search__field', function (e) {
    if (e.key !== 'Enter') return;
    const now = Date.now();
    const delta = now - lastEnterTime;
    lastEnterTime = now;
    if (!activeSelect2) return;
    if (!activeSelect2.id || !activeSelect2.id.startsWith('medicines_id-')) return;
    if (delta < 400 && !activeSelect2.value) {
        e.preventDefault();
        e.stopPropagation();
        $(activeSelect2).select2('close');
        const submitBtn = document.querySelector("[name='submit_btn']");
        if (submitBtn) submitBtn.click();
    }
});