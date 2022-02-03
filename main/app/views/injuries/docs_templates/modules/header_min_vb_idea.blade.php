@if( Settings::get('idea_getin_activated') == 'enabled' )
    <div id="header" style="margin: auto 30px;">
        <table >
            <tr>
                <td style="text-align: left;">
                    <img src="templates-src/idea-getin-logo.png" style="height: 40px;"/>
                </td>
                <td style="text-align: right;">
                    <img src="templates-src/fpk.png" style="height: 40px;"/>
                </td>
            </tr>
        </table>
    </div>
@else
    <div id="header_basic">
        <table>
            <tr>
                <td></td>
                <td style="text-align: right;">
                    <img src="templates-src/idea-logo.png" alt="Logo" style="height: 40px;"/>
                </td>
            </tr>
        </table>
    </div>
@endif