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
use FI\Modules\Incomes\Models\Income;
use FI\Modules\Incomes\Models\IncomeCategory;
use FI\Modules\Incomes\Models\IncomeVendor;
use FI\Traits\ReturnUrl;

class IncomeController extends Controller
{
    use ReturnUrl;

    public function index()
    {
        $this->setReturnUrl();

        $incomes = Income::defaultQuery()
            ->keywords(request('search'))
            ->categoryId(request('category'))
            ->vendorId(request('vendor'))
            ->status(request('status'))
            ->companyProfileId(request('company_profile'))
            ->sortable(['income_date' => 'desc'])
            ->paginate(config('fi.defaultNumPerPage'));

        return view('incomes.index')
            ->with('incomes', $incomes)
            ->with('displaySearch', true)
            ->with('categories', ['' => trans('fi.all_categories')] + IncomeCategory::getList())
            ->with('vendors', ['' => trans('fi.all_vendors')] + IncomeVendor::getList())
            ->with('statuses', ['' => trans('fi.all_statuses'), 'billed' => trans('fi.billed'), 'not_billed' => trans('fi.not_billed'), 'not_billable' => trans('fi.not_billable')])
            ->with('companyProfiles', ['' => trans('fi.all_company_profiles')] + CompanyProfile::getList());
    }

    public function delete($id)
    {
        Income::destroy($id);

        return redirect($this->getReturnUrl())
            ->with('alertInfo', trans('fi.record_successfully_deleted'));
    }

    public function bulkDelete()
    {
        Income::destroy(request('ids'));
    }
}