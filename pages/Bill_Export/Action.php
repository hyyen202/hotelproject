<?php
include('../../include/init.php');

$act = $_GET['act'];
switch ($act) {
    case 'new':
            $user_id       = $data_user['id'];
            $order_name    = check_input($_POST['order_name'], $db);
            $order_note    = check_input($_POST['order_note'], $db);
            $order_by      = check_input($_POST['order_by'], $db);
            $order_name_by = $_POST['order_name_by'] ? check_input($_POST['order_name_by'], $db) : '';
            $check         = $db->num_rows("SELECT * FROM `tbl_order` WHERE name = '$order_name' AND user_id = '$user_id'");
            if(!$order_name){ // nếu trống tên món
                $result['type']     = "danger";
                $result['message']  = "<b>Thất bại!</b> Không được để trống tên món ăn.";
                echo json_encode($result);
            }elseif($order_by == "true" && !$order_name_by){ // nếu check nhận dùm mà không nhập tên người nhận dùm
                $result['type']     = "danger";
                $result['message']  = "<b>Thất bại!</b> Bạn chưa nhập tên người nhận dùm.";
                echo json_encode($result);
            }elseif($check > 0){ // check có đơn giống vậy rồi á
                $result['type']     = "danger";
                $result['message']  = "<b>Thất bại!</b> Bạn đã có 1 đơn tương tự như vậy rồi.";
                echo json_encode($result);
            }else{ // well done
                $user_id    = $data_user['id'];
                $createAt   = CUR_DATE;
                $order_by   = $order_by == "true" ? 1: 0;
                $db->query("INSERT INTO `tbl_order`(`name`, `note`, `order_by`, `name_by`, `user_id`, `create_at`) VALUES ('$order_name', '$order_note', '$order_by', '$order_name_by', '$user_id', '$createAt')");
                $result['type']     = "success";
                $result['message']  = "<b>Thành công!</b> Bạn đã đặt món ăn thành công!";
                echo json_encode($result);
            }
        

        break;
    case 'load':
        $user_id    = $data_user['id'];
        $sql        = "SELECT tbl_order.*, tbl_user.fullname  FROM tbl_order, tbl_user where tbl_order.user_id = tbl_user.id AND tbl_order.user_id ='$user_id' ORDER BY id desc limit 10";
        $result     = "";
        foreach($db->fetch_assoc($sql, 0) as $row){
            $name = $row['fullname'];
            if($row['order_by'] == 1 && $row['name_by'] != '') $name = $row['name_by'];
            $note = $row['note'];
            if($row['note'] == '') $note = "Không";
            $result .= '<tr>
            <th scope="row">'.$row['id'].'</th>
            <td>'. $name.'</td>
            <td>'.$row['name'].'</td>
            <td>'.$note.'</td>
            </tr>';
        }
        echo $result;
        break;
    case 'count':
        $uId    = $data_user['id'];
        $sql    = "SELECT * FROM `tbl_order` where user_id = '$uId' ORDER BY id desc";
        $total  = $db->num_rows($sql);
        echo $total;
        break;
    case 'get':
        
        $limit = LIMIT_ROW;
        $user_id    = $data_user['id'];
        $sql = "SELECT id FROM `tbl_order` where user_id = '$user_id' ORDER BY id desc"; 
        $total = $db->num_rows($sql);
        $total_pages = ceil($total / $limit); 

        
        if(isset($_POST["page"]) && $_POST["page"] > $total_pages){
            $page = $total_pages;
        }elseif (isset($_POST["page"]) && $_POST["page"] != 0) {
            $page  = $_POST["page"]; 
        }else{
            $page=1;
        };    

        $start  = ($page-1) * $limit;  
        $sql    = "SELECT tbl_order.*, tbl_user.fullname  FROM tbl_order, tbl_user where tbl_order.user_id = tbl_user.id AND tbl_order.user_id ='$user_id' ORDER BY id desc limit $start, $limit";
        $result = "";
        foreach($db->fetch_assoc($sql, 0) as $row){
            $name = $row['fullname'];
            if($row['order_by'] == 1 && $row['name_by'] != '') $name = $row['name_by'];
            $note = $row['note'];
            if($row['note'] == '') $note = "Không";
            $result .= '<tr>
            <th scope="row">'.$row['id'].'</th>
            <td>'. $name.'</td>
            <td>'.$row['name'].'</td>
            <td>'.$note.'</td>
            </tr>';
        }
        echo $result;

        break;
    default:
        
        break;
}
