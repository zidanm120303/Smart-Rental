-- Cek bentrok booking untuk satu aset
SELECT b.booking_code, b.pickup_at, b.return_at, b.status
FROM booking_items bi
JOIN bookings b ON b.id = bi.booking_id
WHERE bi.asset_id = :asset_id
  AND b.status IN ('pending', 'approved', 'active')
  AND b.pickup_at < :requested_return_at
  AND b.return_at > :requested_pickup_at;

-- Dashboard total aset per status
SELECT availability_status, COUNT(*) total
FROM assets
WHERE deleted_at IS NULL
GROUP BY availability_status;

-- Pendapatan bulanan
SELECT DATE_FORMAT(payment_date, '%Y-%m') bulan, SUM(amount) total
FROM payments
GROUP BY DATE_FORMAT(payment_date, '%Y-%m')
ORDER BY bulan;

-- Top aset paling sering disewa
SELECT a.asset_code, a.name, COUNT(bi.id) total_disewa, SUM(bi.line_total) revenue
FROM booking_items bi
JOIN assets a ON a.id = bi.asset_id
JOIN bookings b ON b.id = bi.booking_id
WHERE b.status IN ('active', 'completed')
GROUP BY a.id, a.asset_code, a.name
ORDER BY total_disewa DESC
LIMIT 10;
