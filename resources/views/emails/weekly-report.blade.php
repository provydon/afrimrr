@component('mail::message')
# Spot the next big thing 🌍

The week's verified recurring revenue across African startups — **{{ $report['period_label'] }}**. Real MRR pulled from Paystack, Flutterwave and Stripe. No screenshots, no vanity metrics.

@component('mail::panel')
**{{ $report['stats']['verified_mrr_display'] }}/mo** in verified MRR across **{{ $report['stats']['verified_count'] }}** startups in **{{ $report['stats']['country_count'] }}** countries.
@endcomponent

## 🏆 Top earners

@component('mail::table')
| # | Startup | MRR | Where |
|:--|:--------|:----|:------|
@foreach ($report['top'] as $i => $s)
| {{ $i + 1 }} | [{{ $s['name'] }}]({{ $s['url'] }}){!! $s['is_verified'] ? ' ✅' : '' !!} | **{{ $s['mrr_compact'] }}** | {{ $s['country_label'] }} |
@endforeach
@endcomponent

@if (! empty($report['newcomers']))
## ✨ Just joined

These startups verified their revenue in the last 7 days — early movers worth a look.

@component('mail::table')
| Startup | MRR | Category |
|:--------|:----|:---------|
@foreach ($report['newcomers'] as $s)
| [{{ $s['name'] }}]({{ $s['url'] }}){!! $s['is_verified'] ? ' ✅' : '' !!} | **{{ $s['mrr_compact'] }}** | {{ $s['category_label'] }} |
@endforeach
@endcomponent
@endif

@if (! empty($report['growing']))
## 📈 Fastest growing

Biggest month-over-month MRR gains.

@component('mail::table')
| Startup | Growth | MRR |
|:--------|:-------|:----|
@foreach ($report['growing'] as $s)
| [{{ $s['name'] }}]({{ $s['url'] }}) | **{{ $s['mom_growth_display'] }}** | {{ $s['mrr_compact'] }} |
@endforeach
@endcomponent
@endif

@component('mail::button', ['url' => $browseUrl])
See the full leaderboard
@endcomponent

Thanks for following along,
The **{{ config('app.name') }}** team

@slot('subcopy')
You're receiving this because you subscribed to the AfricanMRR weekly report.
[Unsubscribe]({{ $unsubscribeUrl }}) anytime.

AfricanMRR is a product of Steven Malog, Inc.
@endslot
@endcomponent
