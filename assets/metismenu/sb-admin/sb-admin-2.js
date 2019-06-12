$(function () {
    $('#side-menu').metisMenu();
    $(":checkbox").each(function () {
        var onText = $(this).data('ontext');
        var offText = $(this).data('offtext');
        var options = {};
        if (offText) {
            options.onText = onText;
        }
        if (offText) {
            options.offText = offText;
        }
        $(this).bootstrapSwitch(options);
    });
    //$('#example').DataTable( {
    //	responsive: true
    //} );
    //$.plot($("#placeholder"), [ [[0, 0], [1, 1]] ], { yaxis: { max: 1 } });
});
//Loads the correct sidebar on window load,
//collapses the sidebar on window resize.
// Sets the min-height of #page-wrapper to window size
$(function () {
//    $(window).bind("load resize", function () {
//        topOffset = 50;
//        width = (this.window.innerWidth > 0) ? this.window.innerWidth : this.screen.width;
//        if (width < 768) {
//            $('div.navbar-collapse').addClass('collapse');
//            topOffset = 100; // 2-row-menu
//        }
//        else {
//            $('div.navbar-collapse').removeClass('collapse');
//        }
//        height = ((this.window.innerHeight > 0) ? this.window.innerHeight : this.screen.height) - 1;
//        height = height - topOffset;
//        if (height < 1)
//            height = 1;
//        if (height > topOffset) {
//            $("#page-wrapper").css("min-height", (height) + "px");
//        }
//    });
    var url = window.location.toString();
    var urls = url.split('?');
    url = urls[0];
    var element = $('ul.nav a').filter(function () {
        return this.href === url;
    }).addClass('active').parent().parent().addClass('in').parent();
    if (element.is('li')) {
        element.addClass('active');
    }
    if (element.parent().hasClass('nav-second-level')) {
//        element.parent().addClass('in').prev().addClass('active').parent().addClass('active');
        element.parent().addClass('in').parent().addClass('active');
    }
});