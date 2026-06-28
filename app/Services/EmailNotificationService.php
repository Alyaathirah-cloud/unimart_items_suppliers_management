<?php

namespace App\Services;

use App\Mail\LowStockMail;
use App\Mail\ExpiryReminderMail;
use App\Mail\NewStaffWelcomeMail;
use App\Mail\StaffApprovedMail;
use App\Mail\PurchaseOrderApprovedMail;
use App\Mail\PurchaseOrderCreatedToSupplier;
use App\Mail\PurchaseOrderUpdatedToSupplier;
use App\Mail\ReturnRequestApprovedMail;
use App\Mail\ReturnRequestCreatedToSupplier;
use App\Mail\InvoiceMail;
use App\Mail\SupplierInviteMail;
use App\Models\PurchaseOrder;
use App\Models\ReturnRequest;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

/**
 * EmailNotificationService
 *
 * Centralised service for sending transactional email notifications.
 * Reuses the existing Laravel Mail configuration (SMTP via Gmail).
 * All sends are wrapped with logging so failures never crash the application.
 */
class EmailNotificationService
{
    /**
     * Core send wrapper — logs success and failure, never throws.
     *
     * @param  string|null  $toEmail   Recipient address; skips if null/empty.
     * @param  mixed        $mailable  Any Laravel Mailable instance.
     * @param  string       $context   Human-readable label used in log entries.
     * @return array{success: bool, error: string|null}
     */
    protected function send(?string $toEmail, $mailable, string $context): array
    {
        if (empty($toEmail)) {
            Log::warning("Email skipped — no recipient email for: {$context}");
            return ['success' => false, 'error' => 'Recipient email is not set.'];
        }

        try {
            Mail::to($toEmail)->send($mailable);
            Log::info("Email sent: {$context}", ['to' => $toEmail]);
            return ['success' => true, 'error' => null];
        } catch (\Exception $e) {
            Log::error("Email failed: {$context}", ['to' => $toEmail, 'error' => $e->getMessage()]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    // ────────────────────────────────────────────────────────────────────────
    // OWNER NOTIFICATIONS — automated stock/expiry alerts
    // ────────────────────────────────────────────────────────────────────────

    /**
     * Send a low-stock alert email to the owner.
     */
    public function sendLowStockAlert(string $ownerEmail, string $productName, int $currentQty): array
    {
        return $this->send(
            $ownerEmail,
            new LowStockMail($productName, $currentQty),
            "Low stock – {$productName}"
        );
    }

    /**
     * Send an expiry reminder email to the owner.
     */
    public function sendExpiryReminder(string $ownerEmail, string $productName, string $expiryDate): array
    {
        return $this->send(
            $ownerEmail,
            new ExpiryReminderMail($productName, $expiryDate),
            "Expiry reminder – {$productName}"
        );
    }

    // ────────────────────────────────────────────────────────────────────────
    // STAFF NOTIFICATIONS
    // ────────────────────────────────────────────────────────────────────────

    /**
     * Send a welcome email to a newly created staff member.
     * Includes their temporary password so they can log in immediately.
     */
    public function sendNewStaffWelcome(string $staffEmail, string $staffName, string $temporaryPassword): array
    {
        return $this->send(
            $staffEmail,
            new NewStaffWelcomeMail($staffName, $staffEmail, $temporaryPassword),
            "New staff welcome – {$staffName}"
        );
    }

    /**
     * Send an approval notification email to a staff member.
     */
    public function sendStaffApproved(string $staffEmail, string $staffName): array
    {
        return $this->send(
            $staffEmail,
            new StaffApprovedMail($staffName, $staffEmail),
            "Staff approved – {$staffName}"
        );
    }

    /**
     * Send a portal invitation email to a supplier.
     * $data must contain: contact_person, company_name, portal_url, email, password, owner_phone, telegram_token
     */
    public function sendSupplierInvitation(string $supplierEmail, array $data): array
    {
        return $this->send(
            $supplierEmail,
            new SupplierInviteMail($data),
            "Supplier invitation – {$supplierEmail}"
        );
    }

    // ────────────────────────────────────────────────────────────────────────
    // PURCHASE ORDER NOTIFICATIONS — sent to BOTH owner and supplier
    // ────────────────────────────────────────────────────────────────────────

    /**
     * Notify the supplier that a new PO has been created.
     */
    public function sendPurchaseOrderCreatedToSupplier(PurchaseOrder $po): array
    {
        return $this->sendPurchaseOrderSupplierNotification($po, 'created');
    }

    /**
     * Notify the supplier that an existing PO has been updated.
     */
    public function sendPurchaseOrderUpdatedToSupplier(PurchaseOrder $po): array
    {
        return $this->sendPurchaseOrderSupplierNotification($po, 'updated');
    }

    /**
     * Helper to send unified PO notification.
     */
    protected function sendPurchaseOrderSupplierNotification(PurchaseOrder $po, string $action): array
    {
        $supplier = $po->supplier;
        if (!$supplier || !$supplier->contact_email) {
            return ['success' => false, 'error' => 'No supplier email available.'];
        }

        return $this->send(
            $supplier->contact_email,
            new \App\Mail\PurchaseOrderSupplierNotification($po, $action),
            "PO {$action} (supplier) – {$po->po_number}"
        );
    }

    /**
     * Notify both the owner and supplier that a PO has been approved.
     * Either email address may be null — those are silently skipped.
     *
     * @param  string|null  $ownerEmail
     * @param  string|null  $supplierEmail
     * @param  string       $poNumber      e.g. "PO-0015"
     * @param  string|null  $details       Optional summary of items/totals.
     * @return array{owner?: array, supplier?: array}
     */
    public function sendPurchaseOrderApproved(
        ?string $ownerEmail,
        ?string $supplierEmail,
        string $poNumber,
        ?string $details = null
    ): array {
        $results = [];

        if ($ownerEmail) {
            $results['owner'] = $this->send(
                $ownerEmail,
                new PurchaseOrderApprovedMail($poNumber, 'owner', $details),
                "PO approved (owner) – {$poNumber}"
            );
        }

        if ($supplierEmail) {
            $results['supplier'] = $this->send(
                $supplierEmail,
                new PurchaseOrderApprovedMail($poNumber, 'supplier', $details),
                "PO approved (supplier) – {$poNumber}"
            );
        }

        return $results;
    }

    // ────────────────────────────────────────────────────────────────────────
    // RETURN REQUEST NOTIFICATIONS — sent to BOTH owner and supplier
    // ────────────────────────────────────────────────────────────────────────

    /**
     * Notify the supplier that a new return request has been created.
     */
    public function sendReturnRequestCreatedToSupplier(ReturnRequest $returnRequest): array
    {
        $supplier = $returnRequest->supplier;
        if (!$supplier || !$supplier->contact_email) {
            return ['success' => false, 'error' => 'No supplier email available.'];
        }

        $itemName = optional($returnRequest->lines->first())->item->name ?? 'Multiple Items';
        if ($returnRequest->lines->count() > 1) {
            $itemName .= ' (+' . ($returnRequest->lines->count() - 1) . ' more)';
        }
        $loginUrl = $supplier->portal_link ?? route('supplier.login');
        $reason = $returnRequest->lines->pluck('reason')->filter()->unique()->join(', ') ?: 'Not specified';

        return $this->send(
            $supplier->contact_email,
            new ReturnRequestCreatedToSupplier($returnRequest->return_number, $itemName, $reason, $loginUrl),
            "Return created (supplier) – {$returnRequest->return_number}"
        );
    }

    /**
     * Notify both the owner and supplier that a return request has been approved.
     * Either email address may be null — those are silently skipped.
     *
     * @param  string|null  $ownerEmail
     * @param  string|null  $supplierEmail
     * @param  string       $returnId      e.g. "RR-20260621-0001"
     * @return array{owner?: array, supplier?: array}
     */
    public function sendReturnRequestApproved(
        ?string $ownerEmail,
        ?string $supplierEmail,
        string $returnId
    ): array {
        $results = [];

        if ($ownerEmail) {
            $results['owner'] = $this->send(
                $ownerEmail,
                new ReturnRequestApprovedMail($returnId, 'owner'),
                "Return approved (owner) – {$returnId}"
            );
        }

        if ($supplierEmail) {
            $results['supplier'] = $this->send(
                $supplierEmail,
                new ReturnRequestApprovedMail($returnId, 'supplier'),
                "Return approved (supplier) – {$returnId}"
            );
        }

        return $results;
    }

    // ────────────────────────────────────────────────────────────────────────
    // INVOICE NOTIFICATIONS — sent to supplier
    // ────────────────────────────────────────────────────────────────────────

    /**
     * Send an invoice email to the supplier, optionally with a PDF attached.
     *
     * @param  string       $supplierEmail
     * @param  string       $supplierName
     * @param  string       $invoiceNumber   e.g. "INV-PO-0015"
     * @param  string|null  $pdfPath         Absolute server path to the PDF, if available.
     */
    public function sendInvoice(
        string $supplierEmail,
        string $supplierName,
        string $invoiceNumber,
        ?string $pdfPath = null
    ): array {
        return $this->send(
            $supplierEmail,
            new InvoiceMail($invoiceNumber, $supplierName, $pdfPath),
            "Invoice – {$invoiceNumber}"
        );
    }
}
