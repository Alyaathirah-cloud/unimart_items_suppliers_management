<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use App\Models\Setting;
use Illuminate\Support\Facades\Log;

class SupplierController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:owner']);
    }

    /**
     * Display a listing of suppliers.
     */
    public function index()
    {
        $suppliers = Supplier::withCount('items')->orderBy('name')->get();
        return view('owner.suppliers.index', compact('suppliers'));
    }

    /**
     * Show the form for creating a new supplier.
     */
    public function create()
    {
        return view('owner.suppliers.create');
    }

    /**
     * Store a newly created supplier in database.
     */
    public function store(Request $request)
    {
        $portalEnabled = $request->boolean('portal_enabled');

        $request->validate([
            'name' => 'required|string|max:255|unique:suppliers',
            'contact_person' => 'nullable|string|max:255',
            'contact_email' => ['required', 'email', 'max:255', Rule::unique('users', 'email'), Rule::unique('suppliers', 'contact_email')],
            'contact_phone' => ['nullable', 'string', 'regex:/^(\+?601|01)[0-9]{8,9}$/'],
            'address_line_1' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'portal_enabled' => 'sometimes|boolean',
            'accepts_returns' => 'sometimes|boolean',
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ], [
            'contact_phone.regex' => 'The phone format is invalid. It must be a Malaysian number (e.g. 0123456789, 60123456789 or +60123456789).',
        ]);

        $supplier = DB::transaction(function () use ($request, $portalEnabled) {
            $supplierData = [
                'name' => $request->name,
                'contact_email' => $request->contact_email,
                'contact_phone' => $request->contact_phone,
                'contact_person' => $request->contact_person,
                'address_line_1' => $request->address_line_1,
                'city' => $request->city,
                'state' => $request->state,
                'postal_code' => $request->postal_code,
                'country' => $request->country,
                'portal_enabled' => $portalEnabled,
                'portal_link' => $portalEnabled ? route('supplier.login') : null,
                'accepts_returns' => $request->boolean('accepts_returns'),
                'temporary_password' => null,
                'invite_email_status' => $portalEnabled ? 'pending' : 'disabled',
                'invite_whatsapp_status' => $portalEnabled ? 'pending' : 'disabled',
            ];

            if ($portalEnabled) {
                $password = $request->filled('password') ? $request->password : Str::random(10);

                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->contact_email,
                    'password' => bcrypt($password),
                    'role' => 'supplier',
                    'must_change_password' => true,
                ]);

                $supplierData['user_id'] = $user->id;
                $supplierData['temporary_password'] = $password;
            }

            return Supplier::create($supplierData);
        });

        if ($portalEnabled) {
            $this->sendInvites($supplier, $supplier->temporary_password);
            
            \App\Models\Notification::create([
                'user_id' => $supplier->user_id,
                'type' => 'supplier_invited',
                'message' => 'Your supplier portal account has been created.',
                'data' => [
                    'supplier_name' => $supplier->name,
                    'login_url' => route('supplier.login'),
                ],
            ]);
        }

        $successMessage = 'Supplier created successfully!';
        if ($portalEnabled) {
            $successMessage .= ' Temporary password: ' . $supplier->temporary_password;
        }

        return redirect()->route('owner.suppliers.index')->with('success', $successMessage);
    }

    /**
     * Show the form for editing the supplier.
     */
    public function edit(Supplier $supplier)
    {
        $whatsappPhone = null;
        if ($supplier->contact_phone) {
            $whatsappPhone = (new \App\Services\WhatsAppService())->formatPhoneForDisplay($supplier->contact_phone);
        }

        return view('owner.suppliers.edit', compact('supplier', 'whatsappPhone'));
    }

    /**
     * Update the supplier in database.
     */
    public function update(Request $request, Supplier $supplier)
    {
        $portalEnabled = $request->boolean('portal_enabled');

        $request->validate([
            'name' => 'required|string|max:255|unique:suppliers,name,' . $supplier->id,
            'contact_person' => 'nullable|string|max:255',
            'contact_email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($supplier->user_id),
                Rule::unique('suppliers', 'contact_email')->ignore($supplier->id),
            ],
            'contact_phone' => ['nullable', 'string', 'regex:/^(\+?601|01)[0-9]{8,9}$/'],
            'address_line_1' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'portal_enabled' => 'sometimes|boolean',
            'accepts_returns' => 'sometimes|boolean',
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ], [
            'contact_phone.regex' => 'The phone format is invalid. It must be a Malaysian number (e.g. 0123456789, 60123456789 or +60123456789).',
        ]);

        $supplier->update([
            'name' => $request->name,
            'contact_email' => $request->contact_email,
            'contact_phone' => $request->contact_phone,
            'contact_person' => $request->contact_person,
            'address_line_1' => $request->address_line_1,
            'city' => $request->city,
            'state' => $request->state,
            'postal_code' => $request->postal_code,
            'country' => $request->country,
            'portal_enabled' => $portalEnabled,
            'accepts_returns' => $request->boolean('accepts_returns'),
            'portal_link' => $portalEnabled ? ($supplier->portal_link ?? route('supplier.login')) : $supplier->portal_link,
        ]);

        $newlyEnabled = false;
        $generatedPassword = null;
        $successMessage = 'Supplier updated successfully!';

        if ($portalEnabled) {
            if ($supplier->user) {
                $userData = [
                    'name' => $request->name,
                    'email' => $request->contact_email,
                ];

                if ($request->filled('password')) {
                    $generatedPassword = $request->password;
                    $userData['password'] = bcrypt($generatedPassword);
                    $userData['must_change_password'] = true;
                    $successMessage .= ' Password reset to: ' . $generatedPassword;
                }

                $supplier->user->update($userData);

                if ($generatedPassword) {
                    $supplier->update([
                        'temporary_password' => $generatedPassword,
                        'invite_email_status' => 'pending',
                        'invite_whatsapp_status' => 'pending',
                    ]);
                }
            } else {
                $generatedPassword = $request->filled('password') ? $request->password : Str::random(10);

                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->contact_email,
                    'password' => bcrypt($generatedPassword),
                    'role' => 'supplier',
                    'must_change_password' => true,
                ]);
                $supplier->update([
                    'user_id' => $user->id,
                    'temporary_password' => $generatedPassword,
                    'invite_email_status' => 'pending',
                    'invite_whatsapp_status' => 'pending',
                    'portal_link' => route('supplier.login'),
                ]);
                $newlyEnabled = true;
                $successMessage .= ' Temporary password: ' . $generatedPassword;
            }
        } else {
            if ($supplier->user) {
                $supplier->user->delete();
                $supplier->update([
                    'user_id' => null,
                    'temporary_password' => null,
                    'invite_email_status' => 'disabled',
                    'invite_whatsapp_status' => 'disabled',
                ]);
            }
        }

        if ($newlyEnabled) {
            $this->sendInvites($supplier, $generatedPassword);
            
            \App\Models\Notification::create([
                'user_id' => $supplier->user_id,
                'type' => 'supplier_invited',
                'message' => 'Your supplier portal account has been created.',
                'data' => [
                    'supplier_name' => $supplier->name,
                    'login_url' => route('supplier.login'),
                ],
            ]);
        }

        return redirect()->route('owner.suppliers.index')->with('success', $successMessage);
    }

    /**
     * Delete the supplier.
     */
    public function destroy(Supplier $supplier)
    {
        // Only delete if no items or orders are associated
        if ($supplier->items()->count() > 0 || $supplier->purchaseOrders()->count() > 0) {
            return back()->with('error', 'Cannot delete supplier with associated items or orders!');
        }

        if ($supplier->user) {
            $supplier->user->delete();
        }

        $supplier->delete();
        return redirect()->route('owner.suppliers.index')->with('success', 'Supplier deleted successfully!');
    }

    public function exportPdf()
    {
        $suppliers = Supplier::withCount('items')->orderBy('name')->get();

        $filename = 'suppliers-' . now()->format('Y-m-d') . '.csv';
        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($suppliers) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Supplier Name', 'Contact Person', 'Email', 'Phone', 'City', 'Country', 'Portal Enabled', 'Total Items']);
            foreach ($suppliers as $supplier) {
                fputcsv($handle, [
                    $supplier->name,
                    $supplier->contact_person ?? '',
                    $supplier->contact_email ?? '',
                    $supplier->contact_phone ?? '',
                    $supplier->city ?? '',
                    $supplier->country ?? '',
                    $supplier->portal_enabled ? 'Yes' : 'No',
                    $supplier->items_count,
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    protected function getInviteData(Supplier $supplier, $password = '********')
    {
        $setting = \App\Models\Setting::first();
        $companyName = $setting->company_name ?? '22UniMart';
        $ownerPhone = $setting->contact_phone ?? 'N/A';

        return [
            'company_name' => $companyName,
            'contact_person' => $supplier->contact_person ?: $supplier->name,
            'portal_url' => $supplier->portal_link ?? route('supplier.login'),
            'email' => $supplier->contact_email,
            'password' => $password,
            'owner_phone' => $ownerPhone,
            'telegram_token' => $supplier->generateTelegramToken(),
        ];
    }

    protected function sendInvites(Supplier $supplier, $password)
    {
        $data = $this->getInviteData($supplier, $password);

        try {
            Mail::to($supplier->contact_email)
                ->send(new \App\Mail\SupplierInviteMail($data));
            $supplier->update(['invite_email_status' => 'sent']);
        } catch (\Exception $e) {
            $supplier->update(['invite_email_status' => 'failed']);
            session()->flash('warning', "⚠️ Supplier registered but email invite failed to send. Please resend manually from the Supplier Profile page.");
        }

        if ($supplier->contact_phone) {
            try {
                $callMeBotPhone = trim((string) Setting::get('callmebot_phone', config('services.callmebot.phone')));
                $callMeBotApiKey = trim((string) Setting::get('callmebot_api_key', config('services.callmebot.apikey')));
                $whatsappEnabled = Setting::get('whatsapp_enabled', true);

                if ($whatsappEnabled && $callMeBotPhone !== '' && $callMeBotApiKey !== '') {
                    $cb = new \App\Services\CallMeBotService();
                    $cb->sendSupplierInvite($supplier->contact_phone, $data);
                    $supplier->update(['invite_whatsapp_status' => 'sent']);
                } else {
                    $whatsapp = new \App\Services\WhatsAppService();
                    $whatsapp->sendInvite($supplier->contact_phone, $data);
                    $supplier->update(['invite_whatsapp_status' => 'sent']);
                }
            } catch (\Exception $e) {
                $supplier->update(['invite_whatsapp_status' => 'failed']);
                Log::channel('whatsapp_alerts')->error('Failed to send supplier invite via WhatsApp', ['supplier_id' => $supplier->id, 'error' => $e->getMessage()]);
                $fullMsg = "WhatsApp invite failed to send.\n\nPortal: " . ($supplier->portal_link ?? route('supplier.login')) . "\n\nMessage content:\n";
                $fullMsg .= "Email: {$data['email']}\nTemporary Password: {$data['password']}\n\nPlease log in and change your password after first login.";
                session()->flash('warning', $fullMsg);
            }
        } else {
            $supplier->update(['invite_whatsapp_status' => 'missing']);
        }
    }

    public function resendEmailInvite(Supplier $supplier)
    {
        if (!$supplier->portal_enabled || !$supplier->user) {
            return back()->with('error', 'Portal access is not enabled for this supplier.');
        }

        $password = Str::random(10);
        $supplier->user->update([
            'password' => bcrypt($password),
            'must_change_password' => true,
        ]);
        $supplier->update([
            'temporary_password' => $password,
            'portal_link' => $supplier->portal_link ?? route('supplier.login'),
            'invite_email_status' => 'pending',
        ]);

        try {
            $data = $this->getInviteData($supplier, $password);
            Mail::to($supplier->contact_email)
                ->send(new \App\Mail\SupplierInviteMail($data));
            $supplier->update(['invite_email_status' => 'sent']);
            return back()->with('success', '✅ Email sent successfully to ' . $supplier->contact_email);
        } catch (\Exception $e) {
            $supplier->update(['invite_email_status' => 'failed']);
            return back()->with('error', '❌ Email failed — check SMTP settings in .env. ' . $e->getMessage());
        }
    }

    public function resendWhatsAppInvite(Supplier $supplier)
    {
        if (!$supplier->portal_enabled || !$supplier->user) {
            return back()->with('error', 'Portal access is not enabled for this supplier.');
        }

        if (!$supplier->contact_phone) {
            return back()->with('error', 'Supplier does not have a contact phone number.');
        }

        $password = Str::random(10);
        $supplier->user->update([
            'password' => bcrypt($password),
            'must_change_password' => true,
        ]);
        $supplier->update([
            'temporary_password' => $password,
            'portal_link' => $supplier->portal_link ?? route('supplier.login'),
            'invite_whatsapp_status' => 'pending',
        ]);

            try {
                $data = $this->getInviteData($supplier, $password);
                $callMeBotPhone = trim((string) Setting::get('callmebot_phone', config('services.callmebot.phone')));
                $callMeBotApiKey = trim((string) Setting::get('callmebot_api_key', config('services.callmebot.apikey')));
                $whatsappEnabled = Setting::get('whatsapp_enabled', true);

                if ($whatsappEnabled && $callMeBotPhone !== '' && $callMeBotApiKey !== '') {
                    $cb = new \App\Services\CallMeBotService();
                    $cb->sendSupplierInvite($supplier->contact_phone, $data);
                } else {
                    $whatsapp = new \App\Services\WhatsAppService();
                    $whatsapp->sendInvite($supplier->contact_phone, $data);
                }

                $supplier->update(['invite_whatsapp_status' => 'sent']);
                return back()->with('success', 'WhatsApp invite resent successfully. Temporary password: ' . $password);
            } catch (\Exception $e) {
                $supplier->update(['invite_whatsapp_status' => 'failed']);
                Log::channel('whatsapp_alerts')->error('Failed to resend whatsapp invite', ['supplier_id' => $supplier->id, 'error' => $e->getMessage()]);
                $fullMsg = "WhatsApp invite failed to send.\n\nPortal: " . ($supplier->portal_link ?? route('supplier.login')) . "\n\nMessage content:\n";
                $fullMsg .= "Email: {$data['email']}\nTemporary Password: {$data['password']}\n\nPlease log in and change your password after first login.";
                return back()->with('error', $fullMsg);
            }
    }
}
