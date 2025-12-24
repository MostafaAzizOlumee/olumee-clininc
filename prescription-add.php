<?php include 'bootstrap/init.php';
$scripts = [
    '<script> const DRUG_USAGE_FORMS = ' . json_encode(array_keys(DRUG_USAGE_FORMS)) . ';</script>',
    "<script src='assets/js/page/prescription-dynamic-table.js'></script>"
];

if( $_SERVER['REQUEST_METHOD'] === "POST" ){
    
    
    /* ===========================
    * Model Containers
    * =========================== */
    $patientModel = new Patient;
    $prescriptionModel = new Prescription;
    $objPatientPrescription = new PatientPrescription;
    $prescribedMedicineModel = new PrescribedMedicine;

    /* ===========================
    * Start Transaction
    * =========================== */
    $logs = [];
    $objPatientPrescription->startTransaction();   
    
    /* ===========================
    * (1) Insert Patient
    * =========================== */
    $logs[] = $patientModel->add([
        'code'        => isset($_POST['patient_code']) ? $_POST['patient_code'] : generatePatientCode(),
        'first_name'  => $_POST['first_name'],
        'father_name' => $_POST['father_name'] ?? null,
        'last_name'   => $_POST['last_name'] ?? null,
        'age'         => $_POST['age'] ?? null,
    ]);
    $patientId = $patientModel->lastInsertId();
    if (!$patientId) {
        $logs[] = false;
    }
    
    /* ===========================
    * (2) Insert Prescription
    * =========================== */
    $logs[] = $prescriptionModel->add([
        'patient_cc'           => $_POST['cc'] ?? null,
        'patient_past_history' => $_POST['past_history'] ?? null,
        'patient_pb'           => $_POST['bp'] ?? null,
        'patient_pr'           => $_POST['pr'] ?? null,
        'patient_rr'           => $_POST['rr'] ?? null,
        'patient_weight'       => $_POST['weight'] ?? null,
        'doctor_diagnose'      => $_POST['diagnosis'] ?? null,
        'doctor_clinical_note' => $_POST['clinical_notes'] ?? null,
        'patient_id'           => $patientId,
    ]);

    $prescriptionId = $prescriptionModel->lastInsertId();

    if (!$prescriptionId) {
        $logs[] = false;
    }

    /* ===========================
    * (3) Insert Prescribed Medicines
    * =========================== */
    if (!empty($_POST['medicines_id']) && is_array($_POST['medicines_id'])) {

        foreach ($_POST['medicines_id'] as $i => $medicineId) {

            if (empty($medicineId)) {
                continue;
            }

            $logs[] = $prescribedMedicineModel->add([
                'medicine_id'               => $medicineId,
                'medicine_total_usage'      => $_POST['medicine_total_usage'][$i],
                'medicine_usage_frequency'  => $_POST['medicine_usage_time'][$i],
                'medicine_usage_form'       => $_POST['medicine_usage_form'][$i],
                'medicine_doctor_note'      => $_POST['medicine_usage_note'][$i] ?? '',
                'prescription_id'           => $prescriptionId,
            ]);
        }
    }else{       
        $logs[] = false;
    }
    $logs[] =false;
    /* ===========================
    * Commit / Rollback
    * =========================== */
    if ($patientModel->endTransaction($logs)) {
        header("Location: prescription-print-a4.php?PID={$prescriptionId}");
        exit;
    } else {
        header("Location: prescription-add.php?msg=error");
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
                    <h3 class="fw-bold mb-0" style="color: #0097d7;">نسخه الکترونیک</h3>
                    <p class="text-muted small mb-0">ایجاد و مدیریت تجویزات طبی</p>
                </div><!-- col-12 -->
            </div><!-- row -->
            <div class="row">
                <div class="col-6">
                    <?php if( isset($_GET['msg']) && $_GET['msg'] === "error" ): ?>
                        <div class="alert alert-danger bg-danger alert-dismissible fade show mt-4" role="alert">
                            <strong>خطا!</strong> در ثبت نسخه خطایی رخ داده است. لطفاً دوباره تلاش کنید.
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                </div><!-- col-6 -->
            </div><!-- row -->
            <div class="row mt-4">
                <div class="col-12">
                    <form id="prescriptionForm" method="POST">
                        <div class="row g-4 mb-4">
                            <div class="col-lg-9">
                                <div class="card border-0 shadow-sm" style="border-radius: 10px;">

                                    <div class="card-body p-4">
                                        <div class="d-flex align-items-center mb-4 text-muted border-bottom pb-2">
                                            <i class="fas fa-user-circle ms-2"></i>
                                            <span class="fw-bold small">معلومات عمومی بیمار</span>
                                        </div>
                                        <div class="row g-3">
                                            <div class="col-md-3">
                                                <label class="small text-muted mb-1">کد بیمار</label>
                                                <input type="text" name="patient_code" class="form-control sidebar-style-input" placeholder="PT-0000">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="small text-muted mb-1">نام بیمار</label>
                                                <input type="text" autofocus name="first_name" class="form-control sidebar-style-input">
                                            </div>
                                            <div class="col-md-2">
                                                <label class="small text-muted mb-1">نام پدر</label>
                                                <input type="text" name="father_name" class="form-control sidebar-style-input">
                                            </div>
                                            <div class="col-md-2">
                                                <label class="small text-muted mb-1">تخلص</label>
                                                <input type="text" name="last_name" class="form-control sidebar-style-input">
                                            </div>
                                            <div class="col-md-2">
                                                <label class="small text-muted mb-1">سن</label>
                                                <input type="number" name="age" class="form-control sidebar-style-input">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="small fw-bold text-muted mb-2">شکایت اصلی بیمار (CC)</label>
                                                <textarea name="cc" class="form-control sidebar-style-input" rows="2" style="height: 100px;"></textarea>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="small fw-bold text-muted mb-2">تاریخچه قبلی (Past History)</label>
                                                <textarea name="past_history" class="form-control sidebar-style-input" rows="2" style="height: 100px;"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-lg-3">
                                <div class="card border-0 shadow-sm h-100" style="border-radius: 10px; background: #fcfdfe;">
                                    <div class="card-body p-4">
                                        <div class="d-flex align-items-center mb-4 text-muted border-bottom pb-2">
                                            <i class="fas fa-pulse ms-2"></i>
                                            <span class="fw-bold small">علایم حیاتی (Vitals)</span>
                                        </div>
                                        <div class="row g-2">
                                            <div class="col-6 mb-2">
                                                <div class="vitals-box">
                                                    <label>فشار خون</label>
                                                    <input type="text" name="bp" placeholder="BP">
                                                </div>
                                            </div>
                                            <div class="col-6 mb-2">
                                                <div class="vitals-box">
                                                    <label>نبض</label>
                                                    <input type="text" name="pr" placeholder="PR">
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="vitals-box">
                                                    <label>تنفس</label>
                                                    <input type="text" name="rr" placeholder="RR">
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="vitals-box">
                                                    <label>وزن (kg)</label>
                                                    <input type="text" name="weight" placeholder="KG">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                           
                            <div class="col-lg-9">
                                <div class="card border-0 shadow-sm mb-4" style="border-radius: 10px; overflow: hidden;">
                                    <div class="card-header bg-white p-4 border-0 d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0 fw-bold"><i class="fas fa-pills ms-2 text-info"></i>لیست اقلام ادویه</h6>
                                        <button type="button" tabindex="-1" class="btn btn-sm btn-light border text-primary px-3" id="add-row">+ سطر جدید</button>
                                    </div>
                                    <div class="table-responsive px-2 py-3">
                                        <table class="table" id="prescription-add-table">
                                            <thead>
                                                <tr>
                                                    <th style="width: 50px;" class="ps-4">#</th>
                                                    <th style="width: 30%;">نام دوا (Medicine)</th>
                                                    <th class="text-center">تعداد مجموعی</th>
                                                    <th class="text-center">زمان استفاده</th>
                                                    <th>طریق استفاده</th>
                                                    <th>ملاحظات اضافی</th>
                                                    <th class="text-center pe-4">عملیات</th>
                                                </tr>
                                            </thead>
                                            <tbody class="append-area">
                                                </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div><!-- col-lg-9 -->
                             <div class="col-lg-3">
                                <div class="card border-0 shadow-sm mb-4" style="border-radius: 10px;">
                                    <div class="card-body p-4">
                                        <div class="row g-4">
                                            <div class="col-md-12">
                                                <div class="diagnosis-container p-3" style="background: rgba(0, 151, 215, 0.03); border: 1px dashed #0097d7; border-radius: 8px;">
                                                    <label class="fw-bold text-dark small mb-2"><i class="fas fa-stethoscope ms-1" style="color:#0097d7;"></i> تشخیص</label>
                                                    <textarea type="text" name="diagnosis" class="form-control border-0 bg-transparent fs-6 fw-bold text-dark shadow-none p-0" placeholder="تایپ کنید..."></textarea>
                                                </div>
                                            </div>
                                        </div><!-- row -->
                                    </div><!-- card-body -->
                                </div><!-- card -->
                                <div class="card border-0 shadow-sm mb-5" style="border-radius: 10px;">
                                    <div class="card-body p-4">
                                        <div class="diagnosis-container p-3" style="background: rgba(0, 151, 215, 0.03); border: 1px dashed #0097d7; border-radius: 8px;">
                                            <label class="fw-bold text-muted small mb-3"><i class="fas fa-comment-medical ms-1"></i> یادداشت‌ها</label>
                                            <textarea name="clinical_notes" class="form-control border-0 bg-transparent fs-6 fw-bold text-dark shadow-none p-0" rows="3" placeholder="توصیه های داکتر..."></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div><!-- col-lg-4 -->
                        </div><!-- row -->
                        
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="d-flex gap-2">
                                <button type="button" name="submit_btn" class="btn text-white px-4 fw-600" style="background-color: #0097d7; border-radius: 8px; box-shadow: 0 4px 12px rgba(0, 151, 215, 0.25);">
                                    <i class="fas fa-check-circle ms-2"></i>ثبت و تایید نهایی
                                </button>
                            </div><!-- d-flex -->
                        </div><!-- d-flex -->
                    </form>
                </div><!-- col-12 -->
            </div><!-- row -->
        </div><!-- container -->
    </div><!-- page-content -->
</div><!-- wrapper -->
<?php include 'inc/footer.php'; ?>