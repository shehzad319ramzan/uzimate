@php
    $messageTail = $body ?? '';
    if (!empty($recipientName) && (stripos($messageTail, 'We miss you,') !== false || stripos($messageTail, 'we miss you,') !== false)) {
        $pos = strpos($messageTail, '!');
        $messageTail = $pos !== false ? trim(substr($messageTail, $pos + 1)) : $messageTail;
    }
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject ?? 'Notification' }}</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background-color: #f4f4f4; padding: 20px; border-radius: 5px;">
        <x-emails.header :setting="$setting ?? null" :appName="$appName ?? config('app.name')" />
        <div style="background-color: #fff; padding: 20px; border-radius: 5px; margin-top: 0;">
            @if(!empty($recipientName))
                <p style="margin: 0 0 8px 0; font-size: 15px; color: #666;">Hello</p>
                <h2 style="margin: 0 0 16px 0; font-size: 22px; color: #174b79;">We miss you, <strong>{{ $recipientName }}</strong>!</h2>
            @endif
            {!! nl2br(e($messageTail ?: $body ?? '')) !!}
            @if(!empty($recipientName) && ($appUrl ?? config('app.url')))
                <p style="margin-top: 20px;">
                    <a href="{{ $appUrl ?? config('app.url') }}" style="display: inline-block; padding: 10px 20px; background-color: #ffd600; color: #174b79; font-weight: 600; text-decoration: none; border-radius: 5px;">Visit us again</a>
                </p>
            @endif
        </div>
        <x-emails.footer :appName="$appName ?? config('app.name')" :appUrl="$appUrl ?? config('app.url')" />
    </div>
</body>
</html>
