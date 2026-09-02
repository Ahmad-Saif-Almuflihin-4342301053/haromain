Role admin pusat (superadmin):
- menu manajemen jamaah. it is Person Management, with route "admin/Person". At the list in the column Aksi, add a button with label "Reset Password". add a confirmation dialog with message "Apakah Anda yakin ingin mereset password jamaah ini?". Then if user confirm, reset the password to 123456 (save hash in the database).
- Menu Monitoring, detail, tab haromain, with route "admin/Monitoring/show". Add a filter name "Nama Jamaah".
- Menu Monitoring, detail, tab haromain, with route "admin/Monitoring/show". Add check all to check all item in the list. Also when navigate to other datatable pagination the checked item should persist.

Role Admin Travel
- toolbar, add change password feature with label "Ubah Pasword".
- hide menu manajemen user
- Menu Daftarkan Jamaah. add logic, the jamaah list shown should has paket from the correct travel.
- hide menu fee Haromain