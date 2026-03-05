@props([
    'appName' => config('app.name'),
    'setting' => null,
])
<table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="max-width: 600px; margin: 0 auto;">
    <tr>
        <td style="background-color: #174b79; padding: 20px 28px; border-radius: 8px 8px 0 0;">
            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                <tr>
                    <td style="vertical-align: middle;">
                        <x-logo :setting="$setting" maxHeight="44" />
                    </td>
                    <td style="text-align: right; vertical-align: middle;">
                        <span style="display: inline-block; background-color: #ffd600; color: #174b79; font-size: 11px; font-weight: 700; text-transform: uppercase; padding: 6px 12px; border-radius: 4px;">Notification</span>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td style="height: 4px; background-color: #ffd600; font-size: 0; line-height: 0;">&#8203;</td>
    </tr>
</table>
