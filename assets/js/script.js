// Updated selector to match the data-bs-toggle attribute
$('.list-group-item[data-bs-toggle="collapse"]').on('click', function() {
    let target = $(this).data('bs-target');
    $('.collapse.show').not(target).collapse('hide');
});

// Toggle Sidebar
$("#menu-toggle").click(function(e) {
    e.preventDefault();
    $("#wrapper").toggleClass("toggled");
});