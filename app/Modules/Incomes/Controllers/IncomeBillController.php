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
use FI\Modules\Incomes\Models\Income;
use FI\Modules\Incomes\Requests\IncomeBillRequest;
use FI\Modules\Invoices\Models\InvoiceItem;

class IncomeBillController extends Controller
{
    public function create()
    {
        $income = Income::defaultQuery()->find(request('id'));

        $clientInvoices = $income->client->invoices()->orderBy('created_at', 'desc')->statusIn([
            'draft',
            'sent',
        ])->get();

        $invoices = [];

        foreach ($clientInvoices as $invoice) {
            $invoices[$invoice->id] = $invoice->formatted_created_at . ' - ' . $invoice->number . ' ' . $invoice->summary;
        }

        return view('incomes._modal_bill')
            ->with('income', $income)
            ->with('invoices', $invoices)
            ->with('redirectTo', request('redirectTo'));
    }

    public function store(IncomeBillRequest $request)
    {
        $income = Income::find(request('id'));

        $income->invoice_id = request('invoice_id');

        $income->save();

        if (request('add_line_item')) {
            $item = [
                'invoice_id' => request('invoice_id'),
                'name' => request('item_name'),
                'description' => request('item_description'),
                'quantity' => 1,
                'price' => $income->amount,
            ];

            InvoiceItem::create($item);
        }
    }
}