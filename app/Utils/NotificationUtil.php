<?php

namespace App\Utils;

use App\Models\Customer;
use App\Models\Employee;
use App\Models\Notification as ModelsNotification;
use App\Models\Supplier;
use App\Models\System;
use App\Models\Transaction;
use App\Models\User;
use App\Notifications\AddSaleNotification;
use App\Notifications\ContactUsNotification;
use App\Notifications\PurchaseOrderToSupplierNotification;
use App\Notifications\QuotationToCustomerNotification;
use App\Utils\Util;
use Illuminate\Support\Facades\Crypt;
use Notification;
use Illuminate\Support\Facades\Mail;

class NotificationUtil extends Util
{

    public function getMpdf()
    {
        return new \Mpdf\Mpdf([
            'tempDir' => public_path('uploads/temp'),
            'mode' => 'utf-8',
            'autoScriptToLang' => true,
            'autoLangToFont' => true,
            'autoVietnamese' => true,
            'autoArabic' => true
        ]);
    }

    /**
     * sendPurchaseOrderToSupplier
     *
     * @param [int] $transaction_id
     * @return void
     */
    public function sendPurchaseOrderToSupplier($transaction_id)
    {
        $purchase_order = Transaction::find($transaction_id);

        $supplier = Supplier::find($purchase_order->supplier_id);

        $html = view('purchase_order.pdf')
            ->with(compact('purchase_order', 'supplier'))->render();

        $mpdf = $this->getMpdf();

        $mpdf->WriteHTML($html);
        $file = config('constants.mpdf_temp_path') . '/' . time() . '_purchase-order-' . $purchase_order->po_no . '.pdf';
        $mpdf->Output($file, 'F');

        $data['email_body'] =  'this is email body';
        $data['attachment'] =  $file;
        $data['attachment_name'] =  'purchase-order-' . $purchase_order->po_no . '.pdf';

        $email = $supplier->email;
        Notification::route('mail', $email)
            ->notify(new PurchaseOrderToSupplierNotification($data));

        if (file_exists($file)) {
            unlink($file);
        }
    }

    /**
     * sendPurchaseOrderToSupplier
     *
     * @param [int] $transaction_id
     * @return void
     */
    public function sendSellInvoiceToCustomer($transaction_id, $emails)
    {
        $transaction = Transaction::find($transaction_id);

        $payment_types = $this->getPaymentTypeArrayForPos();


        $invoice_lang = System::getProperty('invoice_lang');
        if (empty($invoice_lang)) {
            $invoice_lang = request()->session()->get('language');
        }
        if ($invoice_lang == 'ar_and_en') {
            $html = view('sale_pos.partials.invoice_ar_and_end')->with(compact(
                'transaction',
                'payment_types'
            ))->render();
        } else {
            $html = view('sale_pos.partials.invoice')
                ->with(compact(
                    'transaction',
                    'payment_types',
                    'invoice_lang'
                ))->render();
        }

        $mpdf = $this->getMpdf();
        $mpdf->WriteHTML($html);
        $file = config('constants.mpdf_temp_path') . '/' . time() . '_sell-' . $transaction->invoice_no . '.pdf';
        $mpdf->Output($file, 'F');

        $data['email_body'] =  'New invoice ' . $transaction->invoice_no . ' please check the attachment.';
        $data['attachment'] =  $file;
        $data['attachment_name'] =  'sell-' . $transaction->invoice_no . '.pdf';


        $emails = explode(',', $emails);

        foreach ($emails as $email) {
            Notification::route('mail', $email)
                ->notify(new AddSaleNotification($data));
        }

        if (file_exists($file)) {
            unlink($file);
        }
    }


    /**
     * sendPurchaseOrderToSupplier
     *
     * @param [int] $transaction_id
     * @return void
     */
    public function sendQuotationToCustomer($transaction_id)
    {
        $transaction = Transaction::find($transaction_id);

        $customer = Customer::find($transaction->customer_id);
        $payment_types = $this->getPaymentTypeArrayForPos();


        $invoice_lang = System::getProperty('invoice_lang');
        if (empty($invoice_lang)) {
            $invoice_lang = request()->session()->get('language');
        }
        if ($invoice_lang == 'ar_and_en') {
            $html = view('sale_pos.partials.invoice_ar_and_end')->with(compact(
                'transaction',
                'payment_types'
            ))->render();
        } else {
            $html = view('sale_pos.partials.invoice')
                ->with(compact(
                    'transaction',
                    'payment_types',
                    'invoice_lang'
                ))->render();
        }
        $mpdf = $this->getMpdf();

        $mpdf->WriteHTML($html);
        $file = config('constants.mpdf_temp_path') . '/' . time() . '_quotation-' . $transaction->invoice_no . '.pdf';
        $mpdf->Output($file, 'F');

        $data['email_body'] =  'this is email body';
        $data['attachment'] =  $file;
        $data['attachment_name'] =  'quotation-' . $transaction->invoice_no . '.pdf';


        $email = $customer->email;
        Notification::route('mail', $email)
            ->notify(new QuotationToCustomerNotification($data));

        if (file_exists($file)) {
            unlink($file);
        }
    }

    /**
     * add notification to system
     *
     * @param [type] $data
     * @return void
     */
    public function createNotification($data)
    {
        ModelsNotification::create([
            'user_id' => $data['user_id'],
            'transaction_id' => !empty($data['transaction_id']) ? $data['transaction_id'] : null,
            'product_id' => !empty($data['product_id']) ? $data['product_id'] : null,
            'qty_available' => !empty($data['qty_available']) ? $data['qty_available'] : 0,
            'alert_quantity' => !empty($data['alert_quantity']) ? $data['alert_quantity'] : 0,
            'days' => !empty($data['days']) ? $data['days'] : 0,
            'type' => $data['type'],
            'status' => $data['status'],
            'created_by' => $data['created_by'],
        ]);

        return true;
    }

    /**
     * send login details to user by main
     *
     * @param int $employee_id
     * @return void
     */
    public function sendLoginDetails($employee_id)
    {
        $from = System::getProperty('sender_email');
        $app_name = env('APP_NAME');
        // email data
        $employee = Employee::find($employee_id);
        $user = User::find($employee->user_id);
        $employee->pass_string = Crypt::decrypt($employee->pass_string);
        $email_data = array(
            'email' => $user->email,
            'user' => $user,
            'employee' => $employee,
        );

        Mail::send('notification_template.welcom_message', $email_data, function ($message) use ($email_data, $from, $app_name) {
            $message->to($email_data['email'], $email_data['user']->name)
                ->subject('Welcome')
                ->from($from, $app_name);
        });
    }

    /**
     * send contact us details
     *
     * @param array $data
     * @return void
     */
    public function sendContactUs($data)
    {
        $email_data = array(
            'email' => env('COMPANY_EMAIL'),
            'subject' => 'Contact Us',
            'email_body' => $data['email_body'],
            'from' => $data['email'],
        );

        Notification::route('mail', $email_data['email'])
            ->notify(new ContactUsNotification($email_data));
    }
}
