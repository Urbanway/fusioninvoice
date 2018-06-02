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
use FI\Modules\CompanyProfiles\Models\CompanyProfile;
use FI\Modules\CustomFields\Models\CustomField;
use FI\Modules\Incomes\Models\Income;
use FI\Modules\Incomes\Requests\IncomeRequest;
use FI\Support\DateFormatter;
use FI\Support\NumberFormatter;
use FI\Traits\ReturnUrl;

class IncomeCreateController extends Controller
{
    use ReturnUrl;

    public function create()
    {
        return view('incomes.form')
            ->with('editMode', false)
            ->with('companyProfiles', CompanyProfile::getList())
            ->with('currentDate', DateFormatter::format(date('Y-m-d')))
            ->with('customFields', CustomField::forTable('incomes')->get());
    }

    public function store(IncomeRequest $request)
    {
        $record = request()->except('attachments', 'custom');

        $record['income_date'] = DateFormatter::unformat($record['income_date']);
        $record['amount'] = NumberFormatter::unformat($record['amount']);
        $record['tax'] = ($record['tax']) ? NumberFormatter::unformat($record['tax']) : 0;

        $income = Income::create($record);

        $income->custom->update(request('custom', []));

        return redirect($this->getReturnUrl())
            ->with('alertSuccess', trans('fi.record_successfully_created'));
    }
}