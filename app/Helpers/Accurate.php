<?php
namespace App\Helpers;
use ChannelLog as Log;
use stdClass;

use Auth;
use App\AccurateAuth;
use Carbon\Carbon;
use App\Model\MasterData\MembershipType;

use App\Model\Club\ClubInfo;

/**
 * [handleExceptionAndLog - To handle the Exception and Log for each module wise]
 * @param  [object]  $exception         [Exception object]
 * @param  [string]  $module     [Module name]
 */

class Accurate {
    static $ACCURATE;

    public static function accurateClub($id){
        $club = ClubInfo::find($id);

        if($club){
            if($club->accurate_id && $club->accurate_branch){
                return $club;
            }else {
                return '';
            }
        }else{
            return '';
        }
        /* $clubArray = [
            'cfs-3' => '842000', //PT. Unity Brands indonesia || curves sunda
            'cfs-13' => '842703', //CV.BUGAR SEHAT SEJATI || curves terban yogyakarta
            'cfs-16' => '842264', //CV. Jingga Wibawa || curves bumi serpong damai
            'cfs-4' => '842000', //PT. Unity Brands indonesia || curves kopo
            'cfs-14' => '842329', //CV. Endah Kurves Bahagia || curves kebayoran lama
            'cfs-9' => '842235', //CV. Raina Dewi || curves moi
            'cfs-7' => '842210', //CV. Jubilant || curves buah batu
            'cfs-24' => '842724', //PT. Pratama Langit Persada || curves elang laut pik

            'cfs-23' => '841949', //CV. KERJA BAHAGIA SYURGA || curves gading serpong
            'cfs-17' => '841949', //CV. KERJA BAHAGIA SYURGA || curves gwalk surabaya

            'cfs-19' => '842310', //CV. Darili Kreasi Bersama || curves tebet

            'cfs-5' => '', //HQ not included || curves hq

            'cfs-11' => '842652', //CV. KENAN EKA BUGAR || curves summarecon bandung
            'cfs-29' => '842310', //CV. Darili Kreasi Bersama || curves lebak bulus
            'cfs-18' => '842711', //CV. BUGAR SEHAT SEJATI DUA || curves paramount semarang
            'cfs-27' => '842735', //CV. Cahya Pratama Syafitri || curves grand wisata
            'cfs-8' => '842264', //CV. Jingga Wibawa || curves tasikmalaya
            'cfs-12' => '841949', //CV. KERJA BAHAGIA SYURGA || curves summarecon bekasi
            'cfs-25' => '842761', //PT. Sehat Bugar Jasmani || curves pettarani makassar
            'cfs-20' => '842713', //PT. Mentari Cakrawala Sehati || curves kemanggisan
            'cfs-26' => '841947', //PT. Empat Kaki Meja Sempurna Real || curves margonda depok
            'cfs-28' => '842763', //PT Lima Bugar Sempurna || curves iskandar muda medan
            'cfs-2' => '842000', //PT. Unity Brands indonesia || curves ciumbuleuit
            'cfs-22' => '842235', //CV. Raina Dewi || curves jakarta garden city
            'cfs-15' => '841949', //CV. KERJA BAHAGIA SYURGA || curves kemang pratama
            'cfs-10' => '842662', //CV. PURBA SEKAR || curves mekar wangi
            'cfs-6' => '842000', //PT. Unity Brands indonesia || curves otten
            'cfs-1' => '842000', //PT. Unity Brands indonesia || curves surya sumantri
            'cfs-21' => '842718', //PT. Prima Bugar Sejahtera || curves pajajaran bogor

            'cfs-30' => '1046660', //CV. Dwimitra Sehat Bugar || curves samarinda
        ];
        //$clubId = "cfs-".$id;

        if(isset($clubArray["cfs-$id"])){
            return $clubArray["cfs-$id"];
        }else{
            return '';
        } */


    }

    public static function accurateBranch($id){
        $branchArray = [
            'cfs-13' => '50', //CV.BUGAR SEHAT SEJATI || curves terban yogyakarta
            'cfs-14' => '50', //CV. Endah Kurves Bahagia || curves kebayoran lama
            'cfs-9' => '50', //CV. Raina Dewi || curves moi
            'cfs-7' => '50', //CV. Jubilant || curves buah batu
            'cfs-24' => '50', //PT. Pratama Langit Persada || curves elang laut pik
            'cfs-19' => '50', //CV. Darili Kreasi Bersama || curves tebet
            'cfs-11' => '50', //CV. KENAN EKA BUGAR || curves summarecon bandung
            'cfs-29' => '50', //CV. Darili Kreasi Bersama || curves lebak bulus
            'cfs-18' => '50', //CV. BUGAR SEHAT SEJATI DUA || curves paramount semarang
            'cfs-27' => '50', //CV. Cahya Pratama Syafitri || curves grand wisata
            'cfs-8'  => '50', //CV. Jingga Wibawa || curves tasikmalaya
            'cfs-12' => '50', //CV. KERJA BAHAGIA SYURGA || curves summarecon bekasi
            'cfs-25' => '50', //PT. Sehat Bugar Jasmani || curves pettarani makassar
            'cfs-20' => '50', //PT. Mentari Cakrawala Sehati || curves kemanggisan
            'cfs-26' => '50', //PT. Empat Kaki Meja Sempurna Real || curves margonda depok
            'cfs-28' => '50', //PT Lima Bugar Sempurna || curves iskandar muda medan
            'cfs-10' => '50', //CV. PURBA SEKAR || curves mekar wangi
            'cfs-21' => '50', //PT. Prima Bugar Sejahtera || curves pajajaran bogor

            'cfs-30' => '50', //HQ not included || curves samarinda


            'cfs-3' => '303', //PT. Unity Brands indonesia || curves sunda
            'cfs-6' => '301', //PT. Unity Brands indonesia || curves otten
            'cfs-4' => '300', //PT. Unity Brands indonesia || curves kopo
            'cfs-23' => '250', //CV. KERJA BAHAGIA SYURGA || curves gading serpong
            'cfs-15' => '201', //CV. KERJA BAHAGIA SYURGA || curves kemang pratama
            'cfs-2' => '200', //PT. Unity Brands indonesia || curves ciumbuleuit
            'cfs-17' => '150', //CV. KERJA BAHAGIA SYURGA || curves gwalk surabaya
            'cfs-1' => '150', //PT. Unity Brands indonesia || curves surya sumantri
            'cfs-16' => '150', //CV. Jingga Wibawa || curves bumi serpong damai
            'cfs-22' => '101', //CV. Raina Dewi || curves jakarta garden city

            'cfs-5' => '', //HQ not included || curves hq


        ];

        return $branchArray["cfs-$id"];
    }

    /* public static function accurateBranchName($id){
        $branchArray = [
            '303' => 'PT. Unity Brands indonesia', // curves sunda
            '300', //PT. Unity Brands indonesia || curves kopo
            '150', //CV. Jingga Wibawa || curves bumi serpong damai
            '250', //CV. KERJA BAHAGIA SYURGA || curves gading serpong
            '150', //CV. KERJA BAHAGIA SYURGA || curves gwalk surabaya
            '200', //PT. Unity Brands indonesia || curves ciumbuleuit
            '101', //CV. Raina Dewi || curves jakarta garden city
            '201', //CV. KERJA BAHAGIA SYURGA || curves kemang pratama
            '301', //PT. Unity Brands indonesia || curves otten
            '150', //PT. Unity Brands indonesia || curves surya sumantri

            '50' => 'CV.BUGAR SEHAT SEJATI', // || curves terban yogyakarta
            '50', //CV. Endah Kurves Bahagia || curves kebayoran lama
            '50', //CV. Raina Dewi || curves moi
            '50', //CV. Jubilant || curves buah batu
            '50', //PT. Pratama Langit Persada || curves elang laut pik
            '50', //CV. Darili Kreasi Bersama || curves tebet

            '50', //CV. KENAN EKA BUGAR || curves summarecon bandung
            '50', //CV. Darili Kreasi Bersama || curves lebak bulus
            '50', //CV. BUGAR SEHAT SEJATI DUA || curves paramount semarang
            '50', //CV. Cahya Pratama Syafitri || curves grand wisata
            '50', //CV. Jingga Wibawa || curves tasikmalaya
            '50', //CV. KERJA BAHAGIA SYURGA || curves summarecon bekasi
            '50', //PT. Sehat Bugar Jasmani || curves pettarani makassar
            '50', //PT. Mentari Cakrawala Sehati || curves kemanggisan
            '50', //PT. Empat Kaki Meja Sempurna Real || curves margonda depok
            '50', //PT Lima Bugar Sempurna || curves iskandar muda medan
            '50', //CV. PURBA SEKAR || curves mekar wangi
            '50', //PT. Prima Bugar Sejahtera || curves pajajaran bogor
        ];

        return $branchArray["cfs-$id"];
    } */

    public static function membershipTypes(string $string = NULL)
    {
        /* $types = [
        '0' => '750 K',  //DISREGARD
        '1' => 'MM',
        '2' => 'AD12',
        '3' => 'PP12',
        '4' => 'PP18',
        '5' => 'ST6',
        '6' => 'ST12',
        '7' => 'ST18',
        '8' => '',
        '9' => '',
        '10' => '',
        '11' => 'PP6',
        '12' => 'ST6',
        '13' => 'ST12',
        '14' => 'PP6',
        '15' => 'PP12',
        '16' => 'PP18',
        '17' => 'PP24',
        '18' => '',
        '19' => 'PP3',
        '20' => 'PP12',
        '21' => 'PP6',
        '22' => 'PP24',

        '29' => 'PP24',
        '31' => 'PP12',
        '32' => 'PP6',
        '33' => 'ST12',
        '34' => 'ST6',
        '35' => 'MM',

        //PROMOS
        '41'=> '750K'

        ]; */

        //return $types[$string];
        $membershipTypes = MembershipType::find($string);
        return $membershipTypes->accurate_code;
    }

    public static function AccurateOpenDB($id, $token){
        $accessToken = $token;

        // Header
        $header = array(
            "Authorization: Bearer $accessToken"
        );

        // URL
        $url = "https://account.accurate.id/api/open-db.do?id=" . $id;

        // Connect API
        $opts = array("http" =>
            array(
                "method" => "GET",
                "header" => $header,
                "ignore_errors" => true,
            )
        );
        $context  = stream_context_create($opts);
        $response = json_decode(file_get_contents($url, false, $context));

        // Output
        if(isset($response->session)){
            $session = $response->session;
            $host = $response->host;
            $data = [
                'session' => $response->session,
                'host' => $response->host,
                'access_token' => $accessToken,
            ];
            return $data;
        }else{
            //print_r($response->error);
            return false;
        }
    }

    public static function oauthAOL($code, $accurate, $databaseID, $redirectURI){
        //Check if we have tokens in db table
        //$accurate = AccurateAuth::find(1);

        //echo 'authorization catcher page..';
        if($code){

            $obj = new stdClass;
            self::accurateGetToken($code, $obj, [], true, $databaseID, $redirectURI);
        }elseif ($accurate) {

            $openDB = self::AccurateOpenDB($databaseID, $accurate->access_token );

            if(!$openDB){
                self::accurateOauth($redirectURI);
            }else{
                self::accurateGetToken($accurate->code, $accurate, $openDB, false, $databaseID, $redirectURI);
            }
        }else{
            self::accurateOauth($redirectURI);
        }
    }

    public static function accurateOauth($redirectURI){
        $url = 'https://account.accurate.id/oauth/authorize';

        $params['client_id'] = env('ACCURATE_CLIENT_ID');
        $params['secret'] = env('ACCURATE_CLIENT_SECRET');
        $params['response_type'] = 'code';
        $params['redirect_uri'] = $redirectURI;

        $params['scope'] = "item_view item_save customer_save sales_invoice_save customer_view branch_view";

        $paramString = '?'.http_build_query($params);
        header("Location: ".$url .$paramString);
        exit;
    }

    public static function accurateGetToken($code, $obj, $openDB, $newcode, $databaseID, $redirectURI){
        $clientId = env('ACCURATE_CLIENT_ID');
        $clientSecret = env('ACCURATE_CLIENT_SECRET');
        $oauthCallback = $redirectURI;

        $basicAuth = base64_encode("$clientId:$clientSecret");

        // Header
        $header = array(
            "Authorization: Basic $basicAuth",
            "Content-Type: application/x-www-form-urlencoded"
        );


        if($newcode){
            // Content
            $content = array(
                "code" => $code,
                "grant_type" => "authorization_code",
                "redirect_uri" => $oauthCallback
            );

            // Connect API
            $opts = array("http" =>
                array(
                    "method" => "POST",
                    "header" => $header,
                    "content" => http_build_query($content),
                    "ignore_errors" => true,
                )
            );

            $url = "https://account.accurate.id/oauth/token";

            $context  = stream_context_create($opts);
            $response = file_get_contents($url, false, $context);

            // Output
            $json = json_decode($response);

            if(isset($json->{"error"})){
                header("Location: $redirectURI");
                //dd($json->{"error"});
            }else{
                $accessToken = $json->{"access_token"};
                $refreshToken = $json->{"refresh_token"};

                //save to DB
                $accurate = AccurateAuth::findOrCreate(1);
                $accurate->access_token = $accessToken;
                $accurate->refresh_token = $refreshToken;
                $accurate->code = $code;
                $accurate->save();

                $openDB = self::AccurateOpenDB($databaseID, $accurate->access_token );
            }

            //return $openDB;

        }else{
            $accessToken = $obj->access_token;
            $refreshToken = $obj->refresh_token;
            $created_date = $obj->created_at;

            $created_date = Carbon::parse($created_date);
            $diffdays = Carbon::today()->diffInDays($created_date);
            $expiry_date = Carbon::parse($created_date)->addDays(15);

            if($diffdays > 13){
                //refresh token
                // Content
                $content = array(
                    "grant_type" => "refresh_token",
                    "refresh_token" => $refreshToken,
                );

                // Connect API
                $opts = array("http" =>
                    array(
                        "method" => "POST",
                        "header" => $header,
                        "content" => http_build_query($content),
                        "ignore_errors" => true,
                    )
                );

                $url = "https://account.accurate.id/oauth/token";

                $context  = stream_context_create($opts);
                $response = file_get_contents($url, false, $context);
                //print_r($response);
                // Output
                $json = json_decode($response);

                if(isset($json->access_token)){
                    $accessToken = $json->{"access_token"};
                    $refreshToken = $json->{"refresh_token"};

                    //Update to DB
                    $accurate = AccurateAuth::find(1);
                    $accurate->access_token = $accessToken;
                    $accurate->save();

                    $openDB = self::AccurateOpenDB($databaseID, $accurate->access_token );
                //    return $openDB;
                }
            }
        }

        return $openDB;
    }

    //CHECK MEMBER
    public static function AccurateCheckMember($obj, $member){
        $accessToken = $obj["access_token"];
        $session = $obj["session"];
        $host = $obj["host"];

        // Header
        $header = array(
            "Authorization: Bearer $accessToken",
            "X-SESSION-ID: $session"
        );
        // Connect API
        $opts = array("http" =>
            array(
                "method" => "GET",
                "header" => $header,
                "ignore_errors" => true,
            )
        );
        $context  = stream_context_create($opts);

        // Content
        $content = array(
            "fields" => "id,name,customerNo",
            "keywords" => str_replace("%20", " ", $member['name'])
        );

        // URL
        $url = $host . "/accurate/api/customer/list.do?" . http_build_query($content);


        $result = json_decode(file_get_contents($url, false, $context));

        $data = [];
        if($result){
            foreach ($result->d as $key => $value) {
                if($value->name === $member['name']){
                    $data = [
                        'customerNo' => $value->customerNo,
                        'name' => $value->name
                    ];
                }
            }
        }
        return $data;
    }

    //CREATE CUSTOMER
    public static function AccurateCreateMember($obj, $member){
        $accessToken = $obj["access_token"];
        $session = $obj["session"];
        $host = $obj["host"];

        // Header
        $header = array(
            "Authorization: Bearer $accessToken",
            "X-SESSION-ID: $session",
            "Content-Type: application/json"
        );

        // Content
        $content = array(
            "transDate" => date("d/m/Y"),
            "name" => str_replace("%20", " ", $member['name'])
        );

        // URL
        $url = $host . "/accurate/api/customer/save.do";

        // Connect API
        $opts = array("http" =>
            array(
                "method"  => "POST",
                "header"  => $header,
                "content" => json_encode($content),
                "ignore_errors" => true,
            )
        );
        $context  = stream_context_create($opts);
        //$result = file_get_contents($url, false, $context);

        $result = json_decode(file_get_contents($url, false, $context));

        $data = [];
        if($result){
            $customerNo = $result->r->customerNo;
            $customerName = str_replace("%20", " ", $result->r->name);
            $data = [
                'customerNo' => $customerNo,
                'name' => $customerName
            ];
        }
        return $data;
    }

    //CHECK ITEM
    public static function accurateCheckItem($obj, $member, $type){
        $accessToken = $obj["access_token"];
        $session = $obj["session"];
        $host = $obj["host"];

        // Header
        $header = array(
            "Authorization: Bearer $accessToken",
            "X-SESSION-ID: $session"
        );
        // Connect API
        $opts = array("http" =>
            array(
                "method" => "GET",
                "header" => $header,
                "ignore_errors" => true,
            )
        );
        $context  = stream_context_create($opts);

        /* if($type === 'SERVICE'){
            $itemType = self::membershipTypes($member['membership_type']);
        }else{
            $itemType = $member['item'];
        }
        // Content
        $content = array(
            "fields" => "id,no,name,itemType",
            "sp.pageSize"=>200,
            "filter.itemType" => $type,
            "filter.keywords" => $itemType

        ); */
        $finalType = '';
        if($type === 'SERVICE'){
            $itemType = self::membershipTypes($member['membership_type']);
            $finalType = 'SERVICE';
        }elseif($type === 'PROMO'){
            $itemType = isset($member['item']) ? $member['item'] : $member['membership_type'];
            $finalType = 'SERVICE';
        }
        else{
            $itemType = $member['item'];
            $finalType = 'INVENTORY';
        }
        // Content
        $content = array(
            "fields" => "id,no,name,itemType",
            "sp.pageSize"=>200,
            "filter.itemType" => $finalType,
            "filter.keywords" => $itemType

        );

        // URL
        $url = $host . "/accurate/api/item/list.do?" . http_build_query($content);

        $result = json_decode(file_get_contents($url, false, $context));

        $data = [];

        if($result){
            foreach ($result->d as $key => $value) {
                if($value->no === $itemType){
                    $data = [
                        'no' => $value->no,
                        'id' => $value->id
                    ];
                }
            }
        }
        return $data;
    }


    //CHECK ITEM TEST
    public static function accurateCheckItem_TEST($obj, $member, $type){
        $accessToken = $obj["access_token"];
        $session = $obj["session"];
        $host = $obj["host"];

        // Header
        $header = array(
            "Authorization: Bearer $accessToken",
            "X-SESSION-ID: $session"
        );
        // Connect API
        $opts = array("http" =>
            array(
                "method" => "GET",
                "header" => $header,
                "ignore_errors" => true,
            )
        );
        $context  = stream_context_create($opts);
        $finalType = '';
        if($type === 'SERVICE'){
            $itemType = self::membershipTypes($member['membership_type']);
            $finalType = 'SERVICE';
        }elseif($type === 'PROMO'){
            $itemType = isset($member['item']) ? $member['item'] : $member['membership_type'];
            $finalType = 'SERVICE';
        }
        else{
            $itemType = $member['item'];
            $finalType = 'INVENTORY';
        }
        // Content
        $content = array(
            "fields" => "id,no,name,itemType",
            "sp.pageSize"=>200,
            "filter.itemType" => $finalType,
            "filter.keywords" => $itemType
        );

        // URL
        $url = $host . "/accurate/api/item/list.do?" . http_build_query($content);

        $result = json_decode(file_get_contents($url, false, $context));

        $data = [];

        if($result){
            foreach ($result->d as $key => $value) {
                if($value->no === $itemType){
                    $data = [
                        'no' => $value->no,
                        'id' => $value->id
                    ];
                }
            }
        }
        return $data;
    }

    public static function accurateCheckItems($obj, $type, $page, $max='100'){
        $accessToken = $obj["access_token"];
        $session = $obj["session"];
        $host = $obj["host"];

        // Header
        $header = array(
            "Authorization: Bearer $accessToken",
            "X-SESSION-ID: $session"
        );
        // Connect API
        $opts = array("http" =>
            array(
                "method" => "GET",
                "header" => $header,
                "ignore_errors" => true,
            )
        );
        $context  = stream_context_create($opts);

        // Content
        $content = array(
            "fields" => "id,no,name,itemType",
            "sp.page"=> $page,
            "sp.pageSize"=>$max,
            "filter.itemType" => $type,
            //"filter.keywords" => ''

        );

        // URL
        $url = $host . "/accurate/api/item/list.do?" . http_build_query($content);

        $result = json_decode(file_get_contents($url, false, $context));

        $data = [];

        if($result){
            foreach ($result->d as $key => $value) {
                //if(is_numeric($value->no)){

                //}else{
                    $data[$value->no] = $value->name;
                //}
            }
        }
        return $data;
    }

    //CREATE ITEM
    public static function AccurateCreateItem($obj, $member, $type){
        $accessToken = $obj["access_token"];
        $session = $obj["session"];
        $host = $obj["host"];

        // Header
        $header = array(
            "Authorization: Bearer $accessToken",
            "X-SESSION-ID: $session",
            "Content-Type: application/json"
        );

        //$itemType = self::membershipTypes($member['membership_type']);
        if($type === 'SERVICE'){
            $itemType = self::membershipTypes($member['membership_type']);
        }else{
            $itemType = $member['item'];
        }
        // Content
        $content = array(
            "no"       => $itemType,
            "name"     => $itemType,
            "itemType" => $type,
        );

        // URL
        $url = $host . "/accurate/api/item/save.do";

        // Connect API
        $opts = array("http" =>
            array(
                "method"  => "POST",
                "header"  => $header,
                "content" => json_encode($content),
                "ignore_errors" => true,
            )
        );
        $context  = stream_context_create($opts);
        //$result = file_get_contents($url, false, $context);

        $result = json_decode(file_get_contents($url, false, $context));

        $data = [];
        if($result){
            $itemId = $result->r->id;
            $itemNo = $result->r->name;
            $data = [
                'id' => $itemId,
                'no' => $itemNo
            ];
        }
        return $data;
    }



    //GOODS - Inventory type
    public static function accurateCheckProduct($obj, $member, $type){
        $accessToken = $obj["access_token"];
        $session = $obj["session"];
        $host = $obj["host"];

        // Header
        $header = array(
            "Authorization: Bearer $accessToken",
            "X-SESSION-ID: $session"
        );
        // Connect API
        $opts = array("http" =>
            array(
                "method" => "GET",
                "header" => $header,
                "ignore_errors" => true,
            )
        );
        $context  = stream_context_create($opts);


        $itemType = $member['item'];

        // Content
        $content = array(
            "fields" => "id,no,name,itemType",
            "sp.pageSize"=>200,
            "filter.itemType" => "INVENTORY",
            "filter.keywords" => $itemType

        );

        // URL
        $url = $host . "/accurate/api/item/list.do?" . http_build_query($content);

        $result = json_decode(file_get_contents($url, false, $context));

        $data = [];

        if($result){
            foreach ($result->d as $key => $value) {
                if($value->no === $itemType){
                    $data = [
                        'no' => $value->no,
                        'id' => $value->id
                    ];
                }
            }
        }
        return $data;
    }

    //CREATE ITEM
    public static function AccurateCreateProduct($obj, $member, $type){
        $accessToken = $obj["access_token"];
        $session = $obj["session"];
        $host = $obj["host"];

        // Header
        $header = array(
            "Authorization: Bearer $accessToken",
            "X-SESSION-ID: $session",
            "Content-Type: application/json"
        );

        $itemType = $member['item'];
        // Content
        $content = array(
            //"no"       => $itemType,
            "name"     => $itemType,
            "itemType" => "INVENTORY"
        );

        // URL
        $url = $host . "/accurate/api/item/save.do";

        // Connect API
        $opts = array("http" =>
            array(
                "method"  => "POST",
                "header"  => $header,
                "content" => json_encode($content),
                "ignore_errors" => true,
            )
        );
        $context  = stream_context_create($opts);
        //$result = file_get_contents($url, false, $context);

        $result = json_decode(file_get_contents($url, false, $context));

        $data = [];
        if($result){
            //dd($result);
            $itemId = $result->r->id;
            $itemNo = $result->r->no;
            $data = [
                'id' => $itemId,
                'no' => $itemNo
            ];
        }
        return $data;
    }

    //CREATE SALES INVOICE
    public static function AccurateCreateInvoice($obj, $invoice){
        $accessToken = $obj["access_token"];
        $session = $obj["session"];
        $host = $obj["host"];

        // Header
        $header = array(
            "Authorization: Bearer $accessToken",
            "X-SESSION-ID: $session",
            "Content-Type: application/json"
        );

        // Content
        $content = $invoice;

        // URL
        $url = $host . "/accurate/api/sales-invoice/save.do";

        // Connect API
        $opts = array("http" =>
            array(
                "method"  => "POST",
                "header"  => $header,
                "content" => json_encode($content),
                "ignore_errors" => true,
            )
        );

        $context  = stream_context_create($opts);

        $result = json_decode(file_get_contents($url, false, $context));

        return $result;
    }


    //CHECK DB //for club create/edit dropdown
    public static function accurateGetDB(){
        $accurate = AccurateAuth::find(1);
        $accessToken = $accurate['access_token'];

        // URL
        $url = "https://account.accurate.id/api/db-list.do";

        // Header
        $header = array(
            "Authorization: Bearer $accessToken"
        );
        // Connect API
        $opts = array("http" =>
            array(
                "method" => "GET",
                "header" => $header,
                "ignore_errors" => true,
            )
        );
        $context  = stream_context_create($opts);
        $response = file_get_contents($url, false, $context);
        //return json_decode($response)->{"d"};
        // Output
        $databaseList = json_decode($response)->{"d"};

        $accurateDatabase = [];
        if (count($databaseList) > 0) {
            //echo "<div>Select database</div>";
            $branches = [];
            foreach ($databaseList as $database) {
                $id = $database->{"id"};
                $alias = $database->{"alias"};

                //GET BRANCHES
                //$openDB = self::AccurateOpenDB($id, $accessToken);

                //$session = $openDB['session'];
                //$host = $openDB['host'];
                $branches = self::accurateBranchList($id);

                $accurateDatabase[] = ['id'=> $id, 'alias'=> $alias ];
                //$openDbUrl = "open-db.php?access_token=$accessToken&id=$id";
                //echo "<li>$id : <a href=\"$openDbUrl\">$alias</a></li>";
            }
        } else {
            //echo "You do not have any database, please create database from https://accurate.id";
        }
        return $accurateDatabase;
    }

    public static function accurateBranchList($id){
        $accurate = AccurateAuth::find(1);
        $accessToken = $accurate['access_token'];

        //GET BRANCHES
        $openDB = self::AccurateOpenDB($id, $accessToken);

        if($openDB){
            $session = $openDB['session'];
            $host = $openDB['host'];

            // Header
            $header = array(
                "Authorization: Bearer $accessToken",
                "X-SESSION-ID: $session"
            );

            // Content
            $content = array(
                "fields" => "id,no,name,itemType",
                "sp.pageSize"=>200,
                //"filter.itemType" => "SERVICE"

            );

            // URL
            $url = $host . "/accurate/api/branch/list.do?" . http_build_query($content);

            // Connect API
            $opts = array("http" =>
                array(
                    "method" => "GET",
                    "header" => $header,
                    "ignore_errors" => true,
                )
            );
            $context  = stream_context_create($opts);
            $result = file_get_contents($url, false, $context);

            return json_decode($result)->{"d"};
        }else{
            return false;
        }
    }


    /**************/
    //CURL

    public static function handleExceptionAndLog($exception, $module)
    {
        $curretUser = request()->user();
        $logMessage = '';

        $response = array();
        $response['status']  = 'error';
        $response['errorcode'] = 500;
        $response['message'] = trans('aol.exception_error');

        if (@$exception && is_object($exception) && !empty($exception)) {

            $response['errorcode'] = @$exception->getCode();

            if (@$exception->getMessage()) {
                $response['message']   = $exception->getMessage();
                $response['errorcode'] = 404;
            }

            if (@$exception->getMessage() && strpos(@$exception->getMessage(), 'No query results for model') !== false) {
                $response['message']   = trans('matchup.record_not_found');
                $response['errorcode'] = 404;
            }

            $logMessage .= ' Line:' . @$exception->getLine() . ' File: ' . @$exception->getFile() . ' Message: ' . @$exception->getMessage() . ' Code: ' . $response['errorcode'];

        }

        Log::write([$module => ['path' => 'logs/' . date('d-m-Y') . '/' . $module . '.log', 'level' => 500]], $logMessage);

        if ($response['errorcode'] > 500 || !is_numeric($response['errorcode']) || $response['errorcode'] < 200) {
            $response['errorcode'] = 500;
        }

        return $response;
    }

    public static function curlExec($url, $method, $params = [], $headers = [])
    {
        if(!$headers) {
            $headers[] = "content-type: application/x-www-form-urlencoded";
        }

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => "",
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_AUTOREFERER    => true,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_TIMEOUT        => 300000,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => $method,
            CURLOPT_POSTFIELDS     => $params,
            CURLOPT_HTTPHEADER     => $headers,
            CURLINFO_HEADER_OUT    => true
        ));

        $result = curl_exec($curl);
        $info   = curl_getinfo($curl);
        //print_r($info);
        curl_close($curl);


        if (false === $result) {
            throw new \Exception('curl_exec() failed: ' . curl_error($curl));
        }
        if (@$info['http_code'] == 500) {
            return array('status' => 'filenotfound');
        }

        $data = json_decode($result);
        return $data;
    }
}
