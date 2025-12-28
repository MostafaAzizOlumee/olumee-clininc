$('#prescriptionForm').validate({
    ignore: [], 
    rules: {

        // Patient info
        first_name: {
            required: true,
            maxlength: 50
        },
        father_name: {
            required: true,
            maxlength: 50
        },
        last_name: {
            maxlength: 50
        },
        age: {
            required: true,
            digits: true,
            min: 0,
            max: 150
        },

        // Vitals
        bp: {
            maxlength: 10
        },
        pr: {
            maxlength: 10
        },
        rr: {
            maxlength: 10
        },
        weight: {
            number: true,
            max: 300
        },

        // Notes
        clinical_notes: {
            maxlength: 300
        },

        // Medicines (dynamic rows / arrays)
        'medicines_id[]': {
            required: true,
            digits: true,
            min: 1
        },
        'medicine_total_usage[]': {
            required: true,
            maxlength: 20
        },
        'medicine_usage_time[]': {
            required: true,
            maxlength: 20
        },
        'medicine_usage_form[]': {
            required: true,
            maxlength: 20
        },
        'medicine_usage_note[]': {
            maxlength: 100
        }
    },
        messages: {

        first_name: {
            required: 'نام بیمار الزامی میباشد.',
            maxlength: 'نام بیمار نباید بیش از ۵۰ حرف باشد.'
        },

        father_name: {
            required: 'نام پدر بیمار الزامی میباشد.',
            maxlength: 'نام پدر نباید بیش از ۵۰ حرف باشد.'
        },

        last_name: {
            maxlength: 'تخلص نباید بیش از ۵۰ حرف باشد.'
        },

        age: {
            required: 'سن بیمار الزامی میباشد.',
            digits: 'سن باید عدد صحیح باشد.',
            min: 'سن نمی‌تواند منفی باشد.',
            max: 'سن وارد شده غیر معتبر است.'
        },

        bp: {
            maxlength: 'فشار خون نباید بیش از ۱۰ حرف باشد.'
        },

        pr: {
            maxlength: 'نبض نباید بیش از ۱۰ حرف باشد.'
        },

        rr: {
            maxlength: 'تنفس نباید بیش از ۱۰ حرف باشد.'
        },

        weight: {
            number: 'وزن باید عددی باشد.',
            max: 'وزن وارد شده غیر معتبر است.'
        },

        clinical_notes: {
            maxlength: 'یادداشت‌ها نباید بیش از ۳۰۰ حرف باشد.'
        },

        'medicines_id[]': {
            required: 'حداقل یک دوا باید انتخاب شود.',
            digits: 'دوا انتخاب شده معتبر نیست.',
            min: 'دوا انتخاب شده معتبر نیست.'
        },

        'medicine_total_usage[]': {
            required: 'مقدار مجموع دوا الزامی است.',
            maxlength: 'مقدار مجموع دوا نباید بیش از ۲۰ حرف باشد.'
        },

        'medicine_usage_time[]': {
            required: 'زمان استفاده دوا الزامی است.',
            maxlength: 'زمان استفاده دوا نباید بیش از ۲۰ حرف باشد.'
        },

        'medicine_usage_form[]': {
            required: 'طریقه استفاده دوا الزامی است.',
            maxlength: 'طریقه استفاده دوا نباید بیش از ۲۰ حرف باشد.'
        },

        'medicine_usage_note[]': {
            maxlength: 'ملاحظات دوا نباید بیش از ۱۰۰ حرف باشد.'
        }
    },

    errorElement: 'div',
    errorClass: 'text-danger small mt-1'
});
