(function ($) {
	'use strict';

	// Fungsi untuk mendapatkan path URL aktif
	function getActivePath() {
		return window.location.pathname;
	}

	// Fungsi untuk menambahkan class active pada menu yang sesuai
	function setActiveMenu() {
		var currentPath = getActivePath();
		var currentMenu = $('body').data('menu');

		// Hapus class active yang ada
		$('.sidebar .nav .nav-item').removeClass('active');
		$('.sidebar .nav .nav-item .sub-menu .nav-item').removeClass('active');

		// Cari dan tambahkan class active pada menu yang sesuai
		$('.sidebar .nav .nav-item').each(function () {
			var fiturName = $(this).data('fitur-name');
			if (fiturName === currentMenu) {
				$(this).addClass('active');

				// Jika ada submenu, cek juga submenu yang aktif
				var submenu = $(this).find('.sub-menu');
				if (submenu.length) {
					submenu.find('.nav-item').each(function () {
						var sublink = $(this).find('.nav-link').attr('href');
						if (sublink && currentPath.indexOf(sublink) > -1) {
							$(this).addClass('active');
						}
					});
				}
			}
		});
	}


	// Jalankan fungsi saat dokumen siap
	$(document).ready(function () {
		//setActiveMenu();
	});
})(jQuery);
