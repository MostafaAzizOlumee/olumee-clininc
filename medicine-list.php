<?php
require_once 'bootstrap/init.php';

$objMedicine = new Medicine();

$perPage =  isset($_GET['per_page']) ? (int)$_GET['per_page'] : ROWS_PER_PAGE;
$currentPage = $_GET['page']?? 1;

$rows = $objMedicine->getList([
    'page'     => $currentPage,
    'per_page' => $perPage,
    'search'   => $_GET['search'] ?? '',
]);

$rowNumber  = ($currentPage - 1) * $perPage;

if (isset($_GET['delete'])) {
    $medicineId = (int)$_GET['delete'];
    if ($objMedicine->update(['is_deleted' => 1], 'id = ' . $medicineId)) {
        header('Location: medicine-list.php?msg=success'); die;
    } else {
        header('Location: medicine-list.php?msg=error'); die;
    }
}
?>
<?php include 'inc/head.php'; ?>

<div id="wrapper">
    <?php include 'inc/aside.php'; ?>

    <div id="page-content-wrapper">
        <?php include 'inc/navbar.php'; ?>

        <div class="container-fluid px-4 py-4">
            <div class="row align-items-center mb-4" style="border-right: 5px solid #0097d7; padding-right: 15px;">
                <div class="col-md-6">
                    <h3 class="fw-bold mb-0" style="color: #0097d7;">لیست دواها</h3>
                </div>
                <div class="col-md-6 text-end mt-3 mt-md-0">
                    <a href="medicine-add.php" class="btn text-white px-4 fw-600" style="background-color: #0097d7; border-radius: 8px; box-shadow: 0 4px 12px rgba(0, 151, 215, 0.25);">
                        <i class="fas fa-plus-circle ms-2"></i>ثبت دوا جدید
                    </a>
                </div>
            </div>
            <div class="row">
                <div class="col-6">
                    <?php if (isset($_GET['msg']) && $_GET['msg'] === 'success'): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            عملیات با موفقیت انجام شد.
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php elseif (isset($_GET['msg']) && $_GET['msg'] === 'error'): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            خطایی در انجام عملیات رخ داد. لطفاً دوباره تلاش کنید.
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="card border-0 shadow-sm" style="border-radius: 15px; overflow: hidden;">
                <div class="card-header bg-white p-4 border-0">
                    <div class="row align-items-center g-3">
                       <form method="get" id="medicine-filter-form">
                            <div class="row align-items-center g-3">
                                <div class="col-lg-5">
                                    <div class="search-wrapper">
                                        <i class="fas fa-search search-icon"></i>
                                        <input type="text" name="search" class="form-control search-pill" placeholder="جستجوی هوشمند (نام دوا، نام کمپنی، دوز)..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                                        <button type="submit" class="search-btn">جستجو</button>
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <a href="medicine-list.php" class="btn btn-primary w-100 border-0 ">
                                        نمایش همه
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div><!-- card-header -->
                <div class="card-body">
                    <div class="table-responsive px-4 pb-4">
                        <table class="table align-middle" id="medicine-list-table">
                            <thead>
                                <tr class="text-muted small">
                                    <th style="width: 80px;">شماره</th>
                                    <th>نوعیت </th>
                                    <th>نام دوا</th>
                                    <th>نام تجارتی</th>
                                    <th>دوز</th>
                                    <th>کتگوری</th>
                                    <th>نحوه استفاده</th>
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
                                                <h6 class="mb-0 fw-bold">
                                                    <?= clean_data($row['type']) ?>
                                                </h6>
                                            </td>
                                            <td>
                                                <span class="row-id-badge">
                                                    <?= clean_data($row['generic_name']) ?>
                                                </span>
                                            </td>
    
                                            <td>
                                                <?= clean_data($row['company_name']) ?>
                                            </td>
                                            <td>
                                                <?= clean_data($row['dose']) ?> 
                                            </td>
                                            <td>
                                                <?= clean_data($row['category']) ?> 
                                            </td>
                                            <td>
                                                <?= clean_data($row['usage_description']) ?> 
                                            </td>
    
                                            <td class="text-center">
                                                <div class="btn-group gap-1">
                                                    <a href="medicine-edit.php?id=<?= (int)$row['id'] ?>"
                                                    class="btn btn-sm btn-light border-0 text-warning">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
    
                                                    <a href="medicine-list.php?delete=<?= (int)$row['id'] ?>"
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
                                            هیچ دوایی یافت نشد
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
                            href="?page=<?= $prevPage ?>&per_page=<?= $perPage ?>&search=<?= urlencode($_GET['search'] ?? '') ?>">
                            → قبلی 
                        </a>

                        <a
                            class="btn btn-sm btn-primary"
                            href="?page=<?= $nextPage ?>&per_page=<?= $perPage ?>&search=<?= urlencode($_GET['search'] ?? '') ?>">
                            بعدی ←
                        </a>
                    </nav>
                </div><!-- card-footer -->
            </div>
        </div></div></div><?php include 'inc/footer.php'; ?>