<?php

namespace app\controller;

use support\Request;
use support\Db;

class IndexController
{
    public function getAdminApiData(Request $request)
    {
        $where = [];
        if(!empty($request->input('order_id'))){
            $where['order_id'] = $request->input('order_id');
        }
        if(!empty($request->input('real_name'))){
            $where['real_name'] = $request->input('real_name');
        }
        if(!empty($request->input('user_phone'))){
            $where['user_phone'] = $request->input('user_phone');
        }
        $query=Db::table('eb_store_order')->where($where);
        $count=(clone $query)->count();
        $list=$query->forPage($request->input('page'),$request->input('limit'))->get(['*'])->toArray();
        foreach ($list as &$item){
            $item->split=[];
            $item->_pay_time=date('Y-m-d H:i:s',$item->pay_time);
            switch ($item->status)
                {
                    //-1 : 申请退款 -2 : 退货成功 0：待发货；1：待收货；2：已收货；3：待评价；-1：已退款
                    case '-2':
                        $item->status_name=['pics'=>[],'status_name'=>"退货成功"];
                        break;
                    case '-1':
                        $item->status_name=['pics'=>[],'status_name'=>"申请退款"];
                        break;
                    case '0':
                            $item->status_name=['pics'=>[],'status_name'=>"待发货"];
                        break;
                    case '1':
                        $item->status_name=['pics'=>[],'status_name'=>"待收货"];
                        break;
                    case '2':
                        $item->status_name=['pics'=>[],'status_name'=>"已收货"];
                        break;
                    default:
                        $item->status_name=['pics'=>[],'status_name'=>"待评价"];
                }
        }
        $data=[
            'status'=>200,
            'msg'=>'success',
            'count'=>$count,
            'data'=>$list,
        ];
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }


    public function getApiData(Request $request)
    {
        return view('index');
    }
}
