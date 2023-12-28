<?php
$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
require_once($rootDir."../../include/init.php");
require($rootDir.'../../lib/pdf/tfpdf.php');
$user_id    = $data_user['id'];
$result = [];
$query = $db->fetch_assoc("SELECT bk.id as id, kh.name as name, dateIN, dateOut, r.price as priceR, idRoom, phone, r.type, fullname, day,deal, tbl_bill.day as day, tbl_bill.total as total
							FROM tbl_customers kh, tbl_booking bk, tbl_room r, tbl_user, tbl_bill
							WHERE kh.id = bk.idCustomer  AND r.id = bk.idRoom AND bk.id = $id and 
							bk.idEmloyee = tbl_user.id and tbl_bill.idBooking = bk.id", 0);
foreach($query as $row){
    $name = $row['fullname'];
    if($row['note'] == '') $note = "Không";
	array_push($result, (object)[
        'id' => $row['id'],
        'fullname' => $name,
        'name' => $row['name'],
		'phone' => $row['phone'],
]);
}

$header = array( "No_", "Nhân viên", "Tên Khách hàng", "Số điện thoại");
$line_height = 10;
$width = 110;



$pdf = new tFPDF();
$pdf->AddPage("L");

$pdf->AddFont('Times New Roman','','times.ttf',true);
$pdf->SetFont('Times New Roman','',14);

$pdf->Ln(); // Xuống dòng trước khi hiển thị nội dung hóa đơn

// Hiển thị thông tin hóa đơn
$pdf->Cell(0, $height, "Thông tin hóa đơn", 1, 1, 'C');
$pdf->Cell(40, $height, "ID đơn đặt hàng", 1);
$pdf->Cell(40, $height, "Ngày đặt hàng", 1);
$pdf->Cell(40, $height, "Ngày giao hàng", 1);
$pdf->Cell(40, $height, "Tổng cộng", 1);

$pdf->Ln();

// Lấy thông tin hóa đơn từ kết quả truy vấn
$billInfo = $query[0];

// Định dạng ngày tháng năm
$dateOrder = date('d-m-Y', strtotime($billInfo['dateIN']));
$dateDelivery = date('d-m-Y', strtotime($billInfo['dateOut']));

// Hiển thị thông tin hóa đơn
$pdf->Cell(40, $height, $billInfo['id'], 1);
$pdf->Cell(40, $height, $dateOrder, 1);
$pdf->Cell(40, $height, $dateDelivery, 1);
$pdf->Cell(40, $height, $billInfo['total'], 1);

$pdf->Ln(); // Xuống dòng sau khi hiển thị thông tin hóa đơn

// Hiển thị các chi tiết sản phẩm trong hóa đơn
$pdf->Cell(0, $height, "Chi tiết sản phẩm", 1, 1, 'C');
$pdf->Cell(40, $height, "STT", 1);
$pdf->Cell(80, $height, "Tên sản phẩm", 1);
$pdf->Cell(40, $height, "Số lượng", 1);
$pdf->Cell(40, $height, "Đơn giá", 1);
$pdf->Cell(40, $height, "Thành tiền", 1);

$pdf->Ln();

// Lấy danh sách sản phẩm từ kết quả truy vấn
$products = $db->fetch_assoc("SELECT * FROM tbl_products WHERE id_order = " . $billInfo['id'], 0);

$i = 1;
foreach ($products as $product) {
    $totalPrice = $product['quantity'] * $product['price'];
    $pdf->Cell(40, $height, $i, 1);
    $pdf->Cell(80, $height, $product['name'], 1);
    $pdf->Cell(40, $height, $product['quantity'], 1);
    $pdf->Cell(40, $height, $product['price'], 1);
    $pdf->Cell(40, $height, $totalPrice, 1);
    $pdf->Ln();
    $i++;
}

$pdf->Output();
?>