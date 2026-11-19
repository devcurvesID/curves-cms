<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Ixudra\Curl\Facades\Curl;

class BLEController extends Controller
{
    public function get_member() {
        $members = Curl::to('http://159.89.208.182/curves/api/ble-get-member')
            ->withContentType('application/json')
            ->withHeader('Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6IjYwY2M5NGIwNmUzNzU5ZjdjMGE4ZWE0YTQ1YzhiNmVmZmNkNWZhNjA3OWYyNzA2NWRiYTIxNjQ4M2RmZGEyNjhmZGI5ODkxMzhlNzdiZDkzIn0.eyJhdWQiOiIxIiwianRpIjoiNjBjYzk0YjA2ZTM3NTlmN2MwYThlYTRhNDVjOGI2ZWZmY2Q1ZmE2MDc5ZjI3MDY1ZGJhMjE2NDgzZGZkYTI2OGZkYjk4OTEzOGU3N2JkOTMiLCJpYXQiOjE1NTE2NzE1NzYsIm5iZiI6MTU1MTY3MTU3NiwiZXhwIjoxNTgzMjkzOTc2LCJzdWIiOiIxIiwic2NvcGVzIjpbXX0.UVe54KOe9UshIDxrame2yj9yVufyxV3TIxTN9wPmzqiQwKGUlG0Zb-SdksfaCenybYMS9NKsR7T4hzlMyKfeFshoNYkxCel8YbNgzYHlF4F5g5mqQDIR5aP0kXfSaJT2a2foILusYPGGFrGr-eLp53y7fSDswkO8yRDXoEpKfsuq4TkojCJAzBIYAMwrQHs4jBPTr2ZqeojXPh8KyAXjyx4MKij_vkuG9PVEP4a1l07mTca_Ze-Ka9Hd-cydtZpR-0PBaq-uGxFmOk6a1dRry105JB7PoVpbDD_983Wbwq3rGyV9sR4W_s-bIFHX10Td6qLJm7SJnWKr9b37iV0ClMl_uaidOFukVYzKQaVwtUfrNLF_pMluCtih6pt2rxU4xXts1pl7R9dafSbBnzlXY6RP87A5Mz24K4f7KWrsh7od8l3OlOEd__nTNnPZ4Je_I6Om7G1eehq9wNm-xh7HKUSkxkSi6EXMDiM62CR-MGFlsHE1Gh4Q7JgnZBVQshqVpBmdoG4P5YPA3cng-LRzowgiV20AGwS_YKbdw-bqwwQxtU1S_4Rhm0lie6MIbiKXnL1zwTJlw4XDVlwIPEHoLsJ8zLTqCQvy87pIBAoGmK-ZxZw2fuA_2kXPsW9-N6HTq2p3WgpEaCMsrNc83Bzdm2QBmxz-EvHLMNQPqANAYwI')
            ->asJson()
            ->get();

//        $members = Curl::to('localhost/cfindonesialite/public/api/ble-get-member')
//            ->withContentType('application/json')
//            ->withHeader('Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6IjRkYjIzZGVhYmZmYjhjNjQ3YWY1MWQ5NmMxZTE5N2RlMzA5MDU1N2UxZWI0ZDljY2U0MTg0Y2NlM2ZiZDJlZjViMDQ0MDdjNDdlNmViMmVjIn0.eyJhdWQiOiIxIiwianRpIjoiNGRiMjNkZWFiZmZiOGM2NDdhZjUxZDk2YzFlMTk3ZGUzMDkwNTU3ZTFlYjRkOWNjZTQxODRjY2UzZmJkMmVmNWIwNDQwN2M0N2U2ZWIyZWMiLCJpYXQiOjE1NDQwNzY1MTYsIm5iZiI6MTU0NDA3NjUxNiwiZXhwIjoxNTc1NjEyNTE1LCJzdWIiOiIxMDgiLCJzY29wZXMiOltdfQ.Gvi4iwGhdA8dPc7e8Z5NqryXgODaIwkjBkogbTtvdHwQVVfEIr17VjjDP3B71yWlfW6xyDNDLj2PfJi5Jrh7PIM67jQ1qGoYNrEY2Frfb9Udl5m2TvDCjes6eywMDugE8Zr3zIrXywzrJ5abOdH-nDfnYsSFeiewXFcE5dVfMvxrp2E4DHnhYyee9JBu5cf2HD9rvVROcOiyaJSsOURFm8BVUsmmU3zN7MTJ5zebZygWngoJMQNiRTC0qDZiqZUowouxI5M9WID-eJPL0-zeBwkvs8iJW5Yfv9g1ygZbOkGeQr2IE2bCqvrMzqQcjeYunvXSN_9-yUqCbi88U29aV7AnSvXr_w6Bn5T0oQkmyIIR6RGDL7lRfbmkik9qlPlRAsIQEEOc39L7ul3VS76xpN2tSHw5YHTdR9oel-Di8OoVepbItE7HBciM5dGSodpQt6o5RJEGsDrE10s4UVWnzD_nJlATGVVtPmBtCKth4xMfpdRacUG1Nmi0iLw9XHKfr4NbqQqyOHnihNJyOm7kGFBhVAt4lZyBoemB5toewXy-xANOh9pJMgVJjZqYzP54Lm73TPl2Lt8SKrOpco7dMGVFHtelfU4raCw6ub2zY46mWnO9eWHpjc53rFNJVqN0n_oUeW66cqeqfBlBSI5T_XX5-FH7IT12-mnlj7LZBWA')
//            ->asJson()
//            ->get();

        return view('developer.ble-part', compact('members'));
    }
}
