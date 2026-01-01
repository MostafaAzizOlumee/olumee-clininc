<?php include 'bootstrap/init.php';
$scripts = [
    "<script src='assets/plugins/jquery-validation/jquery.validate.min.js'></script>",
    "<script src='assets/js/page/medicine-edit.js'></script>",
    "<script src='assets/js/page/validations/medicine-add-validation.js'></script>"
];

/* ===========================
 * Model Containers
 * =========================== */
$medicineModel     = new Medicine;
$medicineTypeModel = new MedicineType;

/* ===========================
 * Validate Medicine ID
 * =========================== */
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: medicine-list.php?msg=invalid');
    exit;
}

$medicine_id = (int) $_GET['id'];

/* ===========================
 * Fetch Medicine
 * =========================== */
$medicine = $medicineModel->get([], "WHERE id = {$medicine_id} AND is_deleted = 0");

if (!$medicine) {
    header('Location: medicine-list.php?msg=not_found');
    exit;
}else{
    $medicine = mysqli_fetch_assoc($medicine);
}

/* ===========================
 * Medicine Types
 * =========================== */
$medicineTypeList = $medicineTypeModel->get([], "WHERE is_deleted = 0");

/* ===========================
 *UPDATE
 * =========================== */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $data = [
        'medicine_type_id'     => $_POST['medicine_type_id'],
        'usage_description'    => $_POST['usage_desc'],
        'generic_name'         => $_POST['generic_name'],
        'company_name'         => $_POST['company_name'],
        'dose'                 => $_POST['dose'],
    ];

    $updated = $medicineModel->update($data, "id = {$medicine_id}");

    if ($updated) {
        header("Location: medicine-edit.php?id={$medicine_id}&msg=success");
        exit;
    } else {
        header("Location: medicine-edit.php?id={$medicine_id}&msg=error");
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
                    <h3 class="fw-bold mb-0" style="color: #0097d7;">نسخه دوا</h3>
                </div><!-- col-12 -->
            </div><!-- row -->
            <div class="row justify-content-center">
                <div class="col-6">
                    <?php if( isset($_GET['msg']) && $_GET['msg'] === "error" ): ?>
                        <div class="alert alert-danger bg-danger alert-dismissible fade show mt-4" role="alert">
                            <strong>خطا!</strong> در ثبت نسخه خطایی رخ داده است. لطفاً دوباره تلاش کنید.
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php elseif( isset($_GET['msg']) && $_GET['msg'] === "success" ): ?>
                        <div class="alert alert-success bg-success alert-dismissible fade show mt-4" role="alert">
                            <strong>موفق!</strong> دوا با موفقیت ویرایش شد.
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
                                                        <select name="medicine_type_id" class="form-select select2">
                                                            <option value="">انتخاب نوع دوا</option>
                                                            <?php foreach ($medicineTypeList as $type): ?>
                                                                <option value="<?= $type['id'] ?>"
                                                                    <?= $type['id'] == $medicine['medicine_type_id'] ? 'selected' : '' ?>>
                                                                    <?= $type['name'] ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    <!-- col-12 -->
                                                    <div class="col-md-12">
                                                        <label class="small text-muted mb-1">نام دوا</label>
                                                        <input type="text" name="generic_name"
                                                            value="<?= clean_data($medicine['generic_name']) ?>"
                                                            class="form-control dir-ltr">   
                                                    </div>
                                                    <div class="col-md-12">
                                                        <label class="small text-muted mb-1">نام تجارتی</label>
                                                        <input type="text" name="company_name"
                                                            value="<?= clean_data($medicine['company_name']) ?>"
                                                            class="form-control dir-ltr">
                                                    </div>
                                                    <div class="col-md-12">
                                                        <label class="small text-muted mb-1">دوز</label>
                                                        <input type="text" name="dose"
                                                        value="<?= clean_data($medicine['dose']) ?>"
                                                        class="form-control dir-ltr">
                                                    </div>
                                                    <div class="col-md-12 mt-3">
                                                        <textarea name="usage_desc" rows="4"
                                                            class="form-control"><?= clean_data($medicine['usage_description']) ?></textarea>

                                                    </div>
                                                    <div class="col-md-12 mt-3">
                                                        <button type="button" name="submit_btn" class="btn btn-primary w-100">ویرایش دوا</button>
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