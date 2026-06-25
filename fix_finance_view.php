<?php
$file = 'c:\\xampp\\htdocs\\vshop.zelofx.com\\resources\\views\\admin\\finance-management.blade.php';
$content = file_get_contents($file);

// Fix 1: Add Countries List
$countriesList = "\n    \$countriesListArray = ['India', 'USA', 'UK', 'Australia', 'Canada', 'Germany', 'France', 'Japan', 'China', 'Brazil', 'UAE', 'Singapore', 'Others'];\n";
$content = preg_replace('/(\$revenueData = \[.*?\];)/s', "$1$countriesList", $content);

// Fix 1.1: Replace Region Text Input with Select (Table 1 - Existing Rows)
$oldInput1 = '<input type="text" class="form-control form-control-sm" name="tax_regions[{{ $index }}][region]" value="{{ $tr[\'region\'] }}" required>';
$newInput1 = '<select class="form-select form-select-sm" name="tax_regions[{{ $index }}][region]" required><option value="">Select Country</option>@foreach($countriesListArray as $c)<option value="{{ $c }}" {{ $tr[\'region\'] == $c ? \'selected\' : \'\' }}>{{ $c }}</option>@endforeach</select>';
$content = str_replace($oldInput1, $newInput1, $content);

// Fix 1.2: Replace Region Text Input with Select (Table 1 - Empty Default Row)
$oldInput2 = '<input type="text" class="form-control form-control-sm" name="tax_regions[0][region]" placeholder="e.g., India" required>';
$newInput2 = '<select class="form-select form-select-sm" name="tax_regions[0][region]" required><option value="">Select Country</option>@foreach($countriesListArray as $c)<option value="{{ $c }}">{{ $c }}</option>@endforeach</select>';
$content = str_replace($oldInput2, $newInput2, $content);

// Fix 1.3: Replace Region Text Input with Select (Table 2 - Existing Rows)
$oldInput3 = '<input type="text" class="form-control form-control-sm region-name-field2" value="{{ $tr[\'region\'] }}" required>';
$newInput3 = '<select class="form-select form-select-sm region-name-field2" required><option value="">Select Country</option>@foreach($countriesListArray as $c)<option value="{{ $c }}" {{ $tr[\'region\'] == $c ? \'selected\' : \'\' }}>{{ $c }}</option>@endforeach</select>';
$content = str_replace($oldInput3, $newInput3, $content);

// Fix 1.4: Replace JS Template Region text input with select
$oldJSInput = '<input type="text" class="form-control form-control-sm" name="tax_regions[${rowCount}][region]" placeholder="Enter region" required>';
$newJSInput = '<select class="form-select form-select-sm" name="tax_regions[${rowCount}][region]" required><option value="">Select Country</option><option value="India">India</option><option value="USA">USA</option><option value="UK">UK</option><option value="Australia">Australia</option><option value="Canada">Canada</option><option value="Germany">Germany</option><option value="France">France</option><option value="Japan">Japan</option><option value="China">China</option><option value="Brazil">Brazil</option><option value="UAE">UAE</option><option value="Singapore">Singapore</option><option value="Others">Others</option></select>';
$content = str_replace($oldJSInput, $newJSInput, $content);

// Fix 2: Boost checkbox disabled by default
$oldBoost = '<input type="checkbox" class="form-check-input boost-checkbox" name="rules[${index}][boost]"';
$newBoost = '<input type="checkbox" class="form-check-input boost-checkbox" name="rules[${index}][boost]" disabled';
$content = str_replace($oldBoost, $newBoost, $content);

// Fix 3: JS IDs for Invoice Generation
$content = str_replace('invoiceSelectSellerIdNew', 'invoiceSelectSellerIdMain', $content);
$content = str_replace('invoiceStartDateNew', 'invoiceStartDateMain', $content);
$content = str_replace('invoiceEndDateNew', 'invoiceEndDateMain', $content);
$content = str_replace('reportTypeNew', 'reportTypeMain', $content);

// Fix 4: Refund Analytics - dynamic variables injection
$content = str_replace('<h4 class="text-white mb-0 fw-bold">8.5%</h4>', '<h4 class="text-white mb-0 fw-bold">{{ number_format(($refundAmount > 0 && $totalRevenue > 0) ? ($refundAmount / $totalRevenue * 100) : 0, 1) }}%</h4>', $content);
$content = str_replace('<h4 class="text-white mb-0 fw-bold">$12.5k</h4>', '<h4 class="text-white mb-0 fw-bold">{{ $currencySymbol }}{{ number_format($refundAmount, 2) }}</h4>', $content);
$content = str_replace('<small class="text-white-50">128 txns</small>', '<small class="text-white-50">{{ \App\Models\SellerFinance::where(\'transaction_type\', \'refund\')->count() }} txns</small>', $content);
$content = str_replace('<h4 class="text-white mb-0 fw-bold">4.2d</h4>', '<h4 class="text-white mb-0 fw-bold">{{ number_format(\App\Models\Grievance::where(\'status\', \'close\')->avg(\'resolution_time_days\') ?? 0, 1) }}d</h4>', $content);
$content = str_replace('<h4 class="text-white mb-0 fw-bold">$3.2k</h4>', '<h4 class="text-white mb-0 fw-bold">{{ $currencySymbol }}{{ number_format(\App\Models\SellerFinance::where(\'transaction_type\', \'refund_recovered\')->sum(\'credit\'), 2) }}</h4>', $content);
$content = str_replace('<h4 class="text-white mb-0 fw-bold">$1.9k</h4>', '<h4 class="text-white mb-0 fw-bold">{{ $currencySymbol }}{{ number_format(\App\Models\SellerFinance::where(\'transaction_type\', \'commission_reversed\')->sum(\'debit\'), 2) }}</h4>', $content);
$content = str_replace('<h4 class="text-white mb-0 fw-bold">$340</h4>', '<h4 class="text-white mb-0 fw-bold">{{ $currencySymbol }}{{ number_format(\App\Models\SellerFinance::where(\'transaction_type\', \'lead_refund\')->sum(\'credit\'), 2) }}</h4>', $content);
$content = str_replace('<small class="text-white-50">12 leads</small>', '<small class="text-white-50">{{ \App\Models\SellerFinance::where(\'transaction_type\', \'lead_refund\')->count() }} leads</small>', $content);

file_put_contents($file, $content);
echo "Replacements applied successfully.\n";
