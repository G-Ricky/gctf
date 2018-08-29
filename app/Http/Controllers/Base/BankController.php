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
}
