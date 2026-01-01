<?php include 'bootstrap/init.php';
$scripts = [
    "<script src='assets/plugins/jquery-validation/jquery.validate.min.js'></script>",
    "<script src='assets/js/page/medicine-add.js'></script>",
    "<script src='assets/js/page/validations/medicine-add-validation.js'></script>"
];
/* ===========================
* Model Containers
* =========================== */
$medicineModel = new Medicine;
$medicineTypeModel = new MedicineType;
/* List of Medicine TYpes */
$medicineTypeList = $medicineTypeModel->get([], "WHERE is_deleted = '0'");

if( $_SERVER['REQUEST_METHOD'] === "POST" ){

       $data = [
            'medicine_type_id' => $_POST['medicine_type_id'],
            'medicine_category_id' => 34,
            'usage_description' => $_POST['usage_desc'],
            'generic_name'     => $_POST['generic_name'],
            'company_name'     => $_POST['company_name'],
            'dose'             => $_POST['dose']
        ];

         $log = $medicineModel->add($data);
    if ($log) {        
        header('Location: medicine-add.php?msg=success');
        exit;
    } else {
        // DB failure
        header('Location: medicine-add.php?msg=error');
        exit;
    }
}
?>
<?php include 'inc/head.php'; ?>
<div id="wrapper">
    <?php include 'inc/aside.php'; ?>

    <div id="page-content-wrapper">
        <?php include 'inc/navbar.php'; ?>

        <div class="container-fluid px-4 py-4">
            <div class="row" style="border-right: 5px solid #0097d7; padding-right: 15px;">
                <div class="col-12">
                    <h3 class="fw-bold mb-0" style="color: #0097d7;"> ثبت دوا</h3>
                </div><!-- col-12 -->
            </div><!-- row -->
            <div class="row">
                <div class="col-6">
                    <?php if( isset($_GET['msg']) && $_GET['msg'] === "error" ): ?>
                        <div class="alert alert-danger bg-danger alert-dismissible fade show mt-4" role="alert">
                            <strong>خطا!</strong> در ثبت دوا خطایی رخ داده است. لطفاً دوباره تلاش کنید.
                            <button type="button" class="btn-close text-white" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>                    
                    <?php elseif( isset($_GET['msg']) && $_GET['msg'] === "success" ): ?>
                        <div class="alert alert-success bg-success alert-dismissible fade show mt-4" role="alert">
                            <strong>موفق!</strong>  دوا با موفقیت ثبت شد.
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                    <?php if (isset($validation_msgs)): ?>
                        <div class="alert alert-warning">
                          <h4 class="alert-heading h5">خطای اعتبار سنجی اطلاعات</h4>
                            <ul>
                                <?php foreach ($validation_msgs as $msg): ?>
                                    <li><?= $msg ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                </div><!-- col-6 -->
            </div><!-- row -->
            <div class="row mt-4">
                <div class="col-12">
                    <form id="medicineAdd" method="POST">
                        <div class="row">
                            <div class="col-md-8 mx-auto">
                                <div class="row g-4 mb-4">
                                    <div class="col-md-12">
                                        <div class="card border-0 shadow-sm" style="border-radius: 10px;">

                                            <div class="card-body p-4">
                                                <div class="d-flex align-items-center mb-4 text-muted border-bottom pb-2">
                                                    <i class="fas fa-user-circle ms-2"></i>
                                                    <span class="fw-bold small">معلومات دوا</span>
                                                </div>
                                                <div class="row g-3">
                                                    <div class="col-md-12">
                                                        <label class="small text-muted mb-1">نوع دوا</label>
                                                        <select name="medicine_type_id" class="form-select select2 sidebar-style-input">
                                                            <option value="">انتخاب نوع دوا</option>
                                                            <?php foreach ($medicineTypeList as $type): ?>
                                                                <option value="<?= $type['id'] ?>"><?= $type['name'] ?></option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    <!-- col-12 -->
                                                    <div class="col-md-12">
                                                        <label class="small text-muted mb-1">نام دوا</label>
                                                        <input type="text" autofocus name="generic_name" class="form-control dir-ltr sidebar-style-input">
                                                    </div>
                                                    <div class="col-md-12">
                                                        <label class="small text-muted mb-1">نام تجارتی</label>
                                                        <input type="text" name="company_name" class="form-control dir-ltr sidebar-style-input">
                                                    </div>
                                                    <div class="col-md-12">
                                                        <label class="small text-muted mb-1">دوز</label>
                                                        <input type="text" name="dose" class="form-control dir-ltr sidebar-style-input">
                                                    </div>
                                                    <div class="col-md-12 mt-3">
                                                        <textarea name="usage_desc" class="form-control sidebar-style-input" rows="4" placeholder="طرز استفاده"></textarea>
                                                    </div>
                                                    <div class="col-md-12 mt-3">
                                                        <button type="button" name="submit_btn" class="btn btn-primary w-100">ذخیره دوا</button>
                                                    </div>
                                                </div><!-- row -->
                                            </div><!-- card-body -->
                                        </div>
                                    </div><!-- Patient basic info -->
                                </div><!-- row -->
                        </div><!-- row -->
                        
                        
                    </form>
                </div><!-- col-12 -->
            </div><!-- row -->
        </div><!-- container -->
    </div><!-- page-content -->
</div><!-- wrapper -->
<?php include 'inc/footer.php'; ?>