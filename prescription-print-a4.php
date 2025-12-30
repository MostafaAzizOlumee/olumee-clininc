<?php
    require_once 'bootstrap/init.php';

    // Models
    $prescriptionModel = new PatientPrescription();
    $medicineModel     = new PrescribedMedicine();
    
    // Prescription ID
    if (!isset($_GET['PID']) || empty($_GET['PID'])) {
        header("Location: precription-list.php?msg=error"); die;
    }
    $prescriptionId = (int)$_GET['PID'];
    
     /* Fetch header data */
    $prescription = $prescriptionModel->findFullPrescription($prescriptionId);

    if ( empty($prescription) ) {
        header("Location: precription-list.php?msg=error"); die;
    }

    /* Fetch medicines */
    $medicinesResult = $medicineModel->getByPrescription($prescriptionId);

    if ( empty($medicinesResult) ) {
        header("Location: precription-list.php?msg=error"); die;
    }

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dr. Olumee | Professional Prescription</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;700&family=Playfair+Display:ital,wght@1,600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Katibeh&family=Noto+Naskh+Arabic:wght@400;700&family=Inter:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/prescription-print.css">
</head>
<body>

    <div class="prescription-sheet">
        <header class="signature-header">
            <div class="header-accent"></div>

            <div class="sig-en">
                <h2>Abdul Aziz Olumee, MD</h2>
                <p>Urology & Genital Surgery</p>
            </div>
            <div class="sig-line"></div>
            <div class="sig-fa">
                <img class="w-100" src="assets/imgs/prescription-print-fa-title.png" alt="doctor title">
                <p class="fa-title">متخصص جراحی گرده، مثانه و تناسلی</p>

            </div><!-- sig-fa -->
        </header>


        <section class="patient-grid">
            <div class="p-item">
                <strong>NAME</strong>
                <span class="fa-font text-capitalize">
                    <?= clean_data($prescription['first_name']) ?? ""?> 
                    <?= clean_data($prescription['father_name']) ?? ""?> 
                    <?= clean_data($prescription['last_name']) ?? ""?>
                </span>
            </div><!-- p-item -->
            <div class="p-item"><strong>AGE</strong><span><?=  clean_data($prescription['age']) ?></span></div>
            <div class="p-item"><strong>Patient ID</strong><span><?=  clean_data($prescription['patient_code']) ?></span></div>
            <div class="p-item"><strong>DATE</strong><span><?=  date('d M Y', strtotime(clean_data($prescription['created_at']))) ?></span></div>
        </section>

        <main class="rx-content">
            <?php if (
                        !empty($prescription['patient_pb']) 
                     || !empty($prescription['patient_pr']) 
                     || !empty($prescription['patient_weight']) 
                     || !empty($prescription['patient_rr'])
                    ): 
            ?>
                <div class="vital-and-diagnose">
                    <div class="vitals-strip">
                        <?php if (!empty($prescription['patient_pb'])): ?>
                            <div class="v-cell">BP <span><?= clean_data($prescription['patient_pb']) ?></span></div>
                        <?php endif; ?>
                        <?php if (!empty($prescription['patient_pr'])): ?> 
                            <div class="v-cell">PR <span><?= clean_data($prescription['patient_pr']) ?> bpm</span></div>
                        <?php endif; ?>
                        <?php if (!empty($prescription['patient_weight'])): ?>
                            <div class="v-cell">WT <span><?= clean_data($prescription['patient_weight']) ?> kg</span></div>
                        <?php endif; ?>
                        <?php if (!empty($prescription['patient_rr'])): ?>
                            <div class="v-cell">RR <span><?= clean_data($prescription['patient_rr']) ?>/min</span></div>
                        <?php endif; ?>
                    </div>
                    <div class="diagnose-strip">
                        <div class="v-cell font-weight-normal">DIAGNOSE <span><?= nl2br(clean_data($prescription['doctor_diagnose'])) ?></span></div>
                    </div><!-- vitals-strip -->
                </div><!-- vital-and-diagnose -->
                
            <?php endif; ?>

            <div class="rx-watermark">Rx</div>

            <table class="med-table">
                <thead>
                    <tr>
                        <th width="5%">#</th>
                        <th width="55%">MEDICATION & STRENGTH</th>
                        <th width="20%" class="text-center">QTY</th>
                        <th width="20%" class="text-center">INSTRUCTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $counter = 1; 
                        foreach($medicinesResult as $medicine): 
                    ?>
                    <tr>
                        <td><?= str_pad($counter++, 2, '0', STR_PAD_LEFT) ?></td>
                        <td>
                            <strong class="text-capitalize">
                                <?= clean_data($medicine['medicine_type']) ?>.
                                <?= clean_data($medicine['generic_name']) ?>
                                <?= clean_data($medicine['dose']) ?>
                            </strong>
                            <p class="my-0 dir-rtl">
                                <small><?= clean_data($medicine['medicine_doctor_note']) ?></small>
                            </p>
                        </td>
                        <td class="text-center"><?= (int)$medicine['medicine_total_usage'] ?></td>
                        <td class="text-center">
                            <span class="badge-primary"><?= clean_data($medicine['medicine_usage_frequency']) ?></span> |
                            <span class="badge-primary"><?= clean_data($medicine['medicine_usage_form']) ?></span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="notes-area dir-ltr" style="direction: rtl !important;">
                <label>Clinical Notes 
                <span class="fa-font">
                    (یادداشت کلینیکی)

                </span>
            </label>
                <p class="fa-font"><?= nl2br(clean_data($prescription['doctor_clinical_note'])) ?></p>
            </div>
        </main>

        <footer class="slim-footer">
            <div class="legal">
                <div class="address">
                    <img src="assets/imgs/icons/address.svg" alt="address icon">
                    <span class="fa-font">مارکت حضرت‌ها، طبق دوم، حنت دواخانه حمایت</span>
                </div>
                <div class="phone">
                    <img src="assets/imgs/icons/phone.svg" alt="phone icon">
                    <span class="fa-font">0799750303</span>
                </div>
            </div>
            <div class="signature">
                <div class="sig-box"></div>
                <p>Doctor's Signature</p>
            </div>
        </footer>
    </div><!-- prescription-sheet -->

    <button class="btn-print" autofocus onclick="window.print()">Print A4/A5 پرنت</button>

    <a class="btn btn-primary btn-back" href="prescription-add.php">بازگشت </a>
</body>
</html>