<?php
require_once 'bootstrap/init.php';

$prescriptionModel = new Prescription();

$perPage =  isset($_GET['per_page']) ? (int)$_GET['per_page'] : ROWS_PER_PAGE;
$currentPage = $_GET['page']   ?? 1;
$rows = $prescriptionModel->getList([
    'page'     => $currentPage,
    'per_page' => $perPage,
    'search'   => $_GET['search'] ?? '',
    'filter'   => $_GET['filter'] ?? 'all',
]);

$rowNumber  = ($currentPage - 1) * $perPage;

?>
<?php include 'inc/head.php'; ?>

<div id="wrapper">
    <?php include 'inc/aside.php'; ?>

    <div id="page-content-wrapper">
        <?php include 'inc/navbar.php'; ?>

        <div class="container-fluid px-4 py-4">
            <div class="row align-items-center mb-4" style="border-right: 5px solid #0097d7; padding-right: 15px;">
                <div class="col-md-6">
                    <h3 class="fw-bold mb-0" style="color: #0097d7;">لیست نسخه‌ها</h3>
                    <p class="text-muted small mb-0">مدیریت و مشاهده تمامی تجویزات ثبت شده</p>
                </div>
                <div class="col-md-6 text-end mt-3 mt-md-0">
                    <a href="prescription-add.php" class="btn text-white px-4 fw-600" style="background-color: #0097d7; border-radius: 8px; box-shadow: 0 4px 12px rgba(0, 151, 215, 0.25);">
                        <i class="fas fa-plus-circle ms-2"></i>ثبت نسخه جدید
                    </a>
                </div>
            </div>

            <!-- <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <div class="card card-stylish bg-gradient-sky border-0 p-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <p class="mb-0 opacity-75 small">مجموع نسخه‌ها</p>
                                <h3 class="mb-0 fw-bold">1,284</h3>
                            </div>
                            <div class="icon-circle"><i class="fas fa-file-prescription fs-4"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card card-stylish border-0 p-3 bg-white">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <p class="mb-0 text-muted small">نسخه‌های امروز</p>
                                <h3 class="mb-0 fw-bold text-dark">42</h3>
                            </div>
                            <div class="icon-circle" style="background: #e0f2fe; color: #0ea5e9;"><i class="fas fa-calendar-day fs-4"></i></div>
                        </div>
                    </div>
                </div>
            </div> -->

            <div class="card border-0 shadow-sm" style="border-radius: 15px; overflow: hidden;">
                <div class="card-header bg-white p-4 border-0">
                    <div class="row align-items-center g-3">
                       <form method="get" id="prescription-filter-form">
                            <div class="row align-items-center g-3">
                                <div class="col-lg-5">
                                    <div class="search-wrapper">
                                        <i class="fas fa-search search-icon"></i>
                                        <input type="text" name="search" class="form-control search-pill" placeholder="جستجوی هوشمند (نام بیمار، کد، یا تشخیص)..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                                        <button type="submit" class="search-btn">جستجو</button>
                                    </div>
                                </div>

                                <div class="col-lg-5">
                                    <div class="d-flex flex-wrap gap-2 justify-content-lg-start justify-content-center align-items-center">
                                        <span class="small text-muted ms-2 fw-bold">فیلتر سریع:</span>
                                        <button type="submit" name="filter" value="all" class="filter-chip <?= ($_GET['filter'] ?? 'all') === 'all' ? 'active' : '' ?>">همه</button>
                                        <button type="submit" name="filter" value="today" class="filter-chip <?= ($_GET['filter'] ?? '') === 'today' ? 'active' : '' ?>">امروز</button>
                                        <button type="submit" name="filter" value="month" class="filter-chip <?= ($_GET['filter'] ?? '') === 'month' ? 'active' : '' ?>">این ماه</button>
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <a href="prescription-list.php" class="btn btn-primary w-100 border-0 ">
                                        نمایش همه
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div><!-- card-header -->
                <div class="card-body">
                    <div class="table-responsive px-4 pb-4">
                        <table class="table align-middle" id="prescription-list-table">
                            <thead>
                                <tr class="text-muted small">
                                    <th style="width: 80px;">شماره</th>
                                    <th>کد بیمار</th>
                                    <th>نام و تخلص بیمار</th>
                                    <th>نام پدر</th>
                                    <th>سن</th>
                                    <th>تاریخ ثبت</th>
                                    <th class="text-center">عملیات</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($rows)): ?>
                                    <?php foreach ($rows as $row): ?>
                                        <?php $rowNumber++; ?>
                                        <tr>
                                            <!-- Row number -->
                                            <td class="text-center fw-bold">
                                                <?= $rowNumber ?>
                                            </td>
    
                                            <td>
                                                <span class="row-id-badge">
                                                    <?= clean_data($row['patient_code']) ?>
                                                </span>
                                            </td>
    
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div>
                                                        <h6 class="mb-0 fw-bold">
                                                            <?= clean_data($row['first_name'] . ' ' . $row['last_name']) ?>
                                                        </h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div>
                                                        <small class="text-muted">
                                                           <?= clean_data($row['father_name']) ?>
                                                        </small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                        <small class="text-muted">
                                                           <?= clean_data($row['age']) ?> ساله
                                                        </small>
                                            </td>
                                            <td>
                                                <?= date('Y-m-d', strtotime($row['created_at'])) ?>
                                            </td>
    
                                            <td class="text-center">
                                                <div class="btn-group gap-1">
                                                    <a href="prescription-print-a4.php?PID=<?= (int)$row['prescription_id'] ?>"
                                                    class="btn btn-sm btn-light border-0 text-info">
                                                        <i class="fas fa-print"></i>
                                                    </a>
    
                                                    <a href="prescription-edit.php?PID=<?= (int)$row['prescription_id'] ?>"
                                                    class="btn btn-sm btn-light border-0 text-warning">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
    
                                                    <a href="prescription-delete.php?id=<?= (int)$row['prescription_id'] ?>"
                                                    onclick="return confirm('آیا مطمئن هستید؟')"
                                                    class="btn btn-sm btn-light border-0 text-danger">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
                                            هیچ نسخه‌ای یافت نشد
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div><!-- card-body -->
                <div class="card-footer">
                    <?php
                        $prevPage   = max(1, $currentPage - 1);
                        $nextPage   = $currentPage + 1;
                    ?>
                    <nav class="d-flex justify-content-between align-items-center mt-3">
                        <a
                            class="btn btn-sm btn-primary <?= $currentPage <= 1 ? 'disabled' : '' ?>"
                            href="?page=<?= $prevPage ?>&per_page=<?= $perPage ?>&search=<?= urlencode($_GET['search'] ?? '') ?>&filter=<?= urlencode($_GET['filter'] ?? 'all') ?>">
                            → قبلی 
                        </a>

                        <a
                            class="btn btn-sm btn-primary"
                            href="?page=<?= $nextPage ?>&per_page=<?= $perPage ?>&search=<?= urlencode($_GET['search'] ?? '') ?>&filter=<?= urlencode($_GET['filter'] ?? 'all') ?>">
                            بعدی ←
                        </a>
                    </nav>
                </div><!-- card-footer -->
            </div>
        </div></div></div><?php include 'inc/footer.php'; ?>