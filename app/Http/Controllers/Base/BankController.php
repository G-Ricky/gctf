<?php

namespace App\Http\Controllers\Base;

use App\Http\Controllers\Controller;
use App\Models\Base\Bank;
use Illuminate\Http\Request;

class BankController extends Controller
{
    protected $banks;

    public function __construct(Bank $banks)
    {
        $this->banks = $banks;
    }

    public function index()
    {
        return view('base.bank.index');
    }

    public function add(Request $request)
    {
        $this->validate($request, [
            'name'        => 'required|string|max:256',
            'description' => 'required|string|max:1024',
        ]);

        $data = $request->all();
        if(array_key_exists('is_hidden', $data) and $data['is_hidden'] === 'on') {
            $data['is_hidden'] = true;
        }else{
            $data['is_hidden'] = false;
        }

        $success = $this->banks->add($data);
        return [
            'status'  => 200,
            'success' => $success
        ];
    }

    public function edit()
    {

    }

    public function list(Request $request)
    {
        $data['page'] = $request->query('page', 1);
        $data['pageSize'] = $request->query('pageSize', 20);

        $result = $this->banks->list($data['page'], min($data['pageSize'], 30));

        return [
            'status'  => 200,
            'success' => true,
            'data'    => $result['data'],
            'page'    => array_only($result, [
                'current_page', 'first_page_url', 'from', 'last_page', 'last_page_url', 'next_page_url', 'path',
                'per_page', 'prev_page_url', 'to'
            ])
        ];
    }

    public function remove()
    {

    }
}
