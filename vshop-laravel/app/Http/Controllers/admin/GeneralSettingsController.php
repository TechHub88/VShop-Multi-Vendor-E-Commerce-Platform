<?php

namespace App\Http\Controllers\admin;
use App\Models\Setting;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\NotificationSetting;

class GeneralSettingsController extends Controller
{
     public function index()
    {
        $settings = Setting::first(); // Get the first settings entry
        return view('admin.settings.general', compact('settings'));
    }


    // Save the settings
  public function save(Request $request)
{
    // Validate the form data
    // $request->validate([
    //     'site_name' => 'required|string|max:255',
    //     'contact_email' => 'required|email|max:255',
    //     'contact_phone' => 'nullable|string|max:15',
    //     'system_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
    //     'favicon_icon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
    //     'lazyload_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
    //     'shop_link_banner' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
    // ]);

    // Prepare the data to be saved
    $data = $request->only('site_name', 'contact_email', 'contact_phone', 'currency_format');

    // Upload the files if they exist
    if ($request->hasFile('system_logo')) {
        $file = $request->file('system_logo');
        $filename = 'system_logo_' . time() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('uploads/system_logos'), $filename); // Move the file to public/uploads/system_logos
        $data['system_logo'] = 'uploads/system_logos/' . $filename; // Save the file path
    }

    if ($request->hasFile('favicon_icon')) {
        $file = $request->file('favicon_icon');
        $filename = 'favicon_icon_' . time() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('uploads/favicons'), $filename); // Move the file to public/uploads/favicons
        $data['favicon_icon'] = 'uploads/favicons/' . $filename; // Save the file path
    }

    if ($request->hasFile('lazyload_image')) {
        $file = $request->file('lazyload_image');
        $filename = 'lazyload_image_' . time() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('uploads/lazyload_images'), $filename); // Move the file to public/uploads/lazyload_images
        $data['lazyload_image'] = 'uploads/lazyload_images/' . $filename; // Save the file path
    }

    if ($request->hasFile('shop_link_banner')) {
        $file = $request->file('shop_link_banner');
        $filename = 'shop_link_banner_' . time() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('uploads/shop_banners'), $filename); // Move the file to public/uploads/shop_banners
        $data['shop_link_banner'] = 'uploads/shop_banners/' . $filename; // Save the file path
    }

    // Save settings to database
    Setting::updateOrCreate(['id' => 1], $data); // Assumes settings are stored in a single row

    // Redirect back with success message
    return redirect()->route('admin.settings.general')->with('success', 'Settings saved successfully.');
}

    public function update(Request $r) {
        foreach ($r->except('_token') as $key => $value) {
            Setting::updateOrCreate(['key'=>$key], ['value'=>$value]);
        }
        return back()->with('success','Settings Updated ✅');
    }


         public function notifications()
    {
        // Fetch all notification settings
        $settings = NotificationSetting::all();
        
        return view('admin.settings.notifications', compact('settings'));
    }

    // Save the updated notification settings
    public function store(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'notify_email.*' => 'boolean',
            'notify_sms.*' => 'boolean',
            'notify_web.*' => 'boolean',
        ]);

        // Loop through each setting and update the notification preferences
        foreach ($request->keys() as $key) {
            if (strpos($key, 'notify_') === 0) {
                // Extract setting key (order_confirmed, order_pending, etc.)
                $settingKey = str_replace('notify_', '', $key);
                $setting = NotificationSetting::where('key', $settingKey)->first();
                
                if ($setting) {
                    $setting->update([
                        'notify_email' => $request->get('notify_email.' . $settingKey, false),
                        'notify_sms' => $request->get('notify_sms.' . $settingKey, false),
                        'notify_web' => $request->get('notify_web.' . $settingKey, false),
                    ]);
                }
            }
        }

        // Redirect back with success message
        return redirect()->route('admin.settings.notifications')->with('success', 'Notification settings updated successfully.');
    }

   public function edit($id)
{
    $setting = \App\Models\NotificationSetting::findOrFail($id);
    return view('admin.settings.notifications_edit', compact('setting'));
}

    // Integrations Page
    public function integrations()
    {
        return view('admin.settings.integrations');
    }

    public function integrationsUpdate(Request $request)
    {
        $this->saveSettings($request->only([
            'razorpay_key',
            'razorpay_secret',
            'shiprocket_key',
        ]));

        return back()->with('success', 'Integration settings updated successfully!');
    }

    // Coupons List (Dummy)
    public function coupons()
    {
        return view('admin.coupons.index');
    }

 

    // QR Code Page
    public function qrIndex()
    {
        return view('admin.qr.index');
    }

    public function qrGenerate(Request $request)
    {
        $data = $request->qr_text;

        // later you will implement QR generation logic
        return back()->with('success', 'QR Code generated successfully for: '.$data);
    }


    // Helper to Save Settings Dynamically
    private function saveSettings($data)
    {
        foreach ($data as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }
}
}
