@props([
    'appName' => config('app.name'),
    'appUrl' => config('app.url'),
])
<table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="max-width: 600px; margin: 0 auto;">
    <tr>
        <td style="padding: 20px 0 0 0;">
            <p style="margin: 0; color: #666; font-size: 12px;">
                Regards,<br>
                <strong style="color: #174b79;">{{ $appName }}</strong>
            </p>
            <p style="margin: 12px 0 0 0; font-size: 11px; color: #999;">
                This is an automated message. Please do not reply directly to this email.
            </p>
            @if($appUrl)
            <p style="margin: 8px 0 0 0; font-size: 11px;">
                <a href="{{ $appUrl }}" style="color: #174b79;">Unsubscribe or manage preferences</a>
            </p>
            @endif
        </td>
    </tr>
</table>
