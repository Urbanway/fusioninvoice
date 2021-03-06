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

namespace FI\Modules\API\Requests;

use FI\Modules\Clients\Requests\ClientUpdateRequest;

class APIClientUpdateRequest extends ClientUpdateRequest
{
    public function rules()
    {
        return [
            'id' => 'required',
            'email' => 'email',
            'unique_name' => 'sometimes|unique:clients,unique_name,' . $this->input('id'),
        ];
    }
}