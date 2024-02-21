{**
 * PVP price switcher
 *
 * @author    Reborn Media Studio <info@rebornmediastudio.com>
 * @copyright Reborn Media Studio 2024
 * @version   1.0.0
 *
 *}

{if $is_customer_b2b}
    <div id="price-toggle" class="price-switcher">
        <label class="switch">
            <input type="checkbox" id="price-switch">
            <span class="slider round"></span>
        </label>
        <span class="price-switcher-label">{l s='Show tax included'}</span>
    </div>
{/if}
