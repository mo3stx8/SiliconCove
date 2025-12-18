@php
    use Illuminate\Support\Str;

    $colors = [
        'approved' => 'success',
        'pending' => 'warning text-dark',
        'in progress' => 'info',
        'delivered' => 'primary',
        'rejected' => 'danger',
        'cancelled' => 'secondary',
        'refunded' => 'dark',
        'refund_requested' => 'dark',
        'refund_rejected' => 'danger',
    ];
@endphp

<span class="badge bg-{{ $colors[$status] ?? 'secondary' }}">
    {{ Str::of($status)->replace('_', ' ')->title() }}
</span>
