<?php

/**
 * InvoicePlane
 *
 * @package     InvoicePlane
 * @author      InvoicePlane Developers & Contributors
 * @copyright   Copyright (C) 2014 - 2018 InvoicePlane
 * @license     https://invoiceplane.com/license
 * @link        https://invoiceplane.com
 *
 * Based on FusionInvoice by Jesse Terry (FusionInvoice, LLC)
 */

namespace FI\Modules\Incomes\Controllers;

use FI\Http\Controllers\Controller;
use FI\Modules\Incomes\Models\IncomeCategory;
use FI\Modules\Incomes\Models\IncomeVendor;

class IncomeLookupController extends Controller
{
    public function lookupCategory()
    {
        $incomes = IncomeCategory::select('name')
            ->where('name', 'like', '%' . request('query') . '%')
            ->orderBy('name')
            ->get();

        $list = [];

        foreach ($incomes as $income) {
            $list[]['value'] = $income->name;
        }

        return json_encode($list);
    }

    public function lookupVendor()
    {
        $incomes = IncomeVendor::select('name')
            ->where('name', 'like', '%' . request('query') . '%')
            ->orderBy('name')
            ->get();

        $list = [];

        foreach ($incomes as $income) {
            $list[]['value'] = $income->name;
        }

        return json_encode($list);
    }
}