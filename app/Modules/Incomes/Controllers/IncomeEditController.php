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

class IncomeEditController extends Controller
{
    use ReturnUrl;

    public function edit($id)
    {
        return view('incomes.form')
            ->with('editMode', true)
            ->with('companyProfiles', CompanyProfile::getList())
            ->with('income', $income = Income::defaultQuery()->find($id))
            ->with('customFields', CustomField::forTable('incomes')->get());
    }

    public function update(IncomeRequest $request, $id)
    {
        $record = request()->except('attachments', 'custom');

        $record['income_date'] = DateFormatter::unformat($record['income_date']);
        $record['amount'] = NumberFormatter::unformat($record['amount']);
        $record['tax'] = ($record['tax']) ? NumberFormatter::unformat($record['tax']) : 0;

        $income = Income::find($id);

        $income->fill($record);

        $income->save();

        $income->custom->update(request('custom', []));

        return redirect($this->getReturnUrl())
            ->with('alertSuccess', trans('fi.record_successfully_updated'));
    }
}