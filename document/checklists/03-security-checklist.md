# Checklist Keamanan

- [ ] Semua route fitur memakai middleware auth.
- [ ] Role/permission dicek di controller/policy.
- [ ] Semua form memakai CSRF.
- [ ] Semua request memakai Form Request.
- [ ] Upload file dibatasi mime dan ukuran.
- [ ] Tidak ada password plaintext.
- [ ] Tidak ada query raw dari input user tanpa binding.
- [ ] Booking memakai transaction dan lock.
- [ ] Payment memakai transaction.
- [ ] Invoice tidak bisa dibuat ganda.
- [ ] Data master penting memakai soft delete/nonaktif.
- [ ] Activity log merekam aksi penting.
