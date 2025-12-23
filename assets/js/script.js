// Optional: Auto-close other submenus when opening a new one
$('.list-group-item[data-bs-toggle="collapse"]').on('click', function() {
    $('.collapse.show').not($(this).attr('href')).collapse('hide');
});


 $("#menu-toggle").click(function(e) {
        e.preventDefault();
        $("#wrapper").toggleClass("toggled");
    });