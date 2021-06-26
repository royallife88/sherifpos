<?php

namespace App\Utils;

use App\Models\Supplier;
use App\Models\Transaction;
use App\Notifications\PurchaseOrderToSupplierNotification;
use App\Utils\Util;
use Notification;

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
}
