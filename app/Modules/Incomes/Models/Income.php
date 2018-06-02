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

namespace FI\Modules\Incomes\Models;

use FI\Events\CheckAttachment;
use FI\Events\IncomeCreated;
use FI\Events\IncomeDeleting;
use FI\Events\IncomeSaving;
use FI\Support\CurrencyFormatter;
use FI\Support\DateFormatter;
use FI\Support\NumberFormatter;
use FI\Traits\Sortable;
use Illuminate\Database\Eloquent\Model;

class Income extends Model
{
    use Sortable;

    protected $table = 'incomes';

    protected $guarded = ['id'];

    protected $sortable = ['income_date', 'income_categories.name', 'description', 'amount'];

    public static function boot()
    {
        parent::boot();

        static::created(function ($income) {
            event(new IncomeCreated($income));
        });

        static::saved(function ($income) {
            event(new CheckAttachment($income));
        });

        static::saving(function ($income) {
            event(new IncomeSaving($income));
        });

        static::deleting(function ($income) {
            event(new IncomeDeleting($income));
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function attachments()
    {
        return $this->morphMany('FI\Modules\Attachments\Models\Attachment', 'attachable');
    }

    public function category()
    {
        return $this->belongsTo('FI\Modules\Incomes\Models\IncomeCategory');
    }

    public function client()
    {
        return $this->belongsTo('FI\Modules\Clients\Models\Client');
    }

    public function companyProfile()
    {
        return $this->belongsTo('FI\Modules\CompanyProfiles\Models\CompanyProfile');
    }

    public function custom()
    {
        return $this->hasOne('FI\Modules\CustomFields\Models\IncomeCustom');
    }

    public function invoice()
    {
        return $this->belongsTo('FI\Modules\Invoices\Models\Invoice');
    }

    public function vendor()
    {
        return $this->belongsTo('FI\Modules\Incomes\Models\IncomeVendor');
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getAttachmentPathAttribute()
    {
        return attachment_path('incomes/' . $this->id);
    }

    public function getAttachmentPermissionOptionsAttribute()
    {
        return [
            '0' => trans('fi.not_visible'),
            '1' => trans('fi.visible'),
        ];
    }

    public function getFormattedAmountAttribute()
    {
        return CurrencyFormatter::format($this->amount);
    }

    public function getFormattedTaxAttribute()
    {
        return CurrencyFormatter::format($this->tax);
    }

    public function getFormattedDescriptionAttribute()
    {
        return nl2br($this->description);
    }

    public function getFormattedIncomeDateAttribute()
    {
        return DateFormatter::format($this->income_date);
    }

    public function getFormattedNumericAmountAttribute()
    {
        return NumberFormatter::format($this->amount);
    }

    public function getFormattedNumericTaxAttribute()
    {
        return NumberFormatter::format($this->tax);
    }

    public function getHasBeenBilledAttribute()
    {
        if ($this->invoice_id) {
            return true;
        }

        return false;
    }

    public function getIsBillableAttribute()
    {
        if ($this->client_id) {
            return true;
        }

        return false;
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeCategoryId($query, $categoryId = null)
    {
        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        return $query;
    }

    public function scopeCompanyProfileId($query, $companyProfileId = null)
    {
        if ($companyProfileId) {
            $query->where('company_profile_id', $companyProfileId);
        }

        return $query;
    }

    public function scopeDefaultQuery($query)
    {
        return $query->select('incomes.*', 'income_categories.name AS category_name',
            'income_vendors.name AS vendor_name', 'clients.unique_name AS client_name')
            ->join('income_categories', 'income_categories.id', '=', 'incomes.category_id')
            ->leftJoin('income_vendors', 'income_vendors.id', '=', 'incomes.vendor_id')
            ->leftJoin('clients', 'clients.id', '=', 'incomes.client_id');
    }

    public function scopeKeywords($query, $keywords = null)
    {
        if ($keywords) {
            $keywords = strtolower($keywords);

            $query->where('incomes.income_date', 'like', '%' . $keywords . '%')
                ->orWhere('incomes.description', 'like', '%' . $keywords . '%')
                ->orWhere('income_vendors.name', 'like', '%' . $keywords . '%')
                ->orWhere('clients.name', 'like', '%' . $keywords . '%')
                ->orWhere('income_categories.name', 'like', '%' . $keywords . '%');
        }

        return $query;
    }

    public function scopeStatus($query, $status = null)
    {
        if ($status) {
            switch ($status) {
                case 'billed':
                    $query->where('invoice_id', '<>', 0);
                    break;
                case 'not_billed':
                    $query->where('client_id', '<>', 0)->where('invoice_id', '=', 0);
                    break;
                case 'not_billable':
                    $query->where('client_id', 0);
                    break;
            }
        }

        return $query;
    }

    public function scopeVendorId($query, $vendorId = null)
    {
        if ($vendorId) {
            $query->where('vendor_id', $vendorId);
        }

        return $query;
    }
}