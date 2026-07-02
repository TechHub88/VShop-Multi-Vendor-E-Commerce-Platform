<!DOCTYPE html>
<html>
<head>
    <title>New Product Enquiry</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #eee; border-radius: 5px;">
        <h2 style="color: #004392; border-bottom: 2px solid #eee; padding-bottom: 10px;">New Enquiry Received</h2>
        
        <p>Hello,</p>
        <p>You have received a new enquiry for your product/service: <strong>{{ $enquiry->product_service }}</strong>.</p>
        
        <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
            <tr>
                <td style="padding: 10px; border: 1px solid #ddd; background-color: #f9f9f9; width: 150px;"><strong>Enquiry No:</strong></td>
                <td style="padding: 10px; border: 1px solid #ddd;">{{ $enquiry->enquiry_no }}</td>
            </tr>
            <tr>
                <td style="padding: 10px; border: 1px solid #ddd; background-color: #f9f9f9;"><strong>Customer Name:</strong></td>
                <td style="padding: 10px; border: 1px solid #ddd;">{{ $enquiry->user_name }}</td>
            </tr>
            <tr>
                <td style="padding: 10px; border: 1px solid #ddd; background-color: #f9f9f9;"><strong>Customer Email:</strong></td>
                <td style="padding: 10px; border: 1px solid #ddd;">{{ $enquiry->user_email }}</td>
            </tr>
            <tr>
                <td style="padding: 10px; border: 1px solid #ddd; background-color: #f9f9f9;"><strong>Customer Phone:</strong></td>
                <td style="padding: 10px; border: 1px solid #ddd;">{{ $enquiry->user_mobile }}</td>
            </tr>
            <tr>
                <td style="padding: 10px; border: 1px solid #ddd; background-color: #f9f9f9;"><strong>Preferred Contact:</strong></td>
                <td style="padding: 10px; border: 1px solid #ddd; text-transform: capitalize;">{{ str_replace('_product_page', '', $enquiry->source) }}</td>
            </tr>
            <tr>
                <td style="padding: 10px; border: 1px solid #ddd; background-color: #f9f9f9;"><strong>Message:</strong></td>
                <td style="padding: 10px; border: 1px solid #ddd;">{{ $enquiry->message }}</td>
            </tr>
        </table>
        
        <p style="margin-top: 30px;">
            <a href="{{ url('/seller/enquiries') }}" style="display: inline-block; padding: 10px 20px; background-color: #004392; color: white; text-decoration: none; border-radius: 5px;">View Enquiry in Dashboard</a>
        </p>
        
        <p style="margin-top: 30px; font-size: 12px; color: #888;">
            This is an automated email from {{ config('app.name', 'vShop') }}.
        </p>
    </div>
</body>
</html>
