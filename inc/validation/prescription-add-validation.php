<?php
 $validator = new GUMP();
  // define the rules for GUMP server side validator
$rules = [

    // Patient info
    'first_name'     => 'required|max_len,50',
    'father_name'    => 'required|max_len,50',
    'last_name'      => 'max_len,50',
    'age'            => 'required|integer|min_numeric,0|max_numeric,150',

    // Vitals
    'bp'             => 'max_len,10',
    'pr'             => 'max_len,10',
    'rr'             => 'max_len,10',
    'weight'         => 'numeric|max_numeric,300',

    // Notes
    'clinical_notes' => 'max_len,300',

    // Medicines (dynamic rows – arrays)
    'medicines_id'           => 'required|integer|min_numeric,1',
    'medicine_total_usage'   => 'required|max_len,20',
    'medicine_usage_time'    => 'required|max_len,20',
    'medicine_usage_form'    => 'required|max_len,20',
    'medicine_usage_note'    => 'max_len,100',
];
$messages = [
    'first_name' => [
        'required' => 'نام بیمار الزامی میباشد.',
        'max_len'  => 'نام بیمار نباید بیش از ۵۰ حرف باشد.',
    ],

    'father_name' => [
        'required' => 'نام پدر بیمار الزامی میباشد.',
        'max_len' => 'نام پدر نباید بیش از ۵۰ حرف باشد.',
    ],

    'last_name' => [
        'max_len' => 'تخلص نباید بیش از ۵۰ حرف باشد.',
    ],

    'age' => [
        'required'    => 'سن بیمار الزامی میباشد.',
        'integer'     => 'سن باید عدد صحیح باشد.',
        'min_numeric' => 'سن نمی‌تواند منفی باشد.',
        'max_numeric' => 'سن وارد شده غیر معتبر است.',
    ],

    'bp' => [
        'max_len' => 'فشار خون نباید بیش از ۱۰ حرف باشد.',
    ],

    'pr' => [
        'max_len' => 'نبض نباید بیش از ۱۰ حرف باشد.',
    ],

    'rr' => [
        'max_len' => 'تنفس نباید بیش از ۱۰ حرف باشد.',
    ],

    'weight' => [
        'numeric'     => 'وزن باید عددی باشد.',
        'max_numeric' => 'وزن وارد شده غیر معتبر است.',
    ],

    'clinical_notes' => [
        'max_len' => 'یادداشت‌ها نباید بیش از ۳۰۰ حرف باشد.',
    ],

    // Medicines
    'medicines_id' => [
        'required'    => 'حداقل یک دوا باید انتخاب شود.',
        'integer'     => 'دوا انتخاب شده معتبر نیست.',
        'min_numeric' => 'دوا انتخاب شده معتبر نیست.',
    ],

    'medicine_total_usage' => [
        'required' => 'مقدار مجموع دوا الزامی است.',
        'max_len'  => 'مقدار مجموع دوا نباید بیش از ۲۰ حرف باشد.',
    ],

    'medicine_usage_time' => [
        'required' => 'زمان استفاده دوا الزامی است.',
        'max_len'  => 'زمان استفاده دوا نباید بیش از ۲۰ حرف باشد.',
    ],

    'medicine_usage_form' => [
        'required' => 'طریقه استفاده دوا الزامی است.',
        'max_len'  => 'طریقه استفاده دوا نباید بیش از ۲۰ حرف باشد.',
    ],

    'medicine_usage_note' => [
        'max_len' => 'ملاحظات دوا نباید بیش از ۱۰۰ حرف باشد.',
    ],
];


 
  $validated = $validator->is_valid($_POST, $rules, $messages);
  $validation_msgs = [];