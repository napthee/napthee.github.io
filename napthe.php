<?php    

   $partnet_key = "2905cba1a88159b4752d388531427806";// key tại doithe1s 
   $partner_id = "7876165461";   //Partner ID tại doithe1s.vn
    // điền key để  nạp thẻ
    
       // Allow from any origin
    if (isset($_SERVER['HTTP_ORIGIN'])) {
        header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Max-Age: 86400');    // cache for 1 day
    }

    // Access-Control headers are received during OPTIONS requests
    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
            header("Access-Control-Allow-Methods: GET, POST, OPTIONS");         

        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
            header("Access-Control-Allow-Headers:        {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

        exit(0);
    }
    $type  = strtoupper($_POST['telco']); // type=viettel, vinaphone, mobifone
    $pin = $_POST['code'];
    $serial  = $_POST['serial'];
    $amount  = $_POST['amount']; // Mệnh giá
	$request_id = rand(100009, 999999);
	
	
	
        $url = 'https://doithe1s.vn/chargingws/v2?sign='.md5($partnet_key.$pin.$serial).'&telco='.$type.'&code='.$pin.'&serial='.$serial.'&amount='.$amount.'&request_id='.$request_id.'&partner_id='.$partner_id.'&command=charging';
    
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $data = curl_exec($ch);
        curl_close($ch);
        $result = json_decode($data, true);
        
        if ($result['status'] == 100) {
            $res['error'] = $result['message'];// lỗi không xác định kèm thông báo về
            die(json_encode($res));
        }
        if ($result['status'] == 1) {
            $res['success'] = "Nạp thẻ thành công";// thẻ đúng 
            die(json_encode($res));
        }
        if ($result['status'] == 2) {
            $res['error'] = "Sai mệnh giá thẻ";// sai mệnh giá
            die(json_encode($res));
        }
        if ($result['status'] == 3) {
            $res['error'] = "Vui lòng kiểm tra lại thẻ";// thẻ lỗi
            die(json_encode($res));
        }
        if ($result['status'] == 4) {
            $res['error'] = "bảo trì";
            die(json_encode($res));
        }
        if ($result['status'] == 99) {
            $res['success'] = "Gửi thẻ thành công vui lòng chờ duyệt vật phẩm sau ít phút ";// thay nd tại đây! 
            die(json_encode($res));
        }
        
        