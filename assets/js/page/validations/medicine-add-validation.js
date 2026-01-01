$(function () {

    // Custom rule: letters, numbers, spaces, dashes only
    $.validator.addMethod("textSafe", function (value, element) {
        return this.optional(element) || /^[a-zA-Z0-9\u0600-\u06FF\s\-]+$/.test(value);
    }, "مقدار وارد شده معتبر نیست.");

    // Custom rule: dose format (e.g. 500mg, 5 ml, 1 tablet)
    $.validator.addMethod("doseFormat", function (value, element) {
        return this.optional(element) || /^[0-9]+(\.[0-9]+)?\s?(mg|ml|g|tablet|capsule)$/i.test(value);
    }, "فرمت دوز نادرست است (مثال: 500mg، 5 ml).");

    $("#medicineAdd").validate({
        ignore: [], // IMPORTANT for select2
        errorClass: "text-danger small",
        errorElement: "div",

        rules: {
            medicine_type_id: {
                required: true,
                digits: true
            },
            generic_name: {
                required: true,
                minlength: 2,
                maxlength: 100,
                textSafe: true
            },
            company_name: {
                minlength: 2,
                maxlength: 100,
                textSafe: true
            },
            dose: {
                maxlength: 50
            },
            usage_desc: {
                minlength: 5,
                maxlength: 500
            }
        },

        messages: {
            medicine_type_id: {
                required: "لطفاً نوع دوا را انتخاب نمایید.",
                digits: "نوع دوا معتبر نیست."
            },
            generic_name: {
                required: "نام دوا الزامی است.",
                minlength: "نام دوا حداقل باید ۲ حرف باشد.",
                maxlength: "نام دوا بیش از حد طولانی است."
            },
            company_name: {
                minlength: "نام تجارتی حداقل باید ۲ حرف باشد.",
                maxlength: "نام تجارتی بیش از حد طولانی است."
            },
            dose: {
                maxlength: "دوز بیش از حد طولانی است."
            },
            usage_desc: {
                minlength: "طرز استفاده خیلی کوتاه است.",
                maxlength: "طرز استفاده بیش از حد طولانی است."
            }
        },

        highlight: function (element) {
            $(element).addClass("is-invalid");
        },

        unhighlight: function (element) {
            $(element).removeClass("is-invalid");
        },

        errorPlacement: function (error, element) {
            if (element.hasClass("select2")) {
                error.insertAfter(element.next('.select2-container'));
            } else {
                error.insertAfter(element);
            }
        }
    });

});
