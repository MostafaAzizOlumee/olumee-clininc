<div id="sidebar-wrapper">
    <div class="sidebar-brand">
        <i class="fas ms-2"></i> دکتور عبدالعزیز علومی
    </div>
    
    <div class="list-group list-group-flush mt-3">
        <div class="menu-label">تعریف معلومات</div>
        <!-- Prescription -->
        <a href="javascript:void(0)" 
            data-bs-toggle="collapse" 
            data-bs-target="#prescription" 
            aria-expanded="<?= (isThisRoute('prescription-add.php') || isThisRoute('prescription-list.php')) ? 'true' : 'false' ?>"
            class="list-group-item list-group-item-action <?= (isThisRoute('prescription-add.php') || isThisRoute('prescription-list.php'))? 'active' : '' ?> d-flex justify-content-between align-items-center">
            <span><i class="fas fa-file-prescription"></i> نسخه</span>
            <i class="fas fa-chevron-down small menu-arrow"></i>
        </a>
        <div class="<?= (isThisRoute('prescription-add.php') || isThisRoute('prescription-list.php'))? '' : 'collapse' ?>" id="prescription">
            <div class="list-group list-group-flush pe-3">
                <a href="prescription-add.php" class="list-group-item list-group-item-action <?= isThisRoute('prescription-add.php') ? 'active-menu' : '' ?> border-0 py-2">ثبت</a>
                <a href="prescription-list.php" class="list-group-item list-group-item-action <?= isThisRoute('prescription-list.php') ? 'active-menu' : '' ?> border-0 py-2">لیست</a>
            </div>
        </div>
        <!-- Medicine -->
       <a href="javascript:void(0)" 
            data-bs-toggle="collapse" 
            data-bs-target="#medicine" 
            aria-expanded="<?= (isThisRoute('medicine-add.php') || isThisRoute('medicine-list.php')) ? 'true' : 'false' ?>"
            class="list-group-item list-group-item-action <?= (isThisRoute('medicine-add.php') || isThisRoute('medicine-list.php'))? 'active' : '' ?> d-flex justify-content-between align-items-center">
            <span><i class="fas fa-pills"></i> دوا</span>
            <i class="fas fa-chevron-down small menu-arrow"></i>
        </a>
        <div class="<?= (isThisRoute('medicine-add.php') || isThisRoute('medicine-list.php'))? '' : 'collapse' ?>" id="medicine">
            <div class="list-group list-group-flush pe-3">
                <a href="medicine-add.php" class="list-group-item list-group-item-action <?= isThisRoute('medicine-add.php') ? 'active-menu' : '' ?> border-0 py-2">ثبت</a>
                <a href="medicine-list.php" class="list-group-item list-group-item-action <?= isThisRoute('medicine-list.php') ? 'active-menu' : '' ?> border-0 py-2">لیست</a>
            </div>
        </div>
    </div>
</div>
